<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'questions' => 'array'
    ];

    public function class_room()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id', 'id');
    }
}
