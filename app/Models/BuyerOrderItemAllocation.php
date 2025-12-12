<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerOrderItemAllocation extends Model
{
    protected $fillable = [
        'buyer_order_item_id',
        'factory_id',
        'qty',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(BuyerOrderItem::class, 'buyer_order_item_id');
    }

    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class, 'factory_id');
    }
}