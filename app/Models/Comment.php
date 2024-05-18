<?php

namespace App\Models;

use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function class_room()
    {
        return $this->hasMany(ClassRoom::class, 'id', 'class_id');
    }

    public function user()
    {
        return $this->belongsTo(FilamentUser::class);
    }
}
