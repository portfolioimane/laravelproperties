<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('price');  // Changed from string to unsignedBigInteger
            $table->string('image');
            $table->string('address');
            $table->string('city');
            $table->string('type');
            $table->string('offer_type');
            $table->integer('area');     // square meters
            $table->integer('rooms');
            $table->integer('bathrooms');
            $table->boolean('featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
