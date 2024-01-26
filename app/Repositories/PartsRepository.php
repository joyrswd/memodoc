<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Session;
use stdClass;

class PartsRepository
{
    public const KEY = 'parts';
    public const LIMIT = 20;

    private Session $session;
    private array $items = [];

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * セッションからデータを読み込む
     */
    private function load(): void
    {
        $this->items = $this->session::get(self::KEY, []);
    }

    /**
     * セッションにデータを保存する
     */
    private function save(): bool
    {
        if ($this->isUnderLimit(true)) {
            $this->session::put(self::KEY, $this->items);
            return true;
        } else {
            return false;
        }
    }

    /**
     * セッションに保存するデータを作成する
     */
    private function make($key, $value): stdClass
    {
        $item = new stdClass();
        $item->key = $key;
        $item->value = $value;
        return $item;
    }

    /**
     * セッション内のデータを全て取得する
     */
    public function all(): array
    {
        $this->load();
        $items = [];
        foreach ($this->items as $key => $value) {
            $items[] = $this->make($key, $value);
        }
        return $items;
    }

    /**
     * セッション内からデータをメモIDで取得する
     */
    public function find(int $id): ?stdClass
    {
        $this->load();
        $index = array_search($id, $this->items);
        if ($index === false) {
            return null;
        }
        return $this->make($index, $this->items[$index]);
    }

    /**
     * セッション内にデータを追加する
     */
    public function add(int $id): bool
    {
        $value = $this->find($id);
        if ($value !== null) {
            return false;
        }
        $this->items[] = $id;
        return $this->save();
    }

    /**
     * セッション内からデータを削除する、または全て削除する
     */
    public function remove(?int $id = null): bool
    {
        if ($id === null) {
            $this->items = [];
        } elseif ($item = $this->find($id)) {
            unset($this->items[$item->key]);
        } else {
            return false;
        }
        return $this->save();
    }

    /**
     * セッション内のデータ数が上限を超えているか判定する
     */
    public function isUnderLimit($equal = false): bool
    {
        if ($equal === true) {
            return count($this->items) <= self::LIMIT;
        } else {
            return count($this->items) < self::LIMIT;
        }
    }

}
