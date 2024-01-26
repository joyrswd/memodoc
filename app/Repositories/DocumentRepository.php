<?php

namespace App\Repositories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;

class DocumentRepository
{
    /**
     * レコードを新規作成する
     */
    public function store(int $userId, int $jobId, string $title, string $content, array $memoIds): int
    {
        $document = new Document();
        $document->title = $title;
        $document->content = $content;
        $document->user_id = $userId;
        $document->api_job_id = $jobId;
        $document->save();
        $document->memos()->attach($memoIds);
        return $document->id;
    }

    /**
     * ユーザーIDに紐づくレコードを取得する
     */
    public function findByUserId(int $userId, array $params): Builder
    {
        $query = Document::query();
        $query->where('user_id', $userId);
        if (empty($params['title']) === false) {
            $query->where('title', 'like', '%' . $params['title'] . '%');
        }
        if (empty($params['content']) === false) {
            $query->where('content', 'like', '%' . $params['content'] . '%');
        }
        if (empty($data['from']) === false) {
            $query->where('created_at', '>=', $params['from'] . ' 00:00:00');
        }
        if (empty($data['to']) === false) {
            $query->where('created_at', '<=', $params['to'] . ' 23:59:59');
        }
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * ユーザーIDとIDに紐づくレコードを取得する
     */
    public function findByIdAndUserId(int $userId, int $documentId): array
    {
        $document = Document::whereUserId($userId)->whereId($documentId)->first();
        return empty($document) ? [] : $document->toArray();
    }

    /**
     * ユーザーIDとIDに紐づくレコードを削除する
     */
    public function deleteByUserIdAndId(int $userId, int $documentId): void
    {
        Document::whereUserId($userId)->whereId($documentId)->delete();
    }

    /**
     * ユーザーIDとIDに紐づくレコードを更新する
     */
    public function updateByUserIdAndId(int $userId, int $documentId, array $params): void
    {
        Document::whereUserId($userId)->whereId($documentId)->update($params);
    }
}
