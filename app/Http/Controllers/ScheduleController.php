<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Schedule;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        
        $schedules = Schedule::with(['driver.user', 'truck'])
        ->whereIn('status', ['pending', 'accepted', 'en_route'])
        ->when($q !== '', function ($query) use ($q) {
            $like = '%' . $q . '%';
            $query->where(function ($w) use ($q, $like) {
                $w->where('origin_address', 'ilike', $like)
                ->orWhere('destination_address', 'ilike', $like)
                ->orWhereHas('truck', fn ($t) => $t->where('plate_no', 'ilike', $like))
                ->orWhereHas('driver.user', fn ($u) => $u->where('name', 'ilike', $like));
                
                if (preg_match('/^(fp-?)?(\d+)$/i', $q, $m)) {
                    $w->orWhere('id', (int) $m[2]);
                }
            });
        })
        ->when(
            in_array($status, ['pending', 'accepted', 'en_route'], true),
            fn ($query) => $query->where('status', $status)
        )
        ->orderBy('departure_at')
        ->paginate(10)
        ->withQueryString();
        
        return view('schedules.index', compact('schedules', 'q', 'status'));
    }

    public function create()
    {
        return view('schedules.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['status'] = 'pending';
        DB::transaction(function () use ($data, $request) {
            $schedule = Schedule::create($data);
            $schedule->statusLogs()->create([
                'logged_by' => $request->user()->id,
                'status'    => 'pending',
                'remarks'   => 'Schedule created.',
                'logged_at' => now(),
                ]);
            });
            
            return redirect()->route('schedules.index')->with('status', 'Schedule created.');
        }

    public function edit(Schedule $schedule)
    {
        abort_if(in_array($schedule->status, ['delivered', 'cancelled'], true), 403);
        return view('schedules.edit', $this->formData($schedule) + ['schedule' => $schedule]);
    }

    public function update(Request $request, Schedule $schedule)
    {
        abort_if(in_array($schedule->status, ['delivered', 'cancelled'], true), 403);
        $schedule->update($this->validated($request));

        return redirect()->route('schedules.index')->with('status', 'Schedule updated.');
    }

    public function destroy(Schedule $schedule)
    {
        if ($schedule->status !== 'pending') {
            return back()->withErrors(['delete' => 'Only pending schedules can be deleted. This one already has delivery history.']);
        }

        $schedule->delete();

        return redirect()->route('schedules.index')->with('status', 'Schedule deleted.');
    }

    public function show(Request $request, Schedule $schedule)
    {
        abort_unless($this->canAccess($request, $schedule), 403);
        $schedule->load(['driver.user', 'truck', 'statusLogs.loggedBy']);
        $nextStatuses = $schedule->allowedNext($request->user());
        
        return view('schedules.show', compact('schedule', 'nextStatuses'));
    }

    public function updateStatus(Request $request, Schedule $schedule)
    {
        abort_unless($this->canAccess($request, $schedule), 403);
        
        $allowed = $schedule->allowedNext($request->user());
        abort_if(empty($allowed), 403);
        
        $data = $request->validate([
            'status'  => ['required', Rule::in($allowed)],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($schedule, $data, $request) {
            $schedule->update(['status' => $data['status']]);
            $schedule->statusLogs()->create([
                'logged_by' => $request->user()->id,
                'status'    => $data['status'],
                'remarks'   => $data['remarks'] ?? null,
                'logged_at' => now(),
            ]);
            if ($data['status'] === 'en_route') {
                $schedule->truck->update(['status' => 'on_trip']);
                $schedule->driver->update(['status' => 'on_trip']);
            } elseif (in_array($data['status'], ['delivered', 'cancelled'], true)) {
                if ($schedule->truck->status === 'on_trip') {
                    $schedule->truck->update(['status' => 'available']);
                }
                if ($schedule->driver->status === 'on_trip') {
                    $schedule->driver->update(['status' => 'available']);
                }
            }
        });
        
        return back()->with('status', 'Delivery status updated.');
    }
    public function mine(Request $request)
    {
        $schedules = Schedule::with('truck')
        ->whereHas('driver', fn ($q) => $q->where('user_id', $request->user()->id))
        ->latest()
        ->paginate(10);
        
        return view('schedules.mine', compact('schedules'));
    }
    private function canAccess(Request $request, Schedule $schedule): bool
    {
        $user = $request->user();
        return $user->hasRole('admin')
        || $user->hasRole('dispatcher')
        || $schedule->driver->user_id === $user->id;
    }
    
    private function validated(Request $request): array
    {
        return $request->validate([
            'driver_id'           => ['required', 'exists:drivers,id'],
            'truck_id'            => ['required', 'exists:trucks,id'],
            'origin_address'      => ['required', 'string'],
            'destination_address' => ['required', 'string'],
            'departure_at'        => ['required', 'date'],
            'expected_arrival_at' => ['required', 'date', 'after:departure_at'],
        ]);
    }

    // Dropdown options: active drivers and available trucks (plus the current ones when editing).
    private function formData(?Schedule $schedule = null): array
    {
        $drivers = Driver::with('user')
            ->where('status', 'available')
            ->when($schedule, fn ($q) => $q->orWhere('id', $schedule->driver_id))
            ->get();

        $trucks = Truck::where('status', 'available')
            ->when($schedule, fn ($q) => $q->orWhere('id', $schedule->truck_id))
            ->get();

        return compact('drivers', 'trucks');
    }
}