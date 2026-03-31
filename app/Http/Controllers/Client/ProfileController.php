<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user      = Auth::user();
        $addresses = $user->addresses()->orderByDesc('is_primary')->get();
        return view('client.profile', compact('user', 'addresses'));
    }

    public function update(UpdateProfileRequest $request)
    {
        Auth::user()->update($request->validated());
        return back()->with('success', 'تم تحديث بياناتك بنجاح.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()],
        ], [
            'password.min'      => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed'=> 'كلمة المرور غير متطابقة.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'تم تغيير كلمة المرور بنجاح.');
    }
}
