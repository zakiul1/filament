<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    protected $fillable = [
        'name',
        'code',
        'country_id',
        'mode',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }
}