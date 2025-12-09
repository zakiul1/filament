<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryCompany extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'trade_name',
        'group_name',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'phone',
        'mobile',
        'email',
        'website',
        'erc_no',
        'irc_no',
        'bin_no',
        'vat_reg_no',
        'tin_no',
        'bond_license_no',
        'contact_person_name',
        'contact_person_designation',
        'contact_person_phone',
        'contact_person_email',
        'default_currency_id',
        'is_default',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    public function defaultCurrency()
    {
        return $this->belongsTo(\App\Models\Currency::class, 'default_currency_id');
    }

    public function bankAccounts()
    {
        return $this->hasMany(\App\Models\BeneficiaryBankAccount::class);
    }
}