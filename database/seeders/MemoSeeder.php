<?php

namespace Database\Seeders;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class MemoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // メモを作成するユーザーを選択する(初期値は管理者)
        $user = $this->command->choice(
            'どのユーザーとしてメモを作成しますか？', [
                '管理者',
                '即席ユーザー',
            ], 0);
        if ($user === '管理者') {
            $userId = User::where('name', env('ADMIN_USER_NAME'))->first()->id;
        } elseif ($user === '即席ユーザー') {
            $userId = User::factory()->create()->id;
        } else {
            exit;
        }

        // いくつメモを作成するか尋ねる（1以上100以下の整数を受け付ける）
        $count = $this->command->ask('何個のメモを作成しますか？', 10);
        if (is_int((int) $count) === false) {
            $this->command->error('数字を入力してください');
            exit;
        } elseif ($count < 1) {
            $this->command->error('1以上の数字を入力してください');
            exit;
        } elseif ($count > 100) {
            $this->command->error('100以下の数字を入力してください');
            exit;
        }

        // メモを作成する
        Memo::factory(['user_id' => $userId])->count($count)->create();
        $this->command->info($count . '個のメモを作成しました');
    }
}
