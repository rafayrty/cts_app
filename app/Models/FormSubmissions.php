<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmissions extends Model
{
    protected $guarded = [];

    protected $casts = ['content' => 'array'];

    use HasFactory;
}
