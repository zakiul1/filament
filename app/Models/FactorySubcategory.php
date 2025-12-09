<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactorySubcategory extends Model
{
    protected $fillable = [
        'factory_category_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(FactoryCategory::class, 'factory_category_id');
    }

    public function factories()
    {
        return $this->belongsToMany(
            Factory::class,
            'factory_factory_subcategory'
        )->withTimestamps();
    }
}