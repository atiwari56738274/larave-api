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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();

            $table->enum('type', ['candidate', 'employer']);
            $table->string('subscription_type')->nullable();
            $table->boolean('is_default')->default(false);
            $table->longtext('title');
            $table->longtext('description');
            $table->bigInteger('mrp_amount');
            $table->bigInteger('amount');
            $table->string('logo')->nullable();
            $table->integer('sort_order')->unsigned();
            $table->integer('validity')->unsigned();
            $table->enum('validity_type', ['day', 'month', 'year']);
            $table->longtext('custom_permission')->nullable();
            $table->enum('status', ['active', 'inactive']);
            
            $table->boolean('profile_creation')->default(false);
            $table->bigInteger('no_of_job_apply')->nullable();
            $table->boolean('resume_builder')->default(false);
            $table->boolean('notification_via_email_sms_whatsapp')->default(false);

            $table->bigInteger('no_of_job_post')->nullable();
            $table->bigInteger('no_of_urgent_hiring_tag')->nullable();
            $table->bigInteger('no_of_candidate_unlock')->nullable();
            $table->bigInteger('no_of_job_published')->nullable();
            $table->boolean('job_boosts')->default(false);
            $table->boolean('smart_boost_via_email_sms_whatsapp')->default(false);
            $table->boolean('dedicated_relationship_manager')->default(false);
            $table->boolean('campion_via_email_sms_whatsapp')->default(false);
            $table->boolean('multiple_user_login')->default(false);
            $table->boolean('reports')->default(false);
            $table->boolean('company_branding')->default(false);
            
            $table->softDeletes();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
