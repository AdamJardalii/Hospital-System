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
        Schema::create('insurance_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->string('policy_number');
            $table->string('provider_name');
            $table->string('group_number')->nullable();
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('card_image_path')->nullable();
            $table->json('ocr_raw_data')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            
            $table->index('policy_number');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_cards');
    }
};

