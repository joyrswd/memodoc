<?php

namespace App\Services;

use App\Repositories\ApiJobRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Foundation\Bus\Dispatchable;

class ApiJobService
{
    private ApiJobRepository $apiJobRepository;

    public function __construct(ApiJobRepository $apiJobRepository)
    {
        $this->apiJobRepository = $apiJobRepository;
    }

    public function bind(mixed $value): int
    {
        $int = filter_var($value, FILTER_VALIDATE_INT);
        if (is_int($int) && $this->getApiJob(auth()->id(), $int)) {
            return $int;
        }
        abort(404);
        return 1;
    }

    public function prepare(int $userId, array $memos, \Closure $dispather): bool
    {
        $result = true;
        $jobId = $this->apiJobRepository->store($userId, $memos);
        try {
            $dispather($userId, $jobId, $this);
            $this->queue($jobId);
        } catch (\Throwable $e) {
            $this->exception($jobId, 'ジョブの登録に失敗しました。', $e->getMessage());
            $result = false;
        }
        return $result;
    }

    public function queue(int $id): void
    {
        $this->apiJobRepository->update($id, ['status' => ApiJobRepository::STATUS_WAITING]);
    }

    public function process(int $id, string $apiName): void
    {
        Log::info("ジョブID:{$id} APIリクエストを送信します。");
        $this->apiJobRepository->update($id, ['status' => ApiJobRepository::STATUS_PROCESSING, 'api_name' => $apiName]);
    }

    public function error(int $id, string $message, array $result): void
    {
        $response = $this->apiJobRepository->encodeResponse($result);
        Log::error("ジョブID:{$id} {$message}  \n  {$response}");
        $this->apiJobRepository->update($id, ['status' => ApiJobRepository::STATUS_ERROR, 'response' => $response, 'error_message' => $message]);
    }

    public function complete(int $id, array $result): void
    {
        Log::info("ジョブID:{$id} APIリクエストが完了しました。");
        $response = $this->apiJobRepository->encodeResponse($result);
        $this->apiJobRepository->update($id, ['status' => ApiJobRepository::STATUS_PROCESSED, 'response' => $response]);
    }

    public function success(int $id): void
    {
        $this->apiJobRepository->update($id, ['status' => ApiJobRepository::STATUS_SUCCESS]);
    }

    public function exception(int $id, string $message, string $detail = ''): void
    {
        Log::error("ジョブID:{$id} {$message}  \n  {$detail}");
        $this->apiJobRepository->update($id, ['status' => ApiJobRepository::STATUS_ABORTED, 'response' => $detail, 'error_message' => $message]);
    }

    /**
     * 1日のリクエスト回数制限を超えていないか（0の場合は無制限）
     */
    public function underDailyLimit(int $limit): bool
    {
        return ($limit === 0 || $this->apiJobRepository->countToday() < $limit);
    }

    public function getMemoContents(int $id): array
    {
        return $this->apiJobRepository->getMemoContents($id);
    }

    public function getMemoIds(int $id): array
    {
        return $this->apiJobRepository->getMemoIds($id);
    }

    public function getUpcomingCount(int $userId): int
    {
        $statuses = $this->apiJobRepository->getUpcomingStatuses();
        $upcomings = $this->apiJobRepository->findByUserIdAndStatus($userId, $statuses);
        return count($upcomings);
    }

    public function getApiJobs(int $userId, array $params): array
    {
        $pagination = $this->apiJobRepository->findByUserId($userId, $params)->paginate(10);
        foreach ($pagination->items() as $item) {
            $item->datetime = $item->created_at->format('Y-m-d H:i');
            $item->deletable = $this->isDeletable($item->status);
            $item->regeneratable = $this->isRegeneratable($item->status);
            $item->listTitle = empty($item->document) ? '（無題）' : Str::limit($item->document->title, 30, '...');
            $item->docId = empty($item->document) ? null : $item->document->id;
            $item->parts = $item->memos->pluck('id');
        };
        $data = $pagination->toArray();
        $data['navigation'] = $pagination->withQueryString()->links('pagination::bootstrap-5');
        return $data;
    }

    public function getStatuses(): array
    {
        return $this->apiJobRepository->getStatuses();
    }

    public function isDeletable(string $status): bool
    {
        $statuses = $this->apiJobRepository->getDeletableStatuses();
        return in_array($status, $statuses);
    }

    public function isRegeneratable(string $status): bool
    {
        $statuses = $this->apiJobRepository->getRegeneratableStatuses();
        return in_array($status, $statuses);
    }

    public function getApiJob(int $userId, int $id): ?array
    {
        return $this->apiJobRepository->findByIdAndUserId($userId, $id);
    }

    public function deleteApiJob(int $userId, int $id): void
    {
        $this->apiJobRepository->deleteByIdAndUserId($userId, $id);
    }
}
