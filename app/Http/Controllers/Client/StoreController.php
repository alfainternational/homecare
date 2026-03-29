<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request){
        $query=Product::with('category')->where('is_active',true);
        if($request->filled('category'))$query->where('category_id',$request->category);
        if($request->filled('search'))$query->where('name_ar','like','%'.$request->search.'%');
        if($request->boolean('in_stock'))$query->where('stock','>',0);
        $products=$query->paginate(12);
        $categories=Category::where('is_active',true)->get();
        return view('store.index',compact('products','categories'));
    }
    public function show(Product $product){
        $related=Product::where('category_id',$product->category_id)->where('id','!=',$product->id)->limit(4)->get();
        return view('store.show',compact('product','related'));
    }
    public function addToCart(Request $request,Product $product){
        $cart=session()->get('cart',[]);
        $id=$product->id;
        $cart[$id]=['id'=>$id,'name'=>$product->name_ar,'price'=>$product->price,'quantity'=>($cart[$id]['quantity']??0)+($request->quantity??1),'image'=>$product->image_url];
        session()->put('cart',$cart);
        return back()->with('success','تمت إضافة المنتج إلى السلة.');
    }
    public function cart(){return view('store.cart',['cart'=>session()->get('cart',[])]);}
    public function removeFromCart($id){
        $cart=session()->get('cart',[]);
        unset($cart[$id]);
        session()->put('cart',$cart);
        return back()->with('success','تم حذف المنتج.');
    }
    public function checkout(){return view('store.checkout',['cart'=>session()->get('cart',[])]);}
}
