<?php
namespace App\Repositories;

use App\Models\Memo;
use Illuminate\Database\Eloquent\Builder;

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

    /**
     * @return Builder<Memo>
     */
    public function findByUserId(int $userId, array $data): Builder
    {
        $query = Memo::whereUserId($userId);
        if (empty($data['content']) === false) {
            $query->where('content', 'like', '%' . $data['content'] . '%');
        }
        if (empty($data['from']) === false) {
            $query->where('created_at', '>=', $data['from'] . ' 00:00:00');
        }
        if (empty($data['to']) === false) {
            $query->where('created_at', '<=', $data['to'] . ' 23:59:59');
        }
        if (empty($data['tags']) === false) {
            $query->whereHas('tags', function ($query) use ($data) {
                $query->whereIn('name', $data['tags']);
            });
        }
        return $query->orderBy('created_at', 'desc');
    }
}