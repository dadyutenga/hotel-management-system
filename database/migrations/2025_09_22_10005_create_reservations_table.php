<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if table already exists
        if (Schema::hasTable('reservations')) {
            // Table already exists, skip creation
            return;
        }

        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('property_id');
            $table->uuid('guest_id');
            $table->uuid('group_booking_id')->nullable();
            $table->uuid('corporate_account_id')->nullable();
            $table->enum('status', [
                'PENDING',
                'CONFIRMED', 
                'CHECKED_IN',
                'CHECKED_OUT',
                'CANCELLED',
                'NO_SHOW',
                'HOLD'
            ])->default('PENDING');
            $table->date('arrival_date');
            $table->date('departure_date');
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->decimal('total_amount', 18, 4)->default(0.00);
            $table->decimal('discount_amount', 18, 4)->default(0.00);
            $table->string('discount_reason')->nullable();
            $table->text('special_requests')->nullable();
            $table->text('notes')->nullable();
            $table->string('source', 50)->nullable(); // ONLINE, PHONE, WALK_IN, etc.
            $table->string('external_reference', 100)->nullable(); // For external booking systems
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('guest_id')->references('id')->on('guests')->onDelete('restrict');
            $table->foreign('group_booking_id')->references('id')->on('group_bookings')->onDelete('set null');
            $table->foreign('corporate_account_id')->references('id')->on('corporate_accounts')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for performance
            $table->index('property_id');
            $table->index('guest_id');
            $table->index('status');
            $table->index('arrival_date');
            $table->index('departure_date');
            $table->index(['arrival_date', 'departure_date']); // For date range queries
            $table->index('created_by');
            $table->index('source');
            $table->index('external_reference');
        });
        
        // Add check constraints
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_adults_positive CHECK (adults > 0)');
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_children_non_negative CHECK (children >= 0)');
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_total_amount_non_negative CHECK (total_amount >= 0)');
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_discount_amount_non_negative CHECK (discount_amount >= 0)');
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_departure_after_arrival CHECK (departure_date > arrival_date)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};