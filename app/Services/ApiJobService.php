<?php

namespace App\Services;

use App\Enums\ApiJobStatus;
use App\Repositories\ApiJobRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiJobService
{
    private ApiJobRepository $apiJobRepository;

    public function __construct(ApiJobRepository $apiJobRepository)
    {
        $this->apiJobRepository = $apiJobRepository;
    }

    /**
     * {job}のバリデーションルール
     * @see \App\Providers\RouteServiceProvider::boot()
     */
    public function bind(mixed $value): int
    {
        $int = filter_var($value, FILTER_VALIDATE_INT);
        if (is_int($int) && $this->getApiJob(auth()->id(), $int)) {
            return $int;
        }
        abort(404);
    }

    /**
     * ジョブの登録
     */
    public function prepare(int $userId, array $memos, \Closure $dispather): bool
    {
        $result = true;
        $jobId = $this->apiJobRepository->store($userId, $memos, ApiJobStatus::Started->value);
        try {
            $dispather($userId, $jobId, $this);
            $this->queue($jobId);
        } catch (\Throwable $e) {
            $this->exception($jobId, 'ジョブの登録に失敗しました。', $e->getMessage());
            $result = false;
        }
        return $result;
    }

    /**
     * ジョブがキューに入ったことを記録
     */
    public function queue(int $id): void
    {
        $this->apiJobRepository->update($id, ['status' => ApiJobStatus::Waiting->value]);
    }

    /**
     * ジョブのAPI処理中を記録
     */
    public function process(int $id, string $apiName): void
    {
        Log::info("ジョブID:{$id} APIリクエストを送信します。");
        $this->apiJobRepository->update($id, ['status' => ApiJobStatus::Processing->value, 'api_name' => $apiName]);
    }

    /**
     * ジョブのAPI処理エラーを記録
     */
    public function error(int $id, string $message, array $result): void
    {
        $response = $this->apiJobRepository->encodeResponse($result);
        Log::error("ジョブID:{$id} {$message}  \n  {$response}");
        $this->apiJobRepository->update($id, ['status' => ApiJobStatus::Error->value, 'response' => $response, 'error_message' => $message]);
    }

    /**
     * ジョブのAPI処理終了を記録
     */
    public function complete(int $id, array $result): void
    {
        Log::info("ジョブID:{$id} APIリクエストが完了しました。");
        $response = $this->apiJobRepository->encodeResponse($result);
        $this->apiJobRepository->update($id, ['status' => ApiJobStatus::Processed->value, 'response' => $response]);
    }

    /**
     * ジョブの全体処理完了を記録
     */
    public function success(int $id): void
    {
        $this->apiJobRepository->update($id, ['status' => ApiJobStatus::Success->value]);
    }

    /**
     * ジョブの処理中に例外発生を記録
     */
    public function exception(int $id, string $message, string $detail = ''): void
    {
        Log::error("ジョブID:{$id} {$message}  \n  {$detail}");
        $this->apiJobRepository->update($id, ['status' => ApiJobStatus::Aborted->value, 'response' => $detail, 'error_message' => $message]);
    }

    /**
     * 1日のリクエスト回数制限を超えていないか（0の場合は無制限）
     */
    public function underDailyLimit(int $limit): bool
    {
        return ($limit === 0 || $this->apiJobRepository->countToday() < $limit);
    }

    /**
     * APIジョブに紐づくメモの内容を全て取得
     */
    public function getMemoContents(int $id): array
    {
        return $this->apiJobRepository->getMemoContents($id);
    }

    /**
     * APIジョブに紐づくメモのIDを全て取得
     */
    public function getMemoIds(int $id): array
    {
        return $this->apiJobRepository->getMemoIds($id);
    }

    /**
     * 処理予定のAPIジョブの数を取得
     */
    public function getUpcomingCount(int $userId): int
    {
        $statuses = ApiJobStatus::getUpcoming();
        $upcomings = $this->apiJobRepository->findByUserIdAndStatus($userId, $statuses);
        return count($upcomings);
    }

    /**
     * 指定したAPIジョブIDがユーザーIDに紐づく場合データを取得
     */
    public function getApiJob(int $userId, int $id): ?array
    {
        return $this->apiJobRepository->findByIdAndUserId($userId, $id);
    }

    /**
     * ユーザーIDに紐づくAPIジョブの一覧を取得
     */
    public function getApiJobs(int $userId, array $params): array
    {
        $pagination = $this->apiJobRepository->findByUserId($userId, $params)->paginate(10);
        foreach ($pagination->items() as $item) {
            // viewで使用するためのデータを追加
            $item->datetime = $item->created_at->format('Y-m-d H:i');
            $item->deletable = ApiJobStatus::isDeletable($item->status);
            $item->regeneratable = $this->isRegeneratable($item->status);
            $item->listTitle = empty($item->document) ? '（無題）' : Str::limit($item->document->title, 30, '...');
            $item->docId = empty($item->document) ? null : $item->document->id;
            $item->parts = $item->memos->pluck('id');
        };
        $data = $pagination->toArray();
        $data['navigation'] = $pagination->withQueryString()->links('pagination::bootstrap-5');
        return $data;
    }

    /**
     * api_jobsテーブルのstatusカラムの値を全て取得
     */
    public function getStatuses(): array
    {
        return ApiJobStatus::getAll();
    }

    /**
     * 指定したstatusがジョブの再生成可能かどうかを判定
     */
    public function isRegeneratable(string $status): bool
    {
        return ApiJobStatus::isRegeneratable($status);
    }

    /**
     * 指定したAPIジョブIDがユーザーIDに紐づく場合データを削除する
     */
    public function deleteApiJob(int $userId, int $id): void
    {
        $this->apiJobRepository->deleteByIdAndUserId($userId, $id);
    }
}
