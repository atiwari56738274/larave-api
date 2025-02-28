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
        Schema::create('company_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('company_profile_id');
            $table->foreign('company_profile_id')->references('id')->on('company_profile')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('job_title_id');
            $table->foreign('job_title_id')->references('id')->on('core_job_title')
                ->onDelete('cascade')->onUpdate('cascade'); 

            $table->unsignedBigInteger('job_location_id');
            $table->foreign('job_location_id')->references('id')->on('core_job_city')
                ->onDelete('cascade')->onUpdate('cascade');  

            $table->string('review_title');
            $table->integer('review_rating')->default(0);
            $table->longText('review_description')->nullable();
            $table->longText('review_reply')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected']);
            
            $table->softDeletes();    
            $table->timestamps();
            $table->index(['company_profile_id', 'job_title_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_reviews');
    }
};
