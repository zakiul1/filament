<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoice extends Model
{
    protected $fillable = [
        'pi_number',
        'pi_date',
        'revision_no',
        'customer_id',
        'beneficiary_company_id',
        'beneficiary_bank_account_id',
        'customer_bank_id',
        'currency_id',
        'incoterm_id',
        'shipment_mode_id',
        'port_of_loading_id',
        'port_of_discharge_id',
        'payment_term_id',
        'courier_id',
        'buyer_reference',
        'place_of_delivery',
        'shipment_lead_time_days',
        'shipment_date_from',
        'shipment_date_to',
        'validity_date',
        'subtotal',
        'discount_amount',
        'other_charges',
        'total_amount',
        'total_amount_in_words',
        'status',
        'created_by',
        'updated_by',
        'remarks',
        'internal_notes',
    ];

    protected $casts = [
        'pi_date' => 'date',
        'shipment_date_from' => 'date',
        'shipment_date_to' => 'date',
        'validity_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relations
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function beneficiaryCompany()
    {
        return $this->belongsTo(BeneficiaryCompany::class);
    }

    public function beneficiaryBankAccount()
    {
        return $this->belongsTo(BeneficiaryBankAccount::class);
    }

    public function customerBank()
    {
        return $this->belongsTo(CustomerBank::class);
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

    public function paymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function items()
    {
        return $this->hasMany(ProformaInvoiceItem::class);
    }
    /*     public function proformaInvoice()
        {
            return $this->belongsTo(ProformaInvoice::class);
        } */

}