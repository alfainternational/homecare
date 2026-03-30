<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = $user->wallet;
        $transactions = $wallet ? $wallet->transactions()->latest()->paginate(15) : collect();
        $referrals = Referral::where('referrer_id', $user->id)->with('referred')->latest()->get();
        $referralCode = route('register', ['ref' => $user->id]);

        return view('client.wallet', compact('user', 'wallet', 'transactions', 'referrals', 'referralCode'));
    }

    public function generateReferralLink()
    {
        return back()->with('success', 'تم نسخ رابط الإحالة بنجاح!');
    }
}
