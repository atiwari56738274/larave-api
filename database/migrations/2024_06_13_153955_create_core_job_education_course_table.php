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
        Schema::create('core_job_education_course', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('job_education_id');
            $table->foreign('job_education_id')->references('id')->on('core_job_educations')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('title');
            $table->enum('status', ['active', 'inactive']);
            $table->softDeletes();
            $table->timestamps();
            $table->index(['title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_job_education_course');
    }
};
