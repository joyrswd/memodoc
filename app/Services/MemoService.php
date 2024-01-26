<?php
namespace App\Services;

use App\Repositories\MemoRepository;
use App\Repositories\TagRepository;
use App\Repositories\PartsRepository;
use Illuminate\Support\Str;

class MemoService
{
    private MemoRepository $memoRepository;
    private TagRepository $tagRepository;
    private PartsRepository $partsRepository;

    public function __construct(MemoRepository $memoRepository, TagRepository $tagRepository, PartsRepository $partsRepository)
    {
        $this->memoRepository = $memoRepository;
        $this->tagRepository = $tagRepository;
        $this->partsRepository = $partsRepository;
    }

    public function bind(mixed $value): int
    {
        $int = filter_var($value, FILTER_VALIDATE_INT);
        if (is_int($int) && $this->getMemo(auth()->id(), $int)) {
            return $int;
        }
        abort(404);
    }

    public function addMemoAndTags(array $params): void
    {
        $memoId = $this->memoRepository->store($params);        
        if (empty($params['tags']) === false) {
            foreach($params['tags'] as $tag) {
                $this->tagRepository->store($tag, $memoId);
            }
        }
    }

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

    public function getMemo(int $userId, int $memoId): ?array
    {
        return $this->memoRepository->findByIdAndUserId($userId, $memoId);
    }

    public function updateTags(array $params): void
    {
        $this->memoRepository->detachTags($params['memo_id']);
        if (empty($params['tags']) === false) {
            foreach($params['tags'] as $tag) {
                $this->tagRepository->store($tag, $params['memo_id']);
            }
        }
    }

    public function deleteMemo(int $userId, int $memoId): void
    {
        $this->memoRepository->deleteByIdAndUserId($userId, $memoId);
        $this->partsRepository->remove($memoId);
    }
}