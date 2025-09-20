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
        Schema::create('room_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('property_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('capacity');
            $table->decimal('base_rate', 18, 4);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            
            // Unique constraint
            $table->unique(['property_id', 'name']);
            
            // Indexes
            $table->index('property_id');
        });
        
        // Add check constraints
        DB::statement('ALTER TABLE room_types ADD CONSTRAINT chk_room_types_capacity_positive CHECK (capacity > 0)');
        DB::statement('ALTER TABLE room_types ADD CONSTRAINT chk_room_types_base_rate_positive CHECK (base_rate >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};