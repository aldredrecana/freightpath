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
        Schema::create('delivery_status_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('schedule_id')->index()->constrained()->cascadeOnDelete();
        $table->foreignId('logged_by')->constrained('users')->restrictOnDelete();
        $table->string('status', 20);
        $table->text('remarks')->nullable();
        $table->timestamp('logged_at')->useCurrent();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_status_logs');
    }
};
