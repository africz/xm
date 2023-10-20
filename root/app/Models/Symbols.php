<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Symbols extends Model
{
    protected $fillable = [
        'symbol',
        'description',
        'updated_at'
    ];
}
