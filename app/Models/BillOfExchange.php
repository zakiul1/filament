<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillOfExchange extends Model
{
    use HasFactory;

    protected $fillable = [
        'boe_number',
        'boe_type',
        'issue_date',
        'tenor_days',
        'maturity_date',

        'customer_id',
        'beneficiary_company_id',
        'lc_receive_id',
        'commercial_invoice_id',
        'currency_id',

        'amount',
        'amount_in_words',

        'drawee_name',
        'drawee_address',
        'drawee_bank_name',
        'drawee_bank_address',
        'place_of_drawing',

        'status',
        'remarks',
        'internal_notes',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'maturity_date' => 'date',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function beneficiaryCompany()
    {
        return $this->belongsTo(BeneficiaryCompany::class);
    }

    public function lcReceive()
    {
        return $this->belongsTo(LcReceive::class);
    }

    public function commercialInvoice()
    {
        return $this->belongsTo(CommercialInvoice::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}