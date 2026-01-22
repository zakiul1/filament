<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BankAccountLink extends Model
{
    protected $fillable = [
        'bank_account_id',
        'owner_type',
        'owner_id',
        'label',
        'is_lc_account',
        'is_tt_account',
        'is_default',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_lc_account' => 'bool',
        'is_tt_account' => 'bool',
        'is_default' => 'bool',
        'is_active' => 'bool',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }
}