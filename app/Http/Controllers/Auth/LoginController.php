<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin(){return view('auth.login');}
    public function login(Request $request){
        $credentials=$request->validate(['email'=>'required|email','password'=>'required']);
        if(Auth::attempt($credentials,$request->boolean('remember'))){
            $request->session()->regenerate();
            return redirect()->intended($this->redirectTo());
        }
        return back()->withErrors(['email'=>'البريد الإلكتروني أو كلمة المرور غير صحيحة.'])->onlyInput('email');
    }
    protected function redirectTo():string{
        return match(Auth::user()->role){
            'admin','supervisor'=>'/admin',
            'technician'=>'/tech/dashboard',
            default=>'/dashboard',
        };
    }
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
