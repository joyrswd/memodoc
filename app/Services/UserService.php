<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;

class UserService
{
    public function register(array $params): void
    {
        $user = new User();
        $user->fill($params);
        $user->save();
        // 登録後認証用メールを送信
        event(new Registered($user));
        // ログイン
        auth()->login($user);
    }
}
