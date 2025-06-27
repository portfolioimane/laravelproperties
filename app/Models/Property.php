<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'title',
        'description',
        'price',
        'image',
        'address',
        'city',         // added city
        'area',
        'rooms',
        'bathrooms',
        'type',         // property type: apartment, villa, etc.
        'offer_type',   // offer type: rent, sale, etc.
        'featured',
    ];

    /**
     * Get the user who owns the property.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function contacts()
    {
        return $this->hasMany(ContactCRM::class, 'property_id');
    }

    /**
     * Get the photo gallery associated with the property.
     */
    public function photoGallery()
    {
        return $this->hasMany(PhotoGallery::class);
    }
}
