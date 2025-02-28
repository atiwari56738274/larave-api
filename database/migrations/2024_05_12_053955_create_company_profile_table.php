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
        Schema::create('company_profile', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('company_name');
            $table->string('contact_person_name')->nullable();
            $table->string('email')->nullable();
            $table->bigInteger('mobile')->nullable();
            $table->bigInteger('phone')->nullable();
            $table->string('company_logo')->nullable();
            $table->longText('company_description')->nullable();
            $table->string('url')->nullable();
            $table->string('tan_number')->nullable();
            $table->string('name_as_pan_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('gst_number')->nullable();
            $table->bigInteger('number_of_employee')->nullable();
            $table->string('company_type')->nullable();
            $table->string('industry_type')->nullable();
            $table->string('role')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->bigInteger('city_id')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->bigInteger('pincode')->nullable();


            $table->unsignedBigInteger('added_by');
            $table->foreign('added_by')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profile');
    }
};
