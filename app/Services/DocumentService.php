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

    public function getDocument(int $userId, int $documentId): array
    {
        return $this->documentRepository->findByIdAndUserId($userId, $documentId);
    }

    public function getDocuments(int $userId, array $params): array
    {
        $pagination = $this->documentRepository->findByUserId($userId, $params)->paginate(10);
        foreach ($pagination->items() as $item) {
            $item->listTitle = empty($item->title) ? '（無題）' : $item->title;
            $item->datetime = $item->created_at->format('Y-m-d H:i');
        }
        $data = $pagination->toArray();
        $data['navigation'] = $pagination->withQueryString()->links('pagination::bootstrap-5');
        return $data;
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