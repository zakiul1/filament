<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoiceItem extends Model
{
    protected $fillable = [
        'proforma_invoice_id',
        'line_no',
        'style_ref',
        'item_description',
        'hs_code',
        'factory_subcategory_id',
        'color',
        'size',
        'unit',
        'order_qty',
        'unit_price',
        'amount',
        'remarks',
    ];

    protected $casts = [
        'order_qty' => 'decimal:2',
        'unit_price' => 'decimal:4',
        'amount' => 'decimal:2',
    ];

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(FactorySubcategory::class, 'factory_subcategory_id');
    }
}