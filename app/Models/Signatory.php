<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signatory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'designation',
        'signature_path',
        'is_active',
    ];

    public function documentSignSettings()
    {
        return $this->hasMany(DocumentSignSetting::class);
    }
}