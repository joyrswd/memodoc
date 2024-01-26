<?php

namespace App\Services;

use App\Repositories\DocumentRepository;

class DocumentService
{
    private DocumentRepository $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * {doc}のバリデーションルール
     * @see \App\Providers\RouteServiceProvider::boot()
     */
    public function bind(mixed $value): int
    {
        $int = filter_var($value, FILTER_VALIDATE_INT);
        if (is_int($int) && $this->getDocument(auth()->id(), $int)) {
            return $int;
        }
        abort(404);
    }

    /**
     * ドキュメントの登録
     */
    public function addDocument(int $userId, int $jobId, string $title, string $content, array $memoIds): int
    {
        return $this->documentRepository->store($userId, $jobId, $title, $content, $memoIds);
    }

    /**
     * 指定したドキュメントIDがユーザーIDに紐づく場合データを取得
     */
    public function getDocument(int $userId, int $documentId): array
    {
        return $this->documentRepository->findByIdAndUserId($userId, $documentId);
    }

    /**
     * ユーザーIDに紐づくドキュメント一覧を取得
     */
    public function getDocuments(int $userId, array $params): array
    {
        $pagination = $this->documentRepository->findByUserId($userId, $params)->paginate(10);
        foreach ($pagination->items() as $item) {
            // viewで表示するためのデータを追加
            $item->listTitle = empty($item->title) ? '（無題）' : $item->title;
            $item->datetime = $item->created_at->format('Y-m-d H:i');
        }
        $data = $pagination->toArray();
        $data['navigation'] = $pagination->withQueryString()->links('pagination::bootstrap-5');
        return $data;
    }

    /**
     * 指定したドキュメントIDがユーザーIDに紐づく場合データを削除
     */
    public function deleteDocument(int $userId, int $documentId): void
    {
        $this->documentRepository->deleteByUserIdAndId($userId, $documentId);
    }

    /**
     * 指定したドキュメントIDがユーザーIDに紐づく場合データを更新
     */
    public function updateDocument(int $userId, int $documentId, array $params): void
    {
        $this->documentRepository->updateByUserIdAndId($userId, $documentId, $params);
    }

}
