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
        Schema::create('candidate_employment', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('core_job_department')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->boolean('is_current_employement')->default(false);  
            $table->string('current_company');
            $table->string('employement_type');
            $table->bigInteger('experience_year')->nullable();
            $table->bigInteger('experience_month')->nullable();

            $table->unsignedBigInteger('job_title_id');
            $table->foreign('job_title_id')->references('id')->on('core_job_title')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->bigInteger('start_year');
            $table->bigInteger('start_month');
            $table->bigInteger('end_year')->nullable();
            $table->bigInteger('end_month')->nullable();
            $table->string('current_salary_currency')->nullable();
            $table->bigInteger('current_salary_amount')->nullable();
            $table->longText('job_profile')->nullable();
            $table->string('notice_period')->nullable();
            $table->date('last_working_date')->nullable();
            $table->boolean('has_any_offer')->default(false); 
            $table->string('offered_salary_currency')->nullable();
            $table->bigInteger('offered_salary_amount')->nullable();

            $table->string('offered_company')->nullable();
            $table->unsignedBigInteger('offered_job_title_id')->nullable();
            $table->foreign('offered_job_title_id')->references('id')->on('core_job_title')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_employment');
    }
};