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
        'locked_at',
        'locked_by',

        'submitted_at',
        'submitted_by',
        'submission_ref',
        'bank_ack_file_path',

        'closed_at',
        'closed_by',
        'close_notes',

        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'bundle_date' => 'date',
        'locked_at' => 'datetime',
        'submitted_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function commercialInvoice()
    {
        return $this->belongsTo(CommercialInvoice::class);
    }

    public function documents()
    {
        return $this->hasMany(ExportBundleDocument::class);
    }

    public function doc(string $key): ?ExportBundleDocument
    {
        return $this->documents->firstWhere('doc_key', $key);
    }

    public function lockedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'locked_by');
    }

    public function submittedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'submitted_by');
    }

    // ✅ Step 7 timeline
    public function events()
    {
        return $this->hasMany(ExportBundleEvent::class);
    }

    // ✅ Step 7 shipment relation (create Shipment model/table accordingly)
    public function shipment()
    {
        return $this->hasOne(Shipment::class);
        // If your shipment table uses export_bundle_id.
        // If not, tell me your FK name and I’ll adjust it.
    }

    public function isLocked(): bool
    {
        return !is_null($this->locked_at);
    }

    public function isSubmitted(): bool
    {
        return !is_null($this->submitted_at);
    }
    public function closedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'closed_by');
    }

    public function isClosed(): bool
    {
        return !is_null($this->closed_at);
    }
}