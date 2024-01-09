<?php
namespace App\Repositories;

use App\Models\Tag;

class TagRepository
{
    /**
     * @param string $string
     */
    public function store(string $string, int $memoId = null): int
    {
        $tag = $this->findByName($string);
        if ($tag === null) {
            $tag = new Tag();
            $tag->name = $string;
            $tag->save();
        }
        if (empty($memoId) === false) {
            $tag->memos()->attach($memoId);
        }
        return $tag->id;
    }

    /**
     * @param string $string
     * @return Tag|null
     */
    public function findByName(string $string): ?Tag
    {
        return Tag::where('name', $string)->first();
    }
}