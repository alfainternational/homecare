<?php
namespace App\Http\Controllers;

use App\Models\Plan;

class LandingController extends Controller
{
    public function index(){
        $plans=Plan::where('is_active',true)->orderBy('price')->get();
        return view('landing.index',compact('plans'));
    }
    public function about(){return view('landing.about');}
    public function pricing(){
        $plans=Plan::where('is_active',true)->orderBy('price')->get();
        return view('landing.pricing',compact('plans'));
    }
    public function howItWorks(){return view('landing.how-it-works');}
}
