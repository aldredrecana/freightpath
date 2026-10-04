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
        Schema::table('schedules', function (Blueprint $table) {
            $table->timestamp('assigned_at')->nullable();
        });
        
        Schema::table('delivery_status_logs', function (Blueprint $table) {
            $table->boolean('is_late')->default(false);
        });
        
        DB::statement('UPDATE schedules SET assigned_at = created_at WHERE assigned_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('assigned_at');
        });
        
        Schema::table('delivery_status_logs', function (Blueprint $table) {
            $table->dropColumn('is_late');
        });
    }
};