<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactCRMSeeder extends Seeder
{
    public function run()
    {
        DB::table('contact_crms')->insert([
            [
                'property_id'    => 1,
                'client_name'    => 'John Doe',
                'email'          => 'john@example.com',
                'phone_whatsapp' => '+212600000000',
                'message'        => 'Please contact me ASAP regarding the property.', // added
                'project_type'   => 'Website',
                'lead_source'    => 'Referral',
                'stage'          => 'lead',
                'last_contact'   => '2025-06-10',
                'next_step'      => 'Follow up call',
                'notes'          => 'Interested in early move-in',
                'competitor'     => 'https://competitor1.com',
                'assigned_to'    => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'property_id'    => 2,
                'client_name'    => 'Jane Smith',
                'email'          => 'jane@example.com',
                'phone_whatsapp' => null,
                'message'        => 'Looking forward to a site visit.', // added
                'project_type'   => 'App',
                'lead_source'    => 'Facebook',
                'stage'          => 'nurture',
                'last_contact'   => null,
                'next_step'      => null,
                'notes'          => 'Wants to schedule a visit',
                'competitor'     => 'https://competitor2.com',
                'assigned_to'    => 2,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            // Add more seed data here if needed
        ]);
    }
}
