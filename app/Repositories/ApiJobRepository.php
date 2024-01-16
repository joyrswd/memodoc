<?php
namespace App\Repositories;

use App\Models\ApiJob;
use Illuminate\Database\Eloquent\Builder;

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
        $apiJob->status = self::STATUS_STARTED;
        $apiJob->save();
        foreach ($memos as $index => $memo) {
            $apiJob->memos()->attach($memo, ['order' => $index+1]);
        }
        return $apiJob->id;
    }

    /**
     * apiJobのレコードを更新する
     */
    public function update(int $id, array $params): void
    {
        $apiJob = ApiJob::withTrashed()->find($id);
        if (empty($apiJob) === false && empty($params) === false) {
            foreach ($params as $key => $value) {
                $apiJob->$key = $value;
            }
            $apiJob->save();
        }
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
        return empty($apiJob) ? [] : $apiJob->memos()->pluck('content')->toArray();
    }

    /**
     * apiJobに関連するメモのIDを取得する
     */
    public function getMemoIds(int $id): array
    {
        $apiJob = ApiJob::find($id);
        return empty($apiJob) ? [] : $apiJob->memos()->pluck('memo_id')->toArray();
    }

    /**
     * ユーザーIDに紐づく[waiting, processing, processed]のapiJobのレコードを取得する
     */
    public function findByUserIdAndStatus(int $userId, array $status): array
    {
        return ApiJob::whereUserId($userId)
            ->whereIn('status', $status)
            ->get()
            ->toArray();
    }

    /**
     * ユーザーIDに紐づくapiJobのレコードを取得する
     */
    public function findByUserId(int $userId, array $params): Builder
    {
        $query = ApiJob::whereUserId($userId);
        if (empty($params['status']) === false) {
            $query->whereIn('status', $params['status']);
        }
        if (empty($params['from']) === false) {
            $query->where('created_at', '>=', $params['from'] . ' 00:00:00');
        }
        if (empty($params['to']) === false) {
            $query->where('created_at', '<=', $params['to'] . ' 23:59:59');
        }
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * ユーザーIDとIDに紐づくapiJobのレコードを取得する
     */
    public function findByIdAndUserId(int $userId, int $id): ?array
    {
        $apiJob = ApiJob::whereUserId($userId)->whereId($id)->first();
        return empty($apiJob) ? [] : $apiJob->toArray();
    }

    /**
     * ユーザーIDとIDに紐づくapiJobのレコードを削除する
     */
    public function deleteByIdAndUserId(int $userId, int $id): void
    {
        ApiJob::whereUserId($userId)->whereId($id)->delete();
    }

    /**
     * ステータスのリストを取得する
     */
    public function getStatuses(): array
    {
        return [
            self::STATUS_STARTED,
            self::STATUS_WAITING,
            self::STATUS_PROCESSING,
            self::STATUS_PROCESSED,
            self::STATUS_SUCCESS,
            self::STATUS_ERROR,
            self::STATUS_ABORTED,
        ];
    }

    /**
     * これから生成される文書のステータスを取得する
     */
    public function getUpcomingStatuses(): array
    {
        return [self::STATUS_WAITING, self::STATUS_PROCESSING];
    }

    /**
     * 削除可能なステータスを取得する
     */
    public function getDeletableStatuses(): array
    {
        return [self::STATUS_STARTED, self::STATUS_WAITING, self::STATUS_ERROR, self::STATUS_ABORTED];
    }

    /**
     * 再生成可能なステータスを取得する
     */
    public function getRegeneratableStatuses(): array
    {
        return [self::STATUS_ABORTED];
    }

}
