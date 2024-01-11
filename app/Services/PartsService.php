<?php
namespace App\Services;

use App\Repositories\PartsRepository;
use App\Repositories\MemoRepository;
use Illuminate\Support\Arr;

class PartsService
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';

    /**
     * @var PartsRepository
     */
    private $partsRepository;

    /**
     * @var MemoRepository
     */
    private $memoRepository;

    /**
     * @param PartsRepository $partsRepository
     */
    public function __construct(PartsRepository $partsRepository, MemoRepository $memoRepository)
    {
        $this->partsRepository = $partsRepository;
        $this->memoRepository = $memoRepository;
    }

    public function __destruct()
    {
        $this->partsRepository->save();
    }

    /**
     * @param int $id
     */
    public function addParts(int $id): array
    {
        if ($this->partsRepository->isUnderLimit() === false) {
            return $this->setError('これ以上追加できません。');
        }
        if ($this->partsRepository->add($id) === true) {
            return $this->setSuccess('追加しました。');
        }
        return $this->setError('すでに存在しています。');
    }

    /**
     * @param int $id
     */
    public function deleteParts(?int $id): array
    {
        if ($this->partsRepository->remove($id) === true) {
            return $this->setSuccess('削除しました。');
        }
        return $this->setError('存在しません。');
    }

    /**
     * @param int $id
     */
    public function getParts(int $userId): array
    {
        $items = $this->partsRepository->all();
        $result = [];
        foreach ($items as $item) {
            $memo = $this->memoRepository->findByIdAndUserId($userId, $item->value);
            if ($memo) {
                $result[] = $memo;
            }
        }
        return $result;
    }

    /**
     * 
     * @return array<string, string>
     */
    public function getPartsMemoIds(): array
    {
        $items = $this->partsRepository->all();
        return Arr::pluck($items, 'value', 'value');
    }

    /**
     * 
     * @param string $message
     * @return array<string, string>
     */
    private function setSuccess(string $message): array
    {
        return $this->setResult(self::STATUS_SUCCESS, $message);
    }

    /**
     * 
     * @param string $message
     * @return array<string, string>
     */
    private function setError(string $message): array
    {
        return $this->setResult(self::STATUS_ERROR, $message);
    }

    /**
     * 
     * @param string $status
     * @param string $message
     * @return array<string, string>
     */
    private function setResult(string $status, string $message): array
    {
        $items = $this->partsRepository->all();
        return ['status' => $status, 'message' => $message, 'count' => count($items)];
    }
}