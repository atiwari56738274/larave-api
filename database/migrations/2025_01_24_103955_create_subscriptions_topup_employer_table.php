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
        Schema::create('subscriptions_topup_employer', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('employer_subscription_id')->nullable();
            $table->foreign('employer_subscription_id')->references('id')->on('employer_subscriptions')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->string('type');
            $table->bigInteger('value')->nullable();
            $table->boolean('is_approved')->default(false);
            
            $table->softDeletes();  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions_topup_employer');
    }
};
