<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegister(Request $request){
        $refCode = $request->query('ref');
        return view('auth.register', compact('refCode'));
    }

    public function register(Request $request){
        $data=$request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users',
            'phone'=>'required|string|max:20',
            'password'=>'required|min:8|confirmed',
            'ref_code'=>'nullable|string',
        ]);

        $user = DB::transaction(function() use ($data) {
            $newUser = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'password' => Hash::make($data['password']),
                'role'     => 'client',
            ]);
            $wallet = Wallet::create(['user_id' => $newUser->id, 'balance' => 0]);

            // Process referral if code provided
            if (!empty($data['ref_code'])) {
                $referrer = User::find((int)$data['ref_code']);
                if ($referrer && $referrer->id !== $newUser->id && $referrer->role === 'client') {
                    $rewardAmount = 50.00;
                    Referral::create([
                        'referrer_id'   => $referrer->id,
                        'referred_id'   => $newUser->id,
                        'reward_amount' => $rewardAmount,
                        'status'        => 'rewarded',
                        'rewarded_at'   => now(),
                    ]);
                    // Credit referrer wallet
                    if ($referrer->wallet) {
                        $referrer->wallet->credit($rewardAmount, 'مكافأة إحالة — تسجيل ' . $newUser->name);
                    }
                    // Credit new user welcome bonus
                    $wallet->credit(25.00, 'مكافأة ترحيب بالتسجيل عبر رابط إحالة');
                }
            }

            return $newUser;
        });

        Auth::login($user);
        return redirect('/dashboard');
    }
}
