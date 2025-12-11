<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleInvoice extends Model
{
    protected $fillable = [
        'sample_number',
        'sample_date',
        'status',
        'customer_id',
        'beneficiary_company_id',
        'currency_id',
        'incoterm_id',
        'shipment_mode_id',
        'port_of_loading_id',
        'port_of_discharge_id',
        'courier_id',
        'courier_tracking_no',
        'subtotal',
        'discount_amount',
        'other_charges',
        'total_amount',
        'total_amount_in_words',
        'remarks',
        'internal_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sample_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function beneficiaryCompany()
    {
        return $this->belongsTo(BeneficiaryCompany::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function incoterm()
    {
        return $this->belongsTo(Incoterm::class);
    }

    public function shipmentMode()
    {
        return $this->belongsTo(ShipmentMode::class);
    }

    public function portOfLoading()
    {
        return $this->belongsTo(Port::class, 'port_of_loading_id');
    }

    public function portOfDischarge()
    {
        return $this->belongsTo(Port::class, 'port_of_discharge_id');
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function items()
    {
        return $this->hasMany(SampleInvoiceItem::class);
    }
}