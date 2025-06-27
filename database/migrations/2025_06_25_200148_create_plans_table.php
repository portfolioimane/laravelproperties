<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();                // free, basic, premium...
            $table->decimal('price', 8, 2)->default(0);      // in MAD or other currency
            $table->integer('max_properties');              // number of properties allowed
            $table->integer('duration_days')->nullable();   // null = no expiration
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('plans');
    }
};
