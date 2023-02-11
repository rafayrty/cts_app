<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dedication extends Model
{
    use HasFactory;

    protected $fillable = ['dedication', 'name'];
}
