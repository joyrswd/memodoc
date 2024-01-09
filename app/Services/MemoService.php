<?php
namespace App\Services;

use App\Repositories\MemoRepository;
use App\Repositories\TagRepository;

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
}