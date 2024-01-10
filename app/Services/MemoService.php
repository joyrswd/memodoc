<?php
namespace App\Services;

use App\Repositories\MemoRepository;
use App\Repositories\TagRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MemoService
{
    /**
     * @var MemoRepository
     */
    private $memoRepository;

    /**
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * @param MemoRepository $memoRepository
     * @param TagRepository $tagRepository
     */
    public function __construct(MemoRepository $memoRepository, TagRepository $tagRepository)
    {
        $this->memoRepository = $memoRepository;
        $this->tagRepository = $tagRepository;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function addMemoAndTags(array $data): void
    {
        $memoId = $this->memoRepository->store($data);        
        if (empty($data['tags']) === false) {
            foreach($data['tags'] as $tag) {
                $this->tagRepository->store($tag, $memoId);
            }
        }
    }

    /**
     * 
     * @param int $userId
     * @param array<string, mixed> $data
     * @return LengthAwarePaginator
     */
    public function getMemos(int $userId, array $data): LengthAwarePaginator
    {
        return $this->memoRepository->findByUserId($userId, $data)->paginate(10);
    }

    /**
     * 
     * @param int $userId
     * @param int $memoId
     * @return array<string, mixed>
     */
    public function getMemo(int $userId, int $memoId): ?array
    {
        return $this->memoRepository->findByIdAndUserId($userId, $memoId);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateTags(array $data): void
    {
        $this->memoRepository->detachTags($data['memo_id']);
        if (empty($data['tags']) === false) {
            foreach($data['tags'] as $tag) {
                $this->tagRepository->store($tag, $data['memo_id']);
            }
        }
    }
}