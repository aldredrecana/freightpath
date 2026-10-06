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
        // Constraints: only valid values and sensible times can be stored.
        DB::statement("ALTER TABLE drivers ALTER COLUMN status SET DEFAULT 'available'");
        DB::statement("ALTER TABLE drivers ADD CONSTRAINT drivers_status_check CHECK (status IN ('available','on_trip','off_duty'))");
        DB::statement("ALTER TABLE trucks ADD CONSTRAINT trucks_status_check CHECK (status IN ('available','on_trip','maintenance'))");
        DB::statement("ALTER TABLE schedules ADD CONSTRAINT schedules_status_check CHECK (status IN ('pending','accepted','en_route','needs_reassignment','delivered','cancelled'))");
        DB::statement("ALTER TABLE schedules ADD CONSTRAINT schedules_time_check CHECK (expected_arrival_at > departure_at)");
        DB::statement("ALTER TABLE delivery_status_logs ADD CONSTRAINT logs_status_check CHECK (status IN ('pending','accepted','en_route','needs_reassignment','delivered','cancelled'))");
        
        // Function + trigger 1: finished trips can no longer be changed.
        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE FUNCTION protect_finished_trips() RETURNS trigger AS $$
        BEGIN
            IF OLD.status IN ('delivered', 'cancelled') THEN
                RAISE EXCEPTION 'Trip % is % and can no longer be changed.', OLD.id, OLD.status;
            END IF;
            RETURN NEW;
        END;
        $$ LANGUAGE plpgsql;
        
        SQL);
        DB::unprepared('CREATE TRIGGER trg_protect_finished_trips BEFORE UPDATE ON schedules FOR EACH ROW EXECUTE FUNCTION protect_finished_trips()');
        
        // Function + trigger 2: the status history is append-only (no edits).
        DB::unprepared(<<<'SQL'
        CREATE OR REPLACE FUNCTION block_log_updates() RETURNS trigger AS $$
        BEGIN
            RAISE EXCEPTION 'Delivery status logs are append-only and cannot be edited.';
        END;
        $$ LANGUAGE plpgsql;
        SQL);
        DB::unprepared('CREATE TRIGGER trg_logs_append_only BEFORE UPDATE ON delivery_status_logs FOR EACH ROW EXECUTE FUNCTION block_log_updates()');
        
        // The view, now part of the migrations so it survives migrate:fresh.
        DB::statement(<<<'SQL'
        CREATE OR REPLACE VIEW active_schedules AS
        SELECT s.id AS schedule_id,
               u.name AS driver_name,
               t.plate_no,
               s.origin_address,
               s.destination_address,
               s.departure_at,
               s.expected_arrival_at,
               s.status
        FROM schedules s
        JOIN drivers d ON d.id = s.driver_id
        JOIN users u ON u.id = d.user_id
        JOIN trucks t ON t.id = s.truck_id
        WHERE s.status NOT IN ('delivered', 'cancelled')
        SQL);
    }
    /**
     * Reverse the migrations.
     */
   public function down(): void
   {
    DB::statement('DROP VIEW IF EXISTS active_schedules');
    DB::unprepared('DROP TRIGGER IF EXISTS trg_logs_append_only ON delivery_status_logs');
    DB::unprepared('DROP TRIGGER IF EXISTS trg_protect_finished_trips ON schedules');
    DB::unprepared('DROP FUNCTION IF EXISTS block_log_updates()');
    DB::unprepared('DROP FUNCTION IF EXISTS protect_finished_trips()');
    DB::statement('ALTER TABLE delivery_status_logs DROP CONSTRAINT IF EXISTS logs_status_check');
    DB::statement('ALTER TABLE schedules DROP CONSTRAINT IF EXISTS schedules_time_check');
    DB::statement('ALTER TABLE schedules DROP CONSTRAINT IF EXISTS schedules_status_check');
    DB::statement('ALTER TABLE trucks DROP CONSTRAINT IF EXISTS trucks_status_check');
    DB::statement('ALTER TABLE drivers DROP CONSTRAINT IF EXISTS drivers_status_check');
    }
};
