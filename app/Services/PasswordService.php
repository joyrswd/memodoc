<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Password;

class PasswordService
{
    private UserRepository $userRepository;
    private string $status;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function sendResetLink(array $params): bool
    {
        $this->status = Password::sendResetLink($params);
        return ($this->status === Password::RESET_LINK_SENT);
    }

    public function reset(array $params): bool
    {
        $this->status = Password::reset($params, $this->userRepository->getPasswordResetCallback());
        return ($this->status === Password::PASSWORD_RESET);
    }

    public function getMessage(): string
    {
        return __($this->status);
    }
}
