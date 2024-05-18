<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function contents()
    {
        return $this->hasMany(Content::class,'class_id','id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class,'class_id','id');
    }
}
