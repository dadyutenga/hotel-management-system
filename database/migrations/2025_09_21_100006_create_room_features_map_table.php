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
        Schema::create('room_features_map', function (Blueprint $table) {
            $table->uuid('room_id');
            $table->uuid('feature_id');
            
            // Primary key
            $table->primary(['room_id', 'feature_id']);

            // Foreign key constraints
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('feature_id')->references('id')->on('room_features')->onDelete('cascade');
            
            // Indexes
            $table->index('room_id');
            $table->index('feature_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_features_map');
    }
};