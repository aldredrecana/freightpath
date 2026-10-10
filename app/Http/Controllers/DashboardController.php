<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Schedule;
use App\Models\Truck;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole("driver")) {
            $driver = $user->driver;

            $current = $driver
                ? Schedule::with("truck")
                    ->where("driver_id", $driver->id)
                    ->whereIn("status", ["accepted", "en_route"])
                    ->orderBy("departure_at")
                    ->first()
                : null;

            $upcoming = $driver
                ? Schedule::with("truck")
                    ->where("driver_id", $driver->id)
                    ->where("status", "pending")
                    ->orderBy("departure_at")
                    ->take(5)
                    ->get()
                : collect();

            return view("dashboard-driver", [
                "mode" => "driver",
                "current" => $current,
                "upcoming" => $upcoming,
            ]);
        }

        $drivers = Driver::selectRaw("status, count(*) as total")
            ->groupBy("status")
            ->pluck("total", "status");
        $trucks = Truck::selectRaw("status, count(*) as total")
            ->groupBy("status")
            ->pluck("total", "status");
        $byStatus = Schedule::selectRaw("status, count(*) as total")
            ->groupBy("status")
            ->pluck("total", "status");

        $tripCounts = [
            "Scheduled" =>
                $byStatus->get("pending", 0) + $byStatus->get("accepted", 0),
            "Ongoing" => $byStatus->get("en_route", 0),
            "Completed" => $byStatus->get("delivered", 0),
        ];

        $window = (int) config("freightpath.late_window_hours", 3);

        $alerts = [
            "reassign" => Schedule::where(
                "status",
                "needs_reassignment",
            )->count(),
            "urgent" => Schedule::whereIn("status", [
                "pending",
                "needs_reassignment",
            ])
                ->where("departure_at", "<=", now()->addHours($window))
                ->count(),
            "licenses" => Driver::whereDate(
                "license_expiry",
                "<=",
                today()->addDays(30),
            )->count(),
        ];

        $today = Schedule::with(["driver.user", "truck"])
            ->whereDate("departure_at", today())
            ->orderBy("departure_at")
            ->get();

        $expiring = Driver::with("user")
            ->whereDate("license_expiry", "<=", today()->addDays(30))
            ->orderBy("license_expiry")
            ->take(5)
            ->get();

        return view(
            "dashboard",
            compact(
                "drivers",
                "trucks",
                "tripCounts",
                "alerts",
                "today",
                "expiring",
            ),
        );
    }
}
