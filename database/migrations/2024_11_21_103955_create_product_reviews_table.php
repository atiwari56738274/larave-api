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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();

            $table->string('name');
            $table->string('image')->nullable();
            $table->string('designation');
            $table->string('company_name');
            $table->integer('review_rating')->default(0);
            $table->longText('review_description')->nullable();
            $table->enum('status', ['active', 'inactive']);
            
            $table->softDeletes();    
            $table->timestamps();
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
