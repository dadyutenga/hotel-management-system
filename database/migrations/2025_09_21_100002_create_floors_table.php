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
        Schema::create('floors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('building_id');
            $table->integer('number');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('building_id')->references('id')->on('buildings')->onDelete('cascade');
            
            // Unique constraint - one floor number per building
            $table->unique(['building_id', 'number']);
            
            // Indexes
            $table->index('building_id');
            
            // Check constraint (will be added via raw SQL for better compatibility)
        });
        
        // Add check constraint for positive floor numbers
        DB::statement('ALTER TABLE floors ADD CONSTRAINT chk_floor_number_positive CHECK (number > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('floors');
    }
};