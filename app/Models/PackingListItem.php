<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingListItem extends Model
{
    protected $fillable = [
        'packing_list_id',
        'line_no',
        'description',
        'carton_from',
        'carton_to',
        'total_cartons',
        'qty_per_carton',
        'total_qty',
        'net_weight',
        'gross_weight',
        'cbm',
    ];

    public function packingList()
    {
        return $this->belongsTo(PackingList::class);
    }
}