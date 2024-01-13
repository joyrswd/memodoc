<?php
namespace App\Repositories;

use App\Models\Document;

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
}
