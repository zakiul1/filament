<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LcTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'lc_receive_id',
        'factory_id',
        'transfer_no',
        'transfer_date',
        'currency_id',
        'transfer_amount',
        'tolerance_plus',
        'tolerance_minus',
        'status',
        'remarks',
        'internal_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'transfer_amount' => 'decimal:2',
        'tolerance_plus' => 'decimal:2',
        'tolerance_minus' => 'decimal:2',
    ];

    public function lcReceive()
    {
        return $this->belongsTo(LcReceive::class);
    }

    public function factory()
    {
        return $this->belongsTo(Factory::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}