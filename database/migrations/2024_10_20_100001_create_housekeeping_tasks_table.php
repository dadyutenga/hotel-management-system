<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('housekeeping_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('property_id');
            $table->uuid('room_id')->nullable();
            $table->uuid('assigned_to')->nullable();
            $table->enum('task_type', ['DAILY_CLEAN', 'DEEP_CLEAN', 'TURNDOWN', 'INSPECTION', 'OTHER'])->default('DAILY_CLEAN');
            $table->enum('status', ['PENDING', 'IN_PROGRESS', 'COMPLETED', 'VERIFIED', 'CANCELLED'])->default('PENDING');
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH'])->default('MEDIUM');
            $table->text('notes')->nullable();
            $table->date('scheduled_date');
            $table->time('scheduled_time')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->uuid('verified_by')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['property_id', 'scheduled_date']);
            $table->index(['assigned_to', 'status']);
            $table->index(['room_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housekeeping_tasks');
    }
};
