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
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->string('house_number', 20)->unique();
            $table->string('block', 10)->nullable();
            $table->text('address')->nullable();
            $table->enum('ownership_type', ['permanent', 'rental'])->default('permanent');
            $table->enum('status', ['occupied', 'vacant'])->default('vacant');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
