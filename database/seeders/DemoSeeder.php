<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@freightpath.test')->firstOrFail();

        User::firstOrCreate(
            ['email' => 'dispatcher@freightpath.test'],
            [
                'name'     => 'Demo Dispatcher',
                'password' => Hash::make('Dispatcher12345'),
                'role_id'  => Role::where('name', 'dispatcher')->value('id'),
            ]
        );

        $drivers = collect([
            ['Juan Dela Cruz', 'juan@freightpath.test', 'N01-23-456789', '09171234567', 'Calauan, Laguna'],
            ['Maria Santos', 'maria@freightpath.test', 'N02-34-567890', '09181234567', 'Los Banos, Laguna'],
            ['Pedro Reyes', 'pedro@freightpath.test', 'N03-45-678901', '09191234567', 'Santa Cruz, Laguna'],
        ])->map(function ($row) {
            [$name, $email, $license, $phone, $address] = $row;

            $user = User::firstOrCreate(['email' => $email], [
                'name'     => $name,
                'password' => Hash::make('Driver12345'),
                'role_id'  => Role::where('name', 'driver')->value('id'),
            ]);

            return Driver::firstOrCreate(['user_id' => $user->id], [
                'license_no'     => $license,
                'license_expiry' => now()->addYears(2)->toDateString(),
                'contact_no'     => $phone,
                'address'        => $address,
                'status'         => 'available',
            ]);
        });

        $trucks = collect([
            ['ABC 1234', 'Isuzu Elf', 3000],
            ['XYZ 5678', 'Hino 300', 4000],
            ['DEF 9012', 'Mitsubishi Fuso', 6000],
        ])->map(fn ($row) => Truck::firstOrCreate(
            ['plate_no' => $row[0]],
            ['model' => $row[1], 'capacity_kg' => $row[2], 'status' => 'available']
        ));

        if (Schedule::count() > 0) {
            return;
        }

        // A finished trip with a full history.
        $done = Schedule::create([
            'driver_id'           => $drivers[0]->id,
            'truck_id'            => $trucks[0]->id,
            'origin_address'      => 'Calauan, Laguna',
            'destination_address' => 'Batangas City, Batangas',
            'departure_at'        => now()->subDay()->setTime(6, 0),
            'expected_arrival_at' => now()->subDay()->setTime(10, 0),
            'status'              => 'delivered',
            'assigned_at'         => now()->subDays(2),
        ]);

        foreach ([
            ['pending',   $admin->id,               now()->subDays(2),                  'Schedule created.'],
            ['accepted',  $drivers[0]->user_id,     now()->subDay()->setTime(5, 30),    null],
            ['en_route',  $drivers[0]->user_id,     now()->subDay()->setTime(6, 5),     null],
            ['delivered', $drivers[0]->user_id,     now()->subDay()->setTime(9, 40),    'Received by the warehouse.'],
        ] as [$status, $by, $at, $remarks]) {
            $done->statusLogs()->create([
                'logged_by' => $by, 'status' => $status, 'remarks' => $remarks, 'logged_at' => $at,
            ]);
        }

        // An upcoming trip waiting for its driver to accept.
        $next = Schedule::create([
            'driver_id'           => $drivers[1]->id,
            'truck_id'            => $trucks[1]->id,
            'origin_address'      => 'Los Banos, Laguna',
            'destination_address' => 'Lipa City, Batangas',
            'departure_at'        => now()->addDay()->setTime(8, 0),
            'expected_arrival_at' => now()->addDay()->setTime(12, 0),
            'status'              => 'pending',
            'assigned_at'         => now(),
        ]);

        $next->statusLogs()->create([
            'logged_by' => $admin->id, 'status' => 'pending', 'remarks' => 'Schedule created.', 'logged_at' => now(),
        ]);
    }
}