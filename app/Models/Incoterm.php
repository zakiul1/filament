<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incoterm extends Model
{
    protected $fillable = [
        'code',
        'name',
        'version',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}