<?php

namespace App\Models;

use App\Models\User;
use App\Models\Memo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiJob extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function memos()
    {
        return $this->belongsToMany(Memo::class)->orderBy('order', 'asc');
    }

}
