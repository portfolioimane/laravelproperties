<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'max_properties',
        'duration_days',
    ];

    // One Plan has many subscriptions
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
