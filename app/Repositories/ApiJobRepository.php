<?php
namespace App\Repositories;

use App\Models\ApiJob;

class ApiJobRepository
{
    const STATUS_STARTED = 'started';
    const STATUS_WAITING = 'waiting';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PROCESSED = 'processed';
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';
    const STATUS_ABORTED = 'aborted';

    /**
     * apiJobのレコードを作成する
     */
    public function store(int $userId, array $memos): int
    {
        $apiJob = new ApiJob();
        $apiJob->user_id = $userId;
        $apiJob->save();
        foreach ($memos as $index => $memo) {
            $apiJob->memos()->attach($memo, ['order' => $index+1]);
        }
        return $apiJob->id;
    }

    /**
     * apiJobのレコードを更新する
     */
    public function updateStatus(int $id, string $status, array $result = []): void
    {
        $apiJob = ApiJob::find($id);
        $apiJob->status = $status;
        if (empty($result) === false) {
            foreach ($result as $key => $value) {
                $apiJob->$key = $value;
            }
        }
        $apiJob->save();
    }

    /**
     * APIのレスポンスをJSON形式にエンコードする
     */
    public function encodeResponse(array $response): string
    {
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 今日の日付のAPI実行数をカウントする
     */
    public function countToday(): int
    {
        return ApiJob::whereDate('started_at', now())->count();
    }

    /**
     * apiJobに関連するメモの内容を取得する
     */
    public function getMemoContents(int $id): array
    {
        $apiJob = ApiJob::find($id);
        return $apiJob->memos()->pluck('content')->toArray();
    }

    /**
     * apiJobに関連するメモのIDを取得する
     */
    public function getMemoIds(int $id): array
    {
        $apiJob = ApiJob::find($id);
        return $apiJob->memos()->pluck('memo_id')->toArray();
    }
}