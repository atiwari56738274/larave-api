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
        Schema::create('candidate_projects', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->string('project_tag');
            $table->unsignedBigInteger('candidate_employment_id')->nullable();
            $table->unsignedBigInteger('candidate_education_id')->nullable();
            $table->string('project_status');
            $table->bigInteger('work_from_year');
            $table->bigInteger('work_from_month');
            $table->bigInteger('work_till_year')->nullable();
            $table->bigInteger('work_till_month')->nullable();
            $table->longText('project_description')->nullable();
            $table->string('employment_type');
            $table->bigInteger('team_size');
            $table->string('role');
            $table->longText('role_description');
            $table->longText('skill_used');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_projects');
    }
};