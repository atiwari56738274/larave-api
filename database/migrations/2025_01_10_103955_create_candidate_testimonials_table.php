<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('candidate_testimonials', function (Blueprint $table) {
            $table->id(); 
            $table->uuid('uuid')->unique(); 
            $table->string('name');
            $table->string('designation');
            $table->longText('feedback')->nullable();
            $table->string('photo_url')->nullable(); 
            $table->integer('review_rating')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_testimonials');
    }
};
