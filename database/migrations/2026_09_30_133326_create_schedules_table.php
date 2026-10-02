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
        Schema::create('schedules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('driver_id')->index()->constrained()->restrictOnDelete();
        $table->foreignId('truck_id')->index()->constrained()->restrictOnDelete();
        $table->text('origin_address');
        $table->text('destination_address');
        $table->timestamp('departure_at');
        $table->timestamp('expected_arrival_at');
        $table->string('status', 20)->default('pending')->index();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
