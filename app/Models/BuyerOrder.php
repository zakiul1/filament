<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuyerOrder extends Model
{
    protected $fillable = [
        'order_number',
        'order_date',
        'proforma_invoice_id',
        'lc_receive_id',
        'commercial_invoice_id',
        'customer_id',
        'beneficiary_company_id',
        'buyer_po_number',
        'season',
        'department',
        'merchandiser_name',
        'shipment_date_from',
        'shipment_date_to',
        'order_value',
        'status',
        'remarks',
        'internal_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'shipment_date_from' => 'date',
        'shipment_date_to' => 'date',
        'order_value' => 'decimal:4',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(BuyerOrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function beneficiaryCompany(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryCompany::class);
    }

    public function proformaInvoice(): BelongsTo
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function lcReceive(): BelongsTo
    {
        return $this->belongsTo(LcReceive::class);
    }

    public function commercialInvoice(): BelongsTo
    {
        return $this->belongsTo(CommercialInvoice::class);
    }
}