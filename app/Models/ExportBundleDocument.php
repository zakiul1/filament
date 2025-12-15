<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportBundleDocument extends Model
{
    protected $fillable = [
        'export_bundle_id',
        'doc_key',
        'documentable_type',
        'documentable_id',
        'status',
        'generated_at',
        'printed_at',
        'print_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'printed_at' => 'datetime',
    ];

    public function exportBundle(): BelongsTo
    {
        return $this->belongsTo(ExportBundle::class);
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}