<?php
namespace App\Services;

use App\Repositories\MemoRepository;
use App\Repositories\TagRepository;
use App\Repositories\PartsRepository;
use Illuminate\Support\Str;

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
     * @var PartsRepository
     */
    private $partsRepository;

    /**
     * @param MemoRepository $memoRepository
     * @param TagRepository $tagRepository
     * @param PartsRepository $partsRepository
     */
    public function __construct(MemoRepository $memoRepository, TagRepository $tagRepository, PartsRepository $partsRepository)
    {
        $this->memoRepository = $memoRepository;
        $this->tagRepository = $tagRepository;
        $this->partsRepository = $partsRepository;
    }

    /**
     * @param array<string, mixed> $params
     */
    public function addMemoAndTags(array $params): void
    {
        $memoId = $this->memoRepository->store($params);        
        if (empty($params['tags']) === false) {
            foreach($params['tags'] as $tag) {
                $this->tagRepository->store($tag, $memoId);
            }
        }
    }

    /**
     * 
     * @param int $userId
     * @param array<string, mixed> $params
     */
    public function getMemos(int $userId, array $params): array
    {
        $pagination = $this->memoRepository->findByUserId($userId, $params)->paginate(10);
        foreach ($pagination->items() as $item) {
            $item->datetime = $item->created_at->format('Y-m-d H:i');
            $item->intro = Str::limit($item->content, 30, '...');
            $item->tagNames = $item->tags->pluck('name')->toArray();
        }
        $data = $pagination->toArray();
        $data['navigation'] = $pagination->withQueryString()->links('pagination::bootstrap-5');
        return $data;
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
     * @param array<string, mixed> $params
     */
    public function updateTags(array $params): void
    {
        $this->memoRepository->detachTags($params['memo_id']);
        if (empty($params['tags']) === false) {
            foreach($params['tags'] as $tag) {
                $this->tagRepository->store($tag, $params['memo_id']);
            }
        }
    }

    /**
     * @param array<string, mixed> $params
     */
    public function deleteMemo(int $userId, int $memoId): void
    {
        $this->memoRepository->deleteByIdAndUserId($userId, $memoId);
        $this->partsRepository->remove($memoId);
    }
}