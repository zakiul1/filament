<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportBundleDocument extends Model
{
    protected $fillable = [
        'export_bundle_id',
        'document_type',
        'document_id',
        'print_route',
        'status',
    ];

    public function bundle()
    {
        return $this->belongsTo(ExportBundle::class, 'export_bundle_id');
    }
}