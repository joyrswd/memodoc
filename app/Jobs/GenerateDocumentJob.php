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
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $userId;
    public int $jobId;

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

    /**
     * リクエスト回数が上限に達しているかどうかをチェック
     */
    private function isOverLimit(ApiJobService $jobService, int $limit): ?string
    {
        // リクエスト回数が上限に達している場合は、ジョブを中断する
        if($jobService->underDailyLimit($limit) === false) {
            $message = 'リクエスト回数上限に達したため、処理を中断しました。';
            return $message;
        }
        return null;
    }

    /**
     * ジョブが削除されているかどうかをチェック
     */
    private function isDeleted(ApiJobService $jobService): ?string
    {
        // ジョブが削除されている場合は、ジョブを中断する
        if(empty($jobService->getApiJob($this->userId, $this->jobId))) {
            $message = 'ジョブが削除されているため、処理を中断しました。';
            return $message;
        }
        return null;
    }

    /**
     * ドキュメントを作成
     */
    private function createDocuemnt(AiApiServiceInterface $apiService, DocumentService $documentService, array $apiResponse, array $memoIds): void
    {
        $rawTitle = $apiService->getTitle($apiResponse);
        $title = $this->fixTitle($rawTitle);
        $rawContent = $apiService->getContent($apiResponse);
        $content = $this->fixContent($rawContent, $rawTitle, $title);
        $documentService->addDocument($this->userId, $this->jobId, $title, $content, $memoIds);
    }

    /**
     * タイトルが255文字を超えている場合は空文字にする
     */
    private function fixTitle(string $rawTitle): string
    {
        $title = trim($rawTitle);
        return (mb_strlen($title) > 255) ? '' : $title;
    }

    /**
     * 内容が空の場合はタイトルを内容とする
     */
    private function fixContent(string $rawContent, string $rawTitle, string $title): string
    {
        $content = trim($rawContent);
        if (empty($title) && !empty($rawTitle)) {
            $content = $rawTitle . "\n" . $content;
        }
        return empty($content) ? $title : $content;
    }
}
