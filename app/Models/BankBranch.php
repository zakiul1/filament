<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankBranch extends Model
{
    protected $fillable = [
        'bank_id',
        'name',
        'branch_code',
        'swift_code',
        'city',
        'country_id',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }
}