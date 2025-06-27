<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');   // FK to users
            $table->foreignId('plan_id')->constrained()->onDelete('cascade'); 
  // FK to plans
            $table->string('payment_method')->nullable();                      
            $table->dateTime('expires_at')->nullable();                         // nullable expiration datetime
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('subscriptions');
    }
};
