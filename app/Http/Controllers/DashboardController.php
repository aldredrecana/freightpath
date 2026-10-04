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

        if ($user->hasRole('driver')) {
            $driver = $user->driver;

            $current = $driver
                ? Schedule::with('truck')
                    ->where('driver_id', $driver->id)
                    ->whereIn('status', ['accepted', 'en_route'])
                    ->orderBy('departure_at')
                    ->first()
                : null;

            $upcoming = $driver
                ? Schedule::with('truck')
                    ->where('driver_id', $driver->id)
                    ->where('status', 'pending')
                    ->orderBy('departure_at')
                    ->take(5)
                    ->get()
                : collect();

            return view('dashboard', [
                'mode'     => 'driver',
                'current'  => $current,
                'upcoming' => $upcoming,
            ]);
        }

        $drivers  = Driver::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $trucks   = Truck::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $byStatus = Schedule::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $tripCounts = [
            'Scheduled' => $byStatus->get('pending', 0) + $byStatus->get('accepted', 0),
            'Ongoing'   => $byStatus->get('en_route', 0),
            'Completed' => $byStatus->get('delivered', 0),
        ];

        $recent = Schedule::with(['driver.user', 'truck'])->latest()->take(5)->get();

        $window = (int) config('freightpath.late_window_hours', 3);
        $alerts = [
            'reassign' => Schedule::where('status', 'needs_reassignment')->count(),
            'urgent'   => Schedule::whereIn('status', ['pending', 'needs_reassignment'])
                 ->where('departure_at', '<=', now()->addHours($window))->count(),
            ];

        return view('dashboard', [
            'mode'       => 'staff',
            'drivers'    => $drivers,
            'trucks'     => $trucks,
            'tripCounts' => $tripCounts,
            'recent'     => $recent,
            'alerts' => $alerts,
        ]);
    }
}