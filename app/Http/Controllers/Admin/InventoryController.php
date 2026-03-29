<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class InventoryController extends Controller
{
    public function index(){
        $products=Product::with('category')->latest()->paginate(20);
        $categories=Category::all();
        $lowStock=Product::where('stock','<',10)->count();
        $totalValue=Product::selectRaw('SUM(price * stock) as total')->value('total') ?? 0;
        return view('admin.inventory.index',compact('products','categories','lowStock','totalValue'));
    }
}
