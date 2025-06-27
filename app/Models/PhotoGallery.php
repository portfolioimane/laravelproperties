<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoGallery extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'photo_url'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
