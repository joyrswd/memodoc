<?php
namespace App\Repositories;

use App\Models\Memo;

class MemoRepository
{
    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data): int
    {
        $memo = new Memo();
        $memo->user_id = $data['user_id'];
        $memo->content = $data['content'];
        $memo->save();
        return $memo->id;
    }
}