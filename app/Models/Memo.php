<?php

namespace App\Models;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Memo extends Model
{
    use HasFactory;

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
