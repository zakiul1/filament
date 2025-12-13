<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ExportBundleDocument extends Model
{
    protected $guarded = [];

    protected $casts = [
        'generated_at' => 'datetime',
        'printed_at' => 'datetime',
    ];

    public function exportBundle()
    {
        return $this->belongsTo(ExportBundle::class);
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}