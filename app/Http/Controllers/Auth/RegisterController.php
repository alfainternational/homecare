<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegister(){return view('auth.register');}
    public function register(Request $request){
        $data=$request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users',
            'phone'=>'required|string|max:20',
            'password'=>'required|min:8|confirmed',
        ]);
        $user=User::create([
            'name'=>$data['name'],'email'=>$data['email'],
            'phone'=>$data['phone'],'password'=>Hash::make($data['password']),'role'=>'client',
        ]);
        Wallet::create(['user_id'=>$user->id,'balance'=>0]);
        Auth::login($user);
        return redirect('/dashboard');
    }
}
