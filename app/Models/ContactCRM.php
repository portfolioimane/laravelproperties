<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactCRM extends Model
{
    protected $table = 'contact_crms';

    protected $fillable = [
        'property_id',
        'client_name',
        'email',
        'phone_whatsapp',
        'message',          // <--- Added message here
        'project_type',
        'lead_source',
        'stage',
        'last_contact',
        'next_step',
        'notes',
        'competitor',
        'assigned_to',
    ];

    protected $casts = [
        'last_contact' => 'date',
    ];

    // Relationship with Property model
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    // Relationship with User model (assigned_to)
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
