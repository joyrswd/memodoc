<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Memo;

class Tag extends Model
{
    use HasFactory;

    public function memos()
    {
        return $this->belongsToMany(Memo::class);
    }
}
