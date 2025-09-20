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
        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('property_id');
            $table->uuid('floor_id')->nullable();
            $table->uuid('room_type_id');
            $table->string('room_number', 50);
            $table->enum('status', [
                'OCCUPIED',
                'VACANT', 
                'DIRTY',
                'CLEAN',
                'OUT_OF_ORDER',
                'CLEANING_IN_PROGRESS',
                'INSPECTED'
            ])->default('CLEAN');
            $table->decimal('current_rate', 18, 4);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('floor_id')->references('id')->on('floors')->onDelete('set null');
            $table->foreign('room_type_id')->references('id')->on('room_types')->onDelete('restrict');
            
            // Unique constraint - one room number per property
            $table->unique(['property_id', 'room_number']);
            
            // Indexes
            $table->index('property_id');
            $table->index('floor_id');
            $table->index('room_type_id');
            $table->index('status');
        });
        
        // Add check constraint for positive current rate
        DB::statement('ALTER TABLE rooms ADD CONSTRAINT chk_rooms_current_rate_positive CHECK (current_rate >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};