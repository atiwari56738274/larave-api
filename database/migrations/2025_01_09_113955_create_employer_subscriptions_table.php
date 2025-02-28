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
        Schema::create('employer_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('subscription_id');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->string('payment_type')->nullable();
            $table->string('txnid')->nullable();
            $table->string('order_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('discount_type')->nullable();
            $table->string('discount_code')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('notes')->nullable();
            $table->date('validity')->nullable();
            $table->bigInteger('amount')->nullable();
            $table->bigInteger('gst_amount')->nullable();
            $table->bigInteger('discount_amount')->nullable();
            $table->bigInteger('total_amount')->nullable();
            $table->string('subscription_status')->nullable();
            $table->string('status')->nullable();

            
            $table->boolean('profile_creation')->default(false);
            $table->boolean('notification_via_email_sms_whatsapp')->default(false);

            $table->boolean('job_boosts')->default(false);
            $table->boolean('smart_boost_via_email_sms_whatsapp')->default(false);
            $table->boolean('dedicated_relationship_manager')->default(false);
            $table->boolean('campion_via_email_sms_whatsapp')->default(false);
            $table->boolean('multiple_user_login')->default(false);
            $table->boolean('reports')->default(false);
            $table->boolean('company_branding')->default(false);

            $table->bigInteger('no_of_job_post')->nullable();
            $table->bigInteger('remaining_job_post')->nullable();
            $table->bigInteger('no_of_urgent_hiring_tag')->nullable();
            $table->bigInteger('remaining_urgent_hiring_tag')->nullable();
            $table->bigInteger('no_of_candidate_unlock')->nullable();
            $table->bigInteger('remaining_candidate_unlock')->nullable();
            $table->bigInteger('no_of_job_published')->nullable();
            $table->bigInteger('remaining_job_published')->nullable();
            
            $table->softDeletes();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_subscriptions');
    }
};
