<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidate_job_preference_locations', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('preference_id');
            $table->foreign('preference_id')->references('id')->on('candidate_job_preference')
                ->onDelete('cascade')->onUpdate('cascade');    
            
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('core_job_city')
                ->onDelete('cascade')->onUpdate('cascade');   

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_job_preference_locations');
    }
};