<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    // ログイン処理
    public function login(LoginRequest $request)
    {
        // ログイン処理
        $credentials = $request->only('name', 'password');
        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('memo.create'));
        }
        
        return back()->withErrors([
            'name' => __('auth.failed'),
            'password' => __('auth.failed'),
        ])->withInput();
    }

    // ログアウト処理
    public function logout()
    {
        auth()->logout();
        return redirect()->route('home')->with('success', __('auth.logout'));
    }
}
