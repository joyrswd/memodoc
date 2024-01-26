<?php

namespace App\Repositories;

use App\Models\Memo;
use Illuminate\Database\Eloquent\Builder;

class MemoRepository
{
    public function store(array $params): int
    {
        $memo = new Memo();
        $memo->user_id = $params['user_id'];
        $memo->content = $params['content'];
        $memo->save();
        return $memo->id;
    }

    public function findByUserId(int $userId, array $params): Builder
    {
        $query = Memo::whereUserId($userId);
        if (empty($params['content']) === false) {
            $query->where('content', 'like', '%' . $params['content'] . '%');
        }
        if (empty($params['from']) === false) {
            $query->where('created_at', '>=', $params['from'] . ' 00:00:00');
        }
        if (empty($params['to']) === false) {
            $query->where('created_at', '<=', $params['to'] . ' 23:59:59');
        }
        if (empty($params['tags']) === false) {
            $query->whereHas('tags', function ($query) use ($params) {
                $query->whereIn('name', $params['tags']);
            });
        }
        return $query->orderBy('created_at', 'desc');
    }

    public function findByIdAndUserId(int $userId, int $memoId): array
    {
        $memo = Memo::whereUserId($userId)->whereId($memoId)->first();
        return empty($memo) ? [] : array_merge($memo->toArray(), [
            'tags' => $memo->tags->pluck('name')->toArray(),
        ]);
    }

    public function detachTags(int $memoId): void
    {
        Memo::find($memoId)->tags()->detach();
    }

    public function deleteByIdAndUserId(int $userId, int $memoId): void
    {
        Memo::whereUserId($userId)->whereId($memoId)->delete();
    }
}
