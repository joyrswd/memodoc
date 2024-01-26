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

    /**
     * ユーザー登録
     */
    public function register(array $params): void
    {
        $userId = $this->userRepository->store($params);
        auth()->loginUsingId($userId);
    }

    /**
     * メールアドレスからユーザー情報の取得
     */
    public function getUserNameByEmail(string $email): ?string
    {
        $user = $this->userRepository->findByEmail($email);
        return $user['name'] ?? null;
    }

    /**
     * ユーザーIDからタグ一覧を取得
     */
    public function getTags(int $userId): array
    {
        return $this->userRepository->getTags($userId);
    }

}
