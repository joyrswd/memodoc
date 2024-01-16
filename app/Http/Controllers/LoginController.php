<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Services\PasswordService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class LoginController extends Controller
{

    private PasswordService $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

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

    /**
     * メール認証案内表示
     */
    public function emailNotice()
    {
        return view('login.email_notice');
    }

    /**
     * メール認証
     */
    public function emailVerify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->route('memo.create')->with('success', 'メール認証が完了しました。');
    }

    /**
     * メール認証メール再送信
     */
    public function emailResend()
    {
        auth()->user()->sendEmailVerificationNotification();
        return back()->with('success', '認証メールを再送信しました。');
    }

    /**
     * パスワードリセット
     */
    public function passwordRequest()
    {
        return view('login.password_request');
    }

    /**
     * パスワードリセットメール送信
     */
    public function passwordEmail(LoginRequest $request)
    {
        $result = $this->passwordService->sendResetLink($request->only('email'));
        return $result ? back()->with(['success' => 'パスワードリセットメールを送信しました。'])
            : back()->withErrors(['email' => [$this->passwordService->getMessage()]]);
    }

    /**
     * パスワードリセット画面表示
     */
    public function passwordReset(string $token)
    {
        return view('login.password_reset', ['token' => $token]);
    }

    /**
     * パスワードリセット処理
     */
    public function passwordUpdate(LoginRequest $request)
    {
        $result = $this->passwordService->reset($request->only('email', 'password', 'password_confirmation', 'token'));
        return $result ? redirect()->route('home')->with('success', 'パスワードを再設定しました。')
            : back()->withErrors(['email' => [$this->passwordService->getMessage()]]);
    }
}
