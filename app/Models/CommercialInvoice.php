<?php
// app/Models/CommercialInvoice.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommercialInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'customer_id',
        'beneficiary_company_id',
        'currency_id',
        'proforma_invoice_id',
        'lc_receive_id',
        'shipment_mode_id',
        'incoterm_id',
        'payment_term_id',
        'port_of_loading_id',
        'port_of_discharge_id',
        'place_of_delivery',
        'courier_id',
        'subtotal',
        'discount_amount',
        'other_charges',
        'total_amount',
        'total_amount_in_words',
        'status',
        'remarks',
        'internal_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
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

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function lcReceive()
    {
        return $this->belongsTo(LcReceive::class);
    }

    public function shipmentMode()
    {
        return $this->belongsTo(ShipmentMode::class);
    }

    public function incoterm()
    {
        return $this->belongsTo(Incoterm::class);
    }

    public function paymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class);
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
        return $this->hasMany(CommercialInvoiceItem::class);
    }
}