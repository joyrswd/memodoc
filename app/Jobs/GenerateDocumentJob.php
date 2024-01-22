<?php

namespace App\Jobs;

use App\Services\DocumentService;
use App\Services\ApiJobService;
use App\Interfaces\AiApiServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $jobId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, int $jobId)
    {
        $this->userId = $userId;
        $this->jobId = $jobId;
    }

    /**
     * Execute the job.
     */
    public function handle(AiApiServiceInterface $apiService, DocumentService $documentService, ApiJobService $jobService): void
    {
        if (($message = $this->isDeleted($jobService))
                || ($message = $this->isOverLimit($jobService, $apiService->getDailyLimit()))) {
            $jobService->exception($this->jobId, $message);
            return;
        }
        try {
            $jobService->process($this->jobId, $apiService->getKey());
            $contents = $jobService->getMemoContents($this->jobId);
            $apiResponse = $apiService->sendRequest($contents);
        } catch (\Throwable $e) {
            $jobService->exception($this->jobId, 'エラーが発生したため中断されました。', $e->getMessage());
            return;
        }
        if($apiService->isError($apiResponse)) {
            $jobService->error($this->jobId, 'APIのリクエストに失敗しました', $apiResponse);
        } else {
            $jobService->complete($this->jobId, $apiResponse);
            $this->createDocuemnt($apiService, $documentService, $apiResponse, $jobService->getMemoIds($this->jobId));
            $jobService->success($this->jobId);
        }
    }

    private function createDocuemnt(AiApiServiceInterface $apiService, DocumentService $documentService, array $apiResponse, array $memoIds): void
    {
        $rawTitle = $apiService->getTitle($apiResponse);
        $title = $documentService->fixTitle($rawTitle);
        $rawContent = $apiService->getContent($apiResponse);
        $content = $documentService->fixContent($rawContent, $rawTitle, $title);
        $documentService->addDocument($this->userId, $this->jobId, $title, $content, $memoIds);
    }

    private function isOverLimit(ApiJobService $jobService, int $limit): ?string
    {
        // リクエスト回数が上限に達している場合は、ジョブを中断する
        if($jobService->underDailyLimit($limit) === false) {
            $message = 'リクエスト回数上限に達したため、処理を中断しました。';
            return $message;
        }
        return null;
    }

    private function isDeleted(ApiJobService $jobService): ?string
    {
        // ジョブが削除されている場合は、ジョブを中断する
        if(empty($jobService->getApiJob($this->userId, $this->jobId))) {
            $message = 'ジョブが削除されているため、処理を中断しました。';
            return $message;
        }
        return null;
    }
}
