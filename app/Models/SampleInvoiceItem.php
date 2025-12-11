<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleInvoiceItem extends Model
{
    protected $fillable = [
        'sample_invoice_id',
        'line_no',
        'style_ref',
        'item_description',
        'color',
        'size',
        'factory_subcategory_id',
        'unit',
        'quantity',
        'unit_price',
        'amount',
        'sample_type',
    ];

    public function sampleInvoice()
    {
        return $this->belongsTo(SampleInvoice::class);
    }

    public function factorySubcategory()
    {
        return $this->belongsTo(FactorySubcategory::class);
    }
}