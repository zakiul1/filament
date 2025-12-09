<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactoryCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subcategories()
    {
        return $this->hasMany(FactorySubcategory::class);
    }
}