<?php

namespace App\Models;

use App\Models\Memo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * リレーション - memosテーブル
     */
    public function memos()
    {
        return $this->belongsToMany(Memo::class);
    }
}
