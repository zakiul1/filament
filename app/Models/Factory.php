<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\Currency;
use App\Models\FactorySubcategory;
use App\Models\FactoryCertificate;
use App\Models\FactoryCertificateAssignment;


class Factory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'short_name',
        'factory_type',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'phone',
        'email',
        'website',
        'contact_person_name',
        'contact_person_designation',
        'contact_person_phone',
        'contact_person_email',
        'total_lines',
        'capacity_pcs_per_month',
        'capability_notes',
        'images',
        'default_currency_id',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    // Relations
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function defaultCurrency()
    {
        return $this->belongsTo(Currency::class, 'default_currency_id');
    }

    public function subcategories()
    {
        return $this->belongsToMany(
            FactorySubcategory::class,
            'factory_factory_subcategory'
        )->withTimestamps();
    }

    public function certificateAssignments()
    {
        return $this->hasMany(FactoryCertificateAssignment::class);
    }

    public function certificates()
    {
        return $this->belongsToMany(
            FactoryCertificate::class,
            'factory_certificate_assignments'
        )->withPivot(['file_path', 'issued_at', 'expires_at', 'is_active', 'notes'])
            ->withTimestamps();
    }

}