<?php

namespace App\Models;

use App\Models\User;
use App\Models\Memo;
use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiJob extends Model
{
    use HasFactory, SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function memos()
    {
        return $this->belongsToMany(Memo::class)->orderBy('order', 'asc');
    }

    public function document()
    {
        return $this->hasOne(Document::class);
    }

}
