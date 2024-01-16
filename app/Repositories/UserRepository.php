<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository
{
    /**
     * 新規追加
     */
    public function store(array $params): int
    {
        $user = new User();
        $user->fill($params);
        $user->save();
        // 登録後のイベントを発行
        event(new Registered($user));
        return $user->id;
    }

    /**
     * IDで取得
     */
    public function getById(int $id): array
    {
        return User::find($id)->toArray();
    }

    /**
     * パスワードリセットのコールバックメソッドを取得
     */
    public function getPasswordResetCallback(): \Closure
    {
        return function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ]);
            $user->save();
            event(new PasswordReset($user));
        };
    }

}