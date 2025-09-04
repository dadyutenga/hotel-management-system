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
        Schema::create('core.tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('address');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->enum('certification_type', ['BRELA', 'VAT', 'TIN']);
            $table->string('certification_proof')->nullable();
            $table->enum('business_type', ['HOTEL', 'LODGE', 'RESTAURANT', 'BAR', 'PUB']);
            $table->string('base_currency')->default('TZS');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core.tenants');

    }

};