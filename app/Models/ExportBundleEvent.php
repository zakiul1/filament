<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportBundleEvent extends Model
{
    protected $fillable = [
        'export_bundle_id',
        'event',
        'event_at',
        'ref',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'event_at' => 'datetime',
    ];

    public function exportBundle()
    {
        return $this->belongsTo(ExportBundle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}