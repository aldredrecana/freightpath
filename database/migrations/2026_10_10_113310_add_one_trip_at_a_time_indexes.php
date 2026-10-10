<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement(
            "CREATE UNIQUE INDEX schedules_one_active_trip_per_driver ON schedules (driver_id) WHERE status = 'en_route'",
        );
        DB::statement(
            "CREATE UNIQUE INDEX schedules_one_active_trip_per_truck ON schedules (truck_id) WHERE status = 'en_route'",
        );
    }

    public function down(): void
    {
        DB::statement(
            "DROP INDEX IF EXISTS schedules_one_active_trip_per_driver",
        );
        DB::statement(
            "DROP INDEX IF EXISTS schedules_one_active_trip_per_truck",
        );
    }
};
