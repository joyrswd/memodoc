<?php

namespace App\Jobs;

use App\Services\DocumentService;
use App\Services\ApiJobService;
use App\Interfaces\AiApiServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\FlareClient\Api;

class GenerateDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $jobId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, int $jobId, ApiJobService $jobService)
    {
        $this->userId = $userId;
        $this->jobId = $jobId;
        $jobService->queue($jobId);
    }

    /**
     * Execute the job.
     */
    public function handle(AiApiServiceInterface $apiService, DocumentService $documentService, ApiJobService $jobService): void
    {
        $limit = $apiService->getDailyLimit();
        if($jobService->underDailyLimit($limit) === false) {
            $jobService->exception($this->jobId, 'リクエスト回数上限に達しました。');
            return;
        }
        try {
            $jobService->process($this->jobId, $apiService->getKey());
            $contents = $jobService->getMemoContents($this->jobId);
            $apiResponse = $apiService->sendRequest($contents);
        } catch (\Exception $e) {
            $jobService->exception($this->jobId, $e->getMessage(), 'エラーが発生したため中断されました。');
            return;
        }
        if($apiService->isError($apiResponse)) {
            $jobService->error($this->jobId, $apiResponse);
        } else {
            $jobService->complete($this->jobId, $apiResponse);
            // APIレスポンスからタイトルと本文を取得する
            $title = $apiService->getTitle($apiResponse);
            $content = $apiService->getContent($apiResponse, $title);
            // メモのIDを取得して、文書を保存する
            $memoIds = $jobService->getMemoIds($this->jobId);
            $documentService->addDocument($this->userId, $this->jobId, $title, $content, $memoIds);
            $jobService->success($this->jobId);
        }
    }
}
