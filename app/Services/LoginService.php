<?php

namespace App\Services;

use App\Repositories\UserRepository;

class LoginService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {token}のバリデーションルール
     * @see \App\Providers\RouteServiceProvider::boot()
     */
    public function bind(mixed $value): string
    {
        // $valueが正常なトークンかどうかをチェック
        $email = request()->query('email');
        if($this->userRepository->checkTokenWithEmail($email, $value)) {
            return $value;
        }
        abort(404);
    }

    /**
     * パスワード再設定リンクのメール送信
     */
    public function sendResetPasswordLink(array $params): bool
    {
        return $this->userRepository->sendResetPasswordLink($params);
    }

    /**
     * パスワード再設定
     */
    public function resetPassword(array $params): bool
    {
        return $this->userRepository->resetPassword($params);
    }

    /**
     * パスワード再設定処理結果のメッセージを取得
     */
    public function getErrorMessage(): string
    {
        $status = $this->userRepository->getPasswordResetStatus();
        return __($status);
    }

    /**
     * メールアドレスの確認メール送信
     */
    public function sendVerification(int $userId): bool
    {
        return $this->userRepository->sendVerification($userId);
    }

}
