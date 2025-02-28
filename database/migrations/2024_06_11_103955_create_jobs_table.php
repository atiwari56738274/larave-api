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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('core_job_department')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->string('hiring_company_name');
            $table->string('job_name');

            $table->unsignedBigInteger('job_title_id');
            $table->foreign('job_title_id')->references('id')->on('core_job_title')
                ->onDelete('cascade')->onUpdate('cascade');  

            $table->string('work_location_type');
            $table->string('job_type');
            $table->string('job_shift')->nullable();
            $table->string('job_tag')->nullable();
            $table->string('job_apply_age')->nullable();

            $table->string('pay_type')->nullable();
            $table->string('additional_pay')->nullable();
            $table->boolean('is_planned_start_date')->default(false);
            $table->date('plan_start_date')->nullable();
            $table->bigInteger('no_of_hire')->nullable();

            $table->boolean('salary_not_disclose')->default(false);
            $table->double('salary_min_amount')->default(0);
            $table->double('salary_max_amount')->default(0);
            $table->string('salary_per_type')->nullable();

            $table->longText('job_description')->nullable();
            $table->boolean('joining_amount_required')->default(false);

            $table->string('upload_cv')->default(false);
            $table->string('job_deadline')->nullable();

            $table->bigInteger('min_job_exp')->nullable();
            $table->bigInteger('max_job_exp')->nullable();

            $table->string('interview_type')->nullable();
            $table->string('candidate_contact_via')->nullable();

            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->bigInteger('city_id')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->bigInteger('pincode')->nullable();
            $table->boolean('is_published')->default(false);
            $table->datetime('published_at')->nullable();
            $table->boolean('urgent_hiring_tag')->default(false);
            $table->enum('status', ['active', 'inactive', 'completed', 'cancelled']);
            
            $table->string('job_page_name')->unique()->nullable();

            $table->unsignedBigInteger('added_by');
            $table->foreign('added_by')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            
            $table->softDeletes();    
            $table->timestamps();
            $table->index(['department_id', 'job_title_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
