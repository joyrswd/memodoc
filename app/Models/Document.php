<?php

namespace App\Models;

use App\Models\Memo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    /**
     * リレーション - memosテーブル
     */
    public function memos()
    {
        return $this->belongsToMany(Memo::class);
    }
}
