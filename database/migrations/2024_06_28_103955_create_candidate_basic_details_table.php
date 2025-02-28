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
        Schema::create('candidate_basic_details', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');    

            $table->string('gender');
            $table->date('dob');
            $table->string('marital_status');
            $table->string('address');
            $table->string('address1')->nullable();


            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('country_id');
            $table->bigInteger('pincode');

            $table->string('resume')->nullable();
            $table->longText('profile_summery')->nullable();
            $table->string('profile_pic')->nullable();
            $table->string('work_status')->default('fresher');
            $table->unsignedBigInteger('experience_year')->nullable();
            $table->unsignedBigInteger('experience_month')->nullable();
            $table->bigInteger('job_title_id')->nullable();
            $table->boolean('active_job_search')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_basic_details');
    }
};