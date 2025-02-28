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
        Schema::create('candidate_job_preference', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');


            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('core_job_department')
                ->onDelete('cascade')->onUpdate('cascade');    

            $table->string('job_type');
            $table->string('employment_type');
            $table->string('preferred_shift');
            $table->string('expected_salary_currency');
            $table->bigInteger('expected_salary_amount');
            $table->string('notice_period');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_job_preference');
    }
};