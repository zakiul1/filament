<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTerm extends Model
{
    protected $fillable = [
        'name',
        'code',
        'days_due',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'days_due' => 'integer',
    ];
}