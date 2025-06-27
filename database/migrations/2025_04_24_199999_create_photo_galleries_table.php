<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('photo_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('photo_url'); // Image path
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('photo_gallery');
    }
};
