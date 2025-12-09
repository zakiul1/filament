<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'service_type',
        'account_number',
        'contact_person',
        'contact_phone',
        'contact_email',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'website',
        'tracking_url_template',
        'supports_documents',
        'supports_parcels',
        'supports_import',
        'supports_export',
        'is_default',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'supports_documents' => 'boolean',
        'supports_parcels' => 'boolean',
        'supports_import' => 'boolean',
        'supports_export' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }
}