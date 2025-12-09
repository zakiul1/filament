<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactoryCertificateAssignment extends Model
{
    protected $fillable = [
        'factory_id',
        'factory_certificate_id',
        'file_path',
        'issued_at',
        'expires_at',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function factory()
    {
        return $this->belongsTo(Factory::class);
    }

    public function certificate()
    {
        return $this->belongsTo(FactoryCertificate::class, 'factory_certificate_id');
    }
}