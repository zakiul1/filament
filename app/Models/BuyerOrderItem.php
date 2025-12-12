<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuyerOrderItem extends Model
{
    protected $fillable = [
        'buyer_order_id',
        'line_no',
        'style_ref',
        'item_description',
        'color',
        'size',
        'unit',
        'factory_subcategory_id',
        'factory_id',
        'order_qty',
        'unit_price',
        'amount',
        'remarks',
    ];

    protected $casts = [
        'order_qty' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'amount' => 'decimal:4',
    ];

    public function buyerOrder(): BelongsTo
    {
        return $this->belongsTo(BuyerOrder::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(BuyerOrderItemAllocation::class, 'buyer_order_item_id');
    }

    public function allocatedQty(): float
    {
        return (float) $this->allocations()->sum('qty');
    }

    public function remainingQty(): float
    {
        return (float) ($this->order_qty ?? 0) - $this->allocatedQty();
    }
}