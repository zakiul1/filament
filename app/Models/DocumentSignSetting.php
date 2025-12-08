<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSignSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type',
        'signatory_id',
        'show_signature',
        'show_seal',
    ];

    public function signatory()
    {
        return $this->belongsTo(Signatory::class);
    }
}