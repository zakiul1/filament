<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegotiationLetter extends Model
{
    protected $fillable = [
        'letter_number',
        'letter_date',
        'commercial_invoice_id',
        'lc_receive_id',
        'beneficiary_company_id',
        'customer_id',
        'currency_id',
        'invoice_amount',
        'net_payable_amount',
        'deductions',
        'bank_name',
        'bank_branch',
        'swift_code',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    public function commercialInvoice()
    {
        return $this->belongsTo(CommercialInvoice::class);
    }

    public function lcReceive()
    {
        return $this->belongsTo(LcReceive::class);
    }

    public function beneficiaryCompany()
    {
        return $this->belongsTo(BeneficiaryCompany::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}