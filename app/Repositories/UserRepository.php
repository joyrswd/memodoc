<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class UserRepository
{
    private string $passwordResetStatus;
    /**
     * 新規追加
     */
    public function store(array $params): int
    {
        $user = new User();
        $user->fill($params);
        $user->save();
        try {
            // 登録後のイベントを発行
            event(new Registered($user));
        } catch (\Throwable $e) {
            // エラー時はログ出力
            Log::error($e->getMessage());
        }
        return $user->id;
    }

    /**
     * メールアドレスからユーザー名を取得
     */
    public function findByEmail(string $email): array
    {
        $user = User::where('email', $email)->first();
        if ($user === null) {
            return [];
        }
        return $user->toArray();
    }

    /**
     * トークンとメールアドレスの組み合わせが正しいかチェック
     */
    public function checkTokenWithEmail(string $email, string $token): bool
    {
        $user = User::where('email', $email)->first();
        if ($user === null) {
            return false;
        }
        return Password::tokenExists($user, $token);
    }

    /**
     * パスワードリセットメール送信
     */
    public function sendResetPasswordLink(array $params): string
    {
        $this->passwordResetStatus = Password::sendResetLink($params);
        return ($this->passwordResetStatus === Password::RESET_LINK_SENT);
    }

    /**
     * パスワードリセット
     */
    public function resetPassword(array $params): bool
    {
        $this->passwordResetStatus = Password::reset($params, function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ]);
            $user->save();
            try {
                // パスワードリセット後のイベントを発行
                event(new PasswordReset($user));
            } catch (\Throwable $e) {
                // エラー時はログ出力
                Log::error($e->getMessage());
            }
        });
        return ($this->passwordResetStatus === Password::PASSWORD_RESET);
    }

    /**
     * パスワードリセット処理結果
     */
    public function getPasswordResetStatus(): string
    {
        return $this->passwordResetStatus;
    }

    /**
     * メール認証メール送信
     */
    public function sendVerification(int $id): bool
    {
        $user = User::find($id);
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * タグ一覧取得
     */
    public function getTags(int $userId): array
    {
        $user = User::find($userId);
        if ($user === null) {
            return [];
        }
        return optional($user->memos)->pluck('tags')->flatten()->pluck('name', 'id')->unique()->toArray() ?? [];
    }
}