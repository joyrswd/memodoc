<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\LoginService;
use App\Services\UserService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class LoginController extends Controller
{

    private UserService $userService;
    private LoginService $loginService;

    public function __construct(UserService $userService, LoginService $loginService)
    {
        $this->userService = $userService;
        $this->loginService = $loginService;
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
        $result = $this->loginService->sendVerification(auth()->id());
        return $result ? back()->with('success', '認証メールを再送信しました。')
                        : back()->with(['failed' => 'メール認証メールの送信に失敗しました。']);
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
        $result = $this->loginService->sendResetPasswordLink($request->only('email'));
        return $result ? back()->with(['success' => 'パスワード再設定メールを送信しました。'])
            : back()->withErrors(['email' => [$this->loginService->getErrorMessage()]]);
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
        $result = $this->loginService->resetPassword($request->only('email', 'password', 'password_confirmation', 'token'));
        if ($result) {
            $userName = $this->userService->getUserNameByEmail($request->input('email'));
            return redirect()->route('home')->with('success', 'パスワードを再設定しました。')->withInput(['name' => $userName]);
        } else {
            $message = $this->loginService->getErrorMessage();
            return back()->withErrors(['email' => [$message]]);
        }
    }

    /**
     * アバウト画面表示
     */
    public function about()
    {
        return view('login.about');
    }
}
