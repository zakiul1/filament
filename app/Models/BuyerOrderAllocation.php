<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerOrderAllocation extends Model
{
    protected $fillable = [
        'buyer_order_item_id',
        'factory_id',
        'allocated_qty',
        'notes',
    ];

    protected $casts = [
        'allocated_qty' => 'decimal:4',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(BuyerOrderItem::class, 'buyer_order_item_id');
    }

    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }
}