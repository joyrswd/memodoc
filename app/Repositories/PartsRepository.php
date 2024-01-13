<?php
namespace App\Repositories;

use Illuminate\Support\Facades\Session;
use stdClass;

class PartsRepository
{
    const KEY='parts';
    const LIMIT=20;

    /**
     * @var Session
     */
    private $session;
    private $items=[];

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    private function load(): void
    {
        $this->items = $this->session::get(self::KEY, []);
    }

    private function save(): bool
    {
        if ($this->isUnderLimit(true)) {
            $this->session::put(self::KEY, $this->items);
            return true;
        } else {
            return false;
        }
    }

    private function make($key, $value) : stdClass
    {
        $item = new stdClass();
        $item->key = $key;
        $item->value = $value;
        return $item;
    }

    public function all(): array
    {
        $this->load();
        $items = [];
        foreach ($this->items as $key => $value) {
            $items[] = $this->make($key, $value);
        }
        return $items;
    }

    public function find(int $id): ?stdClass
    {
        $this->load();
        $index = array_search($id, $this->items);
        if ($index === false) {
            return null;
        }
        return $this->make($index, $this->items[$index]);
    }

    public function add(int $id): bool
    {
        $value = $this->find($id);
        if ($value !== null) {
            return false;
        }
        $this->items[] = $id;
        return $this->save();
    }

    public function remove(?int $id=null): bool
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

    public function isUnderLimit($equal=false): bool
    {
        if ($equal === true) {
            return count($this->items) <= self::LIMIT;
        } else {
            return count($this->items) < self::LIMIT;
        }
    }

}