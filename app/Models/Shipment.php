<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'commercial_invoice_id',
        'export_bundle_id',
        'shipment_no',
        'shipment_date',
        'mode',
        'bl_awb_no',
        'vessel_name',
        'voyage_no',
        'container_no',
        'seal_no',
        'port_of_loading',
        'port_of_discharge',
        'final_destination',
        'etd',
        'eta',
        'forwarder_name',
        'forwarder_contact',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'shipment_date' => 'date',
        'etd' => 'date',
        'eta' => 'date',
    ];

    public function exportBundle()
    {
        return $this->belongsTo(ExportBundle::class);
    }

    public function commercialInvoice()
    {
        return $this->belongsTo(CommercialInvoice::class);
    }
}