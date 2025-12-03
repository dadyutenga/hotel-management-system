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
        // Table already exists in database
        // This migration is kept for reference only
        if (!Schema::hasTable('maintenance_requests')) {
            Schema::create('maintenance_requests', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('property_id');
                $table->uuid('room_id')->nullable();
                $table->string('location_details')->nullable();
                $table->string('issue_type');
                $table->text('description');
                $table->uuid('reported_by');
                $table->uuid('assigned_to')->nullable();
                $table->enum('status', ['PENDING', 'ASSIGNED', 'IN_PROGRESS', 'ON_HOLD', 'COMPLETED', 'CANCELLED'])->default('PENDING');
                $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'URGENT'])->default('MEDIUM');
                $table->timestamp('reported_at')->nullable();
                $table->timestamp('assigned_at')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->text('resolution_notes')->nullable();
                $table->timestamps();

                $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
                $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
                $table->foreign('reported_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
