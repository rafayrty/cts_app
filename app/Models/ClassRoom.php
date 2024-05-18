<?php

namespace App\Models;

use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function contents()
    {
        return $this->hasMany(Content::class, 'class_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(FilamentUser::class,'class_room_filament_user','class_room_id','filament_user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'class_id', 'id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'class_id', 'id');
    }

}
