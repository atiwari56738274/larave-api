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
        Schema::create('candidate_education', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            
            $table->unsignedBigInteger('education_id');
            $table->foreign('education_id')->references('id')->on('core_job_educations')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('course_id')->nullable();
            $table->foreign('course_id')->references('id')->on('core_job_education_course')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->string('insitute_university');

            $table->string('specialization')->nullable();
            $table->string('course_type');
            $table->bigInteger('course_duration_start_year');
            $table->bigInteger('course_duration_end_year');
            $table->string('grading_system');
            $table->string('marks');
            $table->boolean('primary_education')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_education');
    }
};