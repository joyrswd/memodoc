<?php

namespace App\Services;

use App\Repositories\DocumentRepository;

class DocumentService
{
    /**
     * @var DocumentRepository
     */
    private $documentRepository;

    /**
     * @param DocumentRepository $documentRepository
     */
    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function addDocument(int $userId, int $jobId, string $title, string $content, array $memoIds): int
    {
        return $this->documentRepository->store($userId, $jobId, $title, $content, $memoIds);
    }
}
