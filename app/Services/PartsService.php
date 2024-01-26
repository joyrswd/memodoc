<?php

namespace App\Services;

use App\Repositories\PartsRepository;
use App\Repositories\MemoRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PartsService
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_ERROR = 'error';

    private PartsRepository $partsRepository;
    private MemoRepository $memoRepository;

    public function __construct(PartsRepository $partsRepository, MemoRepository $memoRepository)
    {
        $this->partsRepository = $partsRepository;
        $this->memoRepository = $memoRepository;
    }

    public function addParts(int $id): array
    {
        if ($this->partsRepository->isUnderLimit() === false) {
            return $this->setError('これ以上追加できません。');
        } elseif ($this->partsRepository->add($id) === false) {
            return $this->setError('すでに存在しています。');
        } else {
            return $this->setSuccess('追加しました。');
        }
    }

    public function deleteParts(?int $id = null): array
    {
        if ($this->partsRepository->remove($id) === true) {
            return $this->setSuccess('削除しました。');
        }
        return $this->setError('存在しません。');
    }

    public function updateParts(array $memos): array
    {
        $parts = $this->partsRepository->all();
        $ids = Arr::pluck($parts, 'value');
        if (empty($ids)) {
            return $this->setError('保存されたパーツがありません。');
        } elseif (array_diff($memos, $ids) + array_diff($ids, $memos)) {
            return $this->setError('パーツの指定に過不足があります。');
        }
        $this->partsRepository->remove();
        foreach ($memos as $id) {
            $this->partsRepository->add($id);
        }
        return $this->setSuccess('更新しました。');
    }

    public function getStatus(?string $name)
    {
        $status = $this->setSuccess('現在のパーツ内容です。');
        return empty($name) ? $status : $status[$name];
    }

    public function getParts(int $userId): array
    {
        $items = $this->partsRepository->all();
        $result = [];
        foreach ($items as $item) {
            $memo = $this->memoRepository->findByIdAndUserId($userId, $item->value);
            if ($memo) {
                //日付をフォーマット
                $memo['datetime'] = date('Y-m-d', strtotime($memo['created_at']));
                $memo['intro'] = Str::limit($memo['content'], 30, '...');
                $result[] = $memo;
            } else {
                $this->partsRepository->remove($item->value);
            }
        }
        return $result;
    }

    public function getMemoValues(int $userId, string $key): array
    {
        return Arr::pluck($this->getParts($userId), $key);
    }

    private function setSuccess(string $message): array
    {
        return $this->setResult(self::STATUS_SUCCESS, $message);
    }

    private function setError(string $message): array
    {
        return $this->setResult(self::STATUS_ERROR, $message);
    }

    private function setResult(string $status, string $message): array
    {
        $items = $this->partsRepository->all();
        return ['status' => $status, 'message' => $message, 'count' => count($items), 'items' => Arr::pluck($items, 'value', 'key')];
    }
}
