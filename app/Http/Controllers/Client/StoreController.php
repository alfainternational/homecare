<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CheckoutRequest;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->filled('category')) {
            $cats = array_filter((array)$request->category);
            if (!empty($cats)) {
                $query->whereIn('category_id', $cats);
            }
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name_ar', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%")->orWhere('brand', 'like', "%{$s}%");
            });
        }
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $products = $query->paginate(12);
        $categories = Category::where('is_active', true)->get();

        return view('store.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();
        return view('store.show', compact('product', 'related'));
    }

    public function addToCart(Request $request, Product $product)
    {
        abort_if($product->stock <= 0, 422, 'هذا المنتج غير متوفر حالياً');

        $cart = session()->get('cart', []);
        $id = $product->id;
        $qty = ($cart[$id]['quantity'] ?? 0) + ($request->quantity ?? 1);

        $cart[$id] = [
            'id'       => $id,
            'name'     => $product->name_ar,
            'price'    => $product->price,
            'quantity' => min($qty, $product->stock),
            'image'    => $product->image,
            'brand'    => $product->brand,
        ];
        session()->put('cart', $cart);

        return back()->with('success', 'تمت إضافة المنتج إلى السلة.');
    }

    public function cart()
    {
        return view('store.cart', ['cart' => session()->get('cart', [])]);
    }

    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);
        unset($cart[$id]);
        session()->put('cart', $cart);
        return back()->with('success', 'تم حذف المنتج من السلة.');
    }

    public function checkoutPage(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('store.index')->with('error', 'السلة فارغة.');
        }
        return view('store.checkout', ['cart' => $cart]);
    }

    public function checkout(CheckoutRequest $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('store.index')->with('error', 'السلة فارغة.');
        }

        $user = Auth::user();
        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $tax = round($subtotal * 0.15, 2);
        $total = $subtotal + $tax;

        // Wallet payment check
        if ($request->payment_method === 'wallet') {
            $wallet = $user->wallet;
            if (!$wallet || $wallet->balance < $total) {
                return back()->with('error', 'رصيد المحفظة غير كافٍ. الرصيد الحالي: ' . ($wallet->balance ?? 0) . ' ر.س');
            }
        }

        try {
        $order = DB::transaction(function () use ($cart, $user, $request, $subtotal, $tax, $total) {
            $order = Order::create([
                'user_id'        => $user->id,
                'subtotal'       => $subtotal,
                'tax'            => $tax,
                'shipping'       => 0,
                'discount'       => 0,
                'total'          => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'wallet' ? 'paid' : 'pending',
                'status'         => 'pending',
                'address'        => json_encode([
                    'name'    => $request->name,
                    'phone'   => $request->phone,
                    'address' => $request->address,
                ]),
            ]);

            foreach ($cart as $item) {
                // Pessimistic lock: prevent concurrent checkout from overselling
                $product = Product::lockForUpdate()->find($item['id']);

                if (!$product || $product->stock < $item['quantity']) {
                    throw new \RuntimeException(
                        "المنتج «{$item['name']}» لا يتوفر منه المخزون الكافي. المتبقي: " . ($product->stock ?? 0)
                    );
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['price'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            // Wallet deduction
            if ($request->payment_method === 'wallet') {
                $user->wallet->debit($total, 'شراء من المتجر — طلب #' . $order->order_number);
            }

            return $order;
        });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->forget('cart');

        return redirect()->route('store.index')->with('success', 'تم إنشاء طلبك بنجاح! رقم الطلب: ' . $order->order_number);
    }
}
