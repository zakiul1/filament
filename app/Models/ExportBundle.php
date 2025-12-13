<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportBundle extends Model
{
    protected $fillable = [
        'commercial_invoice_id',
        'bundle_no',
        'bundle_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'bundle_date' => 'date',
    ];

    public function commercialInvoice()
    {
        return $this->belongsTo(CommercialInvoice::class);
    }

    public function documents()
    {
        return $this->hasMany(ExportBundleDocument::class);
    }

    // Helpful: get document row by key (CI, PL, NL, BOE_ONE, BOE_TWO)
    public function doc(string $key): ?ExportBundleDocument
    {
        return $this->documents->firstWhere('doc_key', $key);
    }
}