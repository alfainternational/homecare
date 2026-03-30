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
        $stats=[
            'total'        => Product::count(),
            'out_of_stock' => Product::where('stock',0)->count(),
            'low_stock'    => Product::where('stock','>',0)->where('stock','<',10)->count(),
            'total_value'  => Product::selectRaw('SUM(price * stock) as total')->value('total') ?? 0,
        ];
        return view('admin.inventory.index',compact('products','categories','stats'));
    }
}
