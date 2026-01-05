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
        Schema::create('guest_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('guest_id');
            $table->string('type', 20);
            $table->text('value');
            $table->boolean('is_primary')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('guest_id')->references('id')->on('guests')->onDelete('cascade');

            $table->index('guest_id');
            $table->index('type');
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_contacts');
    }
};
