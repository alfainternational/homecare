<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
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
            $query->where('category_id', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('name_ar', 'like', '%' . $request->search . '%');
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

    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('store.index')->with('error', 'السلة فارغة.');
        }

        // GET — show checkout page
        if ($request->isMethod('get')) {
            return view('store.checkout', ['cart' => $cart]);
        }

        // POST — process order
        $request->validate([
            'payment_method' => 'required|in:card,bank,tabby,tamara,wallet',
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:500',
        ]);

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
                $order->items()->create([
                    'product_id' => $item['id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['price'],
                ]);

                // Decrement stock
                Product::where('id', $item['id'])->decrement('stock', $item['quantity']);
            }

            // Wallet deduction
            if ($request->payment_method === 'wallet') {
                $user->wallet->debit($total, 'شراء من المتجر — طلب #' . $order->order_number);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('store.index')->with('success', 'تم إنشاء طلبك بنجاح! رقم الطلب: ' . $order->order_number);
    }
}
