<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LcReceive extends Model
{
    protected $fillable = [
        'lc_number',
        'lc_date',
        'lc_type',
        'customer_id',
        'customer_bank_id',
        'beneficiary_company_id',
        'beneficiary_bank_account_id',
        'currency_id',
        'lc_amount',
        'tolerance_plus',
        'tolerance_minus',
        'lc_amount_in_words',
        'expiry_date',
        'last_shipment_date',
        'presentation_days',
        'incoterm_id',
        'shipment_mode_id',
        'port_of_loading_id',
        'port_of_discharge_id',
        'partial_shipment_allowed',
        'transshipment_allowed',
        'reference_pi_number',
        'reimbursement_bank',
        'status',
        'remarks',
        'internal_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'lc_date' => 'date',
        'expiry_date' => 'date',
        'last_shipment_date' => 'date',
        'partial_shipment_allowed' => 'boolean',
        'transshipment_allowed' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerBank(): BelongsTo
    {
        return $this->belongsTo(CustomerBank::class);
    }

    public function beneficiaryCompany(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryCompany::class);
    }

    public function beneficiaryBankAccount(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryBankAccount::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function incoterm(): BelongsTo
    {
        return $this->belongsTo(Incoterm::class);
    }

    public function shipmentMode(): BelongsTo
    {
        return $this->belongsTo(ShipmentMode::class);
    }

    public function portOfLoading(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'port_of_loading_id');
    }

    public function portOfDischarge(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'port_of_discharge_id');
    }
    public function amendments()
    {
        return $this->hasMany(LcAmendment::class);
    }

}