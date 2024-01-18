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

    public function bind(mixed $value): string
    {
        // $valueが正常なトークンかどうかをチェック
        $email = request()->query('email');
        if($this->userRepository->checkTokenWithEmail($email, $value)){
            return $value;
        }
        abort(404);
    }

    public function sendResetPasswordLink(array $params): bool
    {
        return $this->userRepository->sendResetPasswordLink($params);
    }

    public function resetPassword(array $params): bool
    {
        return $this->userRepository->resetPassword($params);
    }

    public function getErrorMessage(): string
    {
        $status = $this->userRepository->getPasswordResetStatus();
        return __($status);
    }

    public function sendVerification(int $userId): bool
    {
        return $this->userRepository->sendVerification($userId);
    }

}
