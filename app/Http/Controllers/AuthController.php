<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = DB::table('tbl_user_accounts')
            ->where('email', $credentials['email'])
            ->first();

        if (!$user) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->withInput($request->only('email'));
        }

        // If status column exists, require active
        if (property_exists($user, 'status') && $user->status !== 'active') {
            return back()
                ->withErrors(['email' => 'Account is inactive.'])
                ->withInput($request->only('email'));
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();
        $request->session()->put('ascend_user_id', $user->IDUser);
        $request->session()->put('ascend_user_email', $user->email ?? null);
        $request->session()->put('ascend_user_role', $user->role ?? null);

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget([
            'ascend_user_id',
            'ascend_user_email',
            'ascend_user_role',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
