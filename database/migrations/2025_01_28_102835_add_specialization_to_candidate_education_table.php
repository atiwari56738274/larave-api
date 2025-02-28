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
        Schema::table('candidate_education', function (Blueprint $table) {
            $table->unsignedBigInteger('specialization_id')->nullable()->after('specialization');
            $table->foreign('specialization_id')->references('id')->on('core_job_education_course_specialty')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidate_education', function (Blueprint $table) {
            $table->dropColumn('specialization_id');
        });
    }
};
