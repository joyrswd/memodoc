<?php

namespace App\Services;

use App\Repositories\DocumentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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

    public function getDocument(int $userId, int $documentId): array
    {
        return $this->documentRepository->findByIdAndUserId($userId, $documentId);
    }

    public function getDocuments(int $userId, array $params): LengthAwarePaginator
    {
        return $this->documentRepository->findByUserId($userId, $params)->paginate(10);
    }

    public function deleteDocument(int $userId, int $documentId): void
    {
        $this->documentRepository->deleteByUserIdAndId($userId, $documentId);
    }

    public function updateDocument(int $userId, int $documentId, array $params): void
    {
        $this->documentRepository->updateByUserIdAndId($userId, $documentId, $params);
    }
}
