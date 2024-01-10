<?php
namespace App\Repositories;

use App\Models\Memo;
use Illuminate\Database\Eloquent\Builder;

class MemoRepository
{
    /**
     * @param array<string, mixed> $data
     * @return int
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
     * 
     * @param int $userId
     * @param array<string, mixed> $data
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

    /**
     * 
     * @param int $userId
     * @param int $memoId
     * @return array<string, mixed>
     */
    public function findByIdAndUserId(int $userId, int $memoId): array
    {
        $memo = Memo::whereUserId($userId)->whereId($memoId)->firstOrFail();
        return array_merge($memo->toArray(), [
            'tags' => $memo->tags->pluck('name')->toArray(),
        ]);
    }

    /**
     * @param int $memoId
     */
    public function detachTags(int $memoId): void
    {
        Memo::find($memoId)->tags()->detach();
    }
}