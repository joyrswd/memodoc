<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $params): void
    {
        $userId = $this->userRepository->store($params);
        auth()->loginUsingId($userId);
    }
}
