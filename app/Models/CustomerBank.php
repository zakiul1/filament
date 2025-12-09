<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\BankBranch;


class CustomerBank extends Model
{
    protected $fillable = [
        'customer_id',
        'bank_branch_id',
        'label',
        'is_default_lc',
        'is_default_tt',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_default_lc' => 'boolean',
        'is_default_tt' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function bankBranch()
    {
        return $this->belongsTo(BankBranch::class);
    }
}