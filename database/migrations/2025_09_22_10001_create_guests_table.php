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
        Schema::create('guests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('nationality', 50)->nullable();
            $table->enum('id_type', ['PASSPORT', 'NATIONAL_ID', 'DRIVING_LICENSE', 'OTHER'])->nullable();
            $table->string('id_number', 50)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['MALE', 'FEMALE', 'OTHER'])->nullable();
            $table->json('preferences')->nullable(); // JSON field for guest preferences
            $table->json('loyalty_program_info')->nullable(); // JSON field for loyalty data
            $table->boolean('marketing_consent')->default(false);
            $table->text('notes')->nullable();
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            // Unique constraint - email per tenant (if provided)
            $table->unique(['tenant_id', 'email']);
            
            // Indexes
            $table->index('tenant_id');
            $table->index('email');
            $table->index('phone');
            $table->index('full_name');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};