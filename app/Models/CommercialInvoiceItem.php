<?php
// app/Models/CommercialInvoiceItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommercialInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'commercial_invoice_id',
        'line_no',
        'proforma_invoice_item_id',
        'style_ref',
        'item_description',
        'hs_code',
        'factory_subcategory_id',
        'color',
        'size',
        'unit',
        'quantity',
        'unit_price',
        'amount',
        'carton_count',
        'net_weight',
        'gross_weight',
        'cbm',
        'remarks',
    ];

    public function commercialInvoice()
    {
        return $this->belongsTo(CommercialInvoice::class);
    }

    public function factorySubcategory()
    {
        return $this->belongsTo(FactorySubcategory::class);
    }

    public function proformaInvoiceItem()
    {
        return $this->belongsTo(ProformaInvoiceItem::class);
    }
}