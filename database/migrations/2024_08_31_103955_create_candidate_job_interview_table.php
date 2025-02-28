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
        Schema::create('candidate_job_interview', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();

            $table->unsignedBigInteger('candidate_job_apply_id');
            $table->foreign('candidate_job_apply_id')->references('id')->on('candidate_job_apply')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->integer('duration')->comment('in minuts');
            $table->string('call_type')->nullable();
            $table->longText('message')->nullable();
            $table->string('hiring_team_email')->nullable();
            $table->date('interview_date')->nullable();
            $table->time('interview_time')->nullable();

            $table->enum('status', ['scheduled', 'rejected', 'selected', 'hold'])->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_job_interview');
    }
};