<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'code',
        'name',
        'short_name',
        'buyer_group',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'phone',
        'email',
        'website',
        'contact_person_name',
        'contact_person_designation',
        'contact_person_phone',
        'contact_person_email',
        'vat_reg_no',
        'eori_no',
        'registration_no',
        'default_currency_id',
        'default_incoterm_id',
        'default_payment_term_id',
        'default_shipment_mode_id',
        'default_destination_port_id',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function defaultCurrency()
    {
        return $this->belongsTo(Currency::class, 'default_currency_id');
    }

    public function defaultIncoterm()
    {
        return $this->belongsTo(Incoterm::class, 'default_incoterm_id');
    }

    public function defaultPaymentTerm()
    {
        return $this->belongsTo(PaymentTerm::class, 'default_payment_term_id');
    }

    public function defaultShipmentMode()
    {
        return $this->belongsTo(ShipmentMode::class, 'default_shipment_mode_id');
    }

    public function defaultDestinationPort()
    {
        return $this->belongsTo(Port::class, 'default_destination_port_id');
    }

    public function banks()
    {
        return $this->hasMany(CustomerBank::class);
    }
}