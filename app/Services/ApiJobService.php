<?php

namespace App\Services;

use App\Repositories\ApiJobRepository;
use Illuminate\Support\Facades\Log;

class ApiJobService
{
    private $apiJobRepository;

    public function __construct(ApiJobRepository $apiJobRepository)
    {
        $this->apiJobRepository = $apiJobRepository;
    }

    public function prepare(int $userId, array $memos): int
    {
        return $this->apiJobRepository->store($userId, $memos);
    }

    public function queue(int $id): void
    {
        $this->apiJobRepository->updateStatus($id, ApiJobRepository::STATUS_WAITING);
    }

    public function process(int $id, string $apiName): void
    {
        $this->apiJobRepository->updateStatus($id, ApiJobRepository::STATUS_PROCESSING, ['api_name' => $apiName]);
    }

    public function error(int $id, array $result): void
    {
        Log::error('APIのリクエストに失敗しました');
        $response = $this->apiJobRepository->encodeResponse($result);
        $this->apiJobRepository->updateStatus($id, ApiJobRepository::STATUS_ERROR, ['response' => $response]);
    }

    public function complete(int $id, array $result): void
    {
        $response = $this->apiJobRepository->encodeResponse($result);
        $this->apiJobRepository->updateStatus($id, ApiJobRepository::STATUS_PROCESSED, ['response' => $response]);
    }

    public function success(int $id): void
    {
        $this->apiJobRepository->updateStatus($id, ApiJobRepository::STATUS_SUCCESS);
    }

    public function exception(int $id, string $log, string $message = null): void
    {
        Log::error($log);
        $response = empty($message) ? $log : $message;
        $this->apiJobRepository->updateStatus($id, ApiJobRepository::STATUS_ABORTED, ['response' => $response]);
    }

    public function underDailyLimit(int $limit): bool
    {
        return $this->apiJobRepository->countToday() < $limit;
    }

    public function getMemoContents(int $id): array
    {
        return $this->apiJobRepository->getMemoContents($id);
    }

    public function getMemoIds(int $id): array
    {
        return $this->apiJobRepository->getMemoIds($id);
    }
}
