<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingList extends Model
{
    protected $fillable = [
        'pl_number',
        'pl_date',
        'commercial_invoice_id',
        'lc_receive_id',
        'customer_id',
        'beneficiary_company_id',
        'total_cartons',
        'total_quantity',
        'total_nw',
        'total_gw',
        'total_cbm',
        'remarks',
        'internal_notes',
        'status',
        'created_by',
        'updated_by',
    ];

    public function items()
    {
        return $this->hasMany(PackingListItem::class);
    }

    public function commercialInvoice()
    {
        return $this->belongsTo(CommercialInvoice::class);
    }

    public function lcReceive()
    {
        return $this->belongsTo(LcReceive::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function beneficiaryCompany()
    {
        return $this->belongsTo(BeneficiaryCompany::class);
    }
}