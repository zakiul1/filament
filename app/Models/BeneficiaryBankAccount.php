<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryBankAccount extends Model
{
    protected $fillable = [
        'beneficiary_company_id',
        'bank_branch_id',
        'currency_id',
        'account_title',
        'account_number',
        'iban',
        'swift_code',
        'routing_number',
        'is_lc_account',
        'is_tt_account',
        'is_default',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_lc_account' => 'boolean',
        'is_tt_account' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function beneficiaryCompany()
    {
        return $this->belongsTo(\App\Models\BeneficiaryCompany::class);
    }

    public function bankBranch()
    {
        return $this->belongsTo(\App\Models\BankBranch::class);
    }

    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class);
    }
}