<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactCrmsTable extends Migration
{
    public function up()
    {
        Schema::create('contact_crms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();

            $table->string('client_name');
            $table->string('email');
            $table->string('phone_whatsapp')->nullable();

            $table->text('message')->nullable();  // <-- Added message here after phone_whatsapp

            $table->string('project_type')->nullable();
            $table->string('lead_source')->nullable();

            $table->enum('stage', ['lead', 'nurture', 'conversion', 'closed', 'follow_up'])->default('lead');

            $table->date('last_contact')->nullable();
            $table->string('next_step')->nullable();
            $table->text('notes')->nullable();
            $table->string('competitor')->nullable();

            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_crms');
    }
}
