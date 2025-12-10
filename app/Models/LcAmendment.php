<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LcAmendment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lc_receive_id',
        'amendment_number',
        'amendment_date',
        'amendment_type',
        'previous_lc_amount',
        'new_lc_amount',
        'previous_tolerance_plus',
        'new_tolerance_plus',
        'previous_tolerance_minus',
        'new_tolerance_minus',
        'previous_expiry_date',
        'new_expiry_date',
        'previous_last_shipment_date',
        'new_last_shipment_date',
        'change_summary',
        'other_changes',
        'remarks',
        'internal_notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amendment_date' => 'date',
        'previous_expiry_date' => 'date',
        'new_expiry_date' => 'date',
        'previous_last_shipment_date' => 'date',
        'new_last_shipment_date' => 'date',
    ];

    public function lcReceive()
    {
        return $this->belongsTo(LcReceive::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}