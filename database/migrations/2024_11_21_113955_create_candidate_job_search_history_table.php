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
        Schema::create('candidate_job_search_history', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->json('job_titles')->nullable();     
            $table->json('job_skills')->nullable();     
            $table->json('job_locations')->nullable();     
            $table->json('job_company')->nullable();     
            $table->integer('experience')->nullable();
            $table->boolean('alert_enable')->default(false);
               
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_job_search_history');
    }
};
