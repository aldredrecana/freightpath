<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Schedule;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        
        $schedules = Schedule::with(['driver.user', 'truck'])
        ->whereIn('status', Schedule::ACTIVE)
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
            in_array($status, Schedule::ACTIVE, true),
            fn ($query) => $query->where('status', $status)
        )
        ->orderByRaw("CASE WHEN status = 'needs_reassignment' THEN 0 ELSE 1 END")
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
        $data['assigned_at'] = now();
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
        $data = $this->validated($request);
        $newDriverId = (int) $data['driver_id'];
        
        unset($data['driver_id']);
        
        $driverChanged = $newDriverId !== (int) $schedule->driver_id;
        
        if ($driverChanged && $schedule->status === 'en_route') {
            return back()->withInput()->withErrors(['driver_id' => 'The driver cannot be changed while the trip is en route.']);
        }
        
        DB::transaction(function () use ($schedule, $data, $newDriverId, $driverChanged, $request) {
            $schedule->update($data);
            
            if ($driverChanged) {
                $this->assignDriver($schedule, $newDriverId, $request->user(), null);
                }
            });
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
        
        $candidates = collect();
        $lastDone = collect();
        
        if (! $request->user()->hasRole('driver')
            && in_array($schedule->status, ['pending', 'accepted', 'needs_reassignment'], true)) {
        $candidates = $this->candidatesFor($schedule);
        $lastDone = Schedule::where('status', 'delivered')
            ->whereIn('driver_id', $candidates->pluck('id'))
            ->selectRaw('driver_id, max(updated_at) as last_done')
            ->groupBy('driver_id')
            ->pluck('last_done', 'driver_id');
            
        }
        
        return view('schedules.show', compact('schedule', 'nextStatuses', 'candidates', 'lastDone'));
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
    

    public function decline(Request $request, Schedule $schedule)
{
    $user = $request->user();

    abort_unless($schedule->driver->user_id === $user->id, 403);
    abort_unless(in_array($schedule->status, ['pending', 'accepted'], true), 403);

    $data = $request->validate([
        'reason'  => ['required', Rule::in(array_keys(Schedule::DECLINE_REASONS))],
        'details' => ['required_if:reason,other', 'nullable', 'string', 'max:300'],
    ]);

    $isLate = $schedule->departure_at->lte(now()->addHours((int) config('freightpath.late_window_hours', 3)));
    $remarks = Schedule::DECLINE_REASONS[$data['reason']]
        . (filled($data['details'] ?? null) ? ': ' . $data['details'] : '');

    DB::transaction(function () use ($schedule, $user, $remarks, $isLate) {
        $schedule->update(['status' => 'needs_reassignment']);

        $schedule->statusLogs()->create([
            'logged_by' => $user->id,
            'status'    => 'needs_reassignment',
            'remarks'   => $remarks,
            'is_late'   => $isLate,
            'logged_at' => now(),
        ]);
    });

    return back()->with('status', 'You declined this trip. The dispatcher will assign another driver.');
}

public function reassign(Request $request, Schedule $schedule)
{
    abort_unless(in_array($schedule->status, ['pending', 'accepted', 'needs_reassignment'], true), 403);

    $data = $request->validate([
        'driver_id' => ['required', Rule::in($this->candidatesFor($schedule)->pluck('id')->all())],
        'note'      => ['nullable', 'string', 'max:300'],
    ]);

    DB::transaction(fn () => $this->assignDriver($schedule, (int) $data['driver_id'], $request->user(), $data['note'] ?? null));

    return redirect()->route('schedules.show', $schedule)
        ->with('status', 'Trip reassigned. The new driver must accept it.');
}

// Available drivers, excluding the current driver and anyone who already declined this trip.
private function candidatesFor(Schedule $schedule)
{
    $declined = $schedule->statusLogs()->where('status', 'needs_reassignment')->pluck('logged_by');

    return Driver::with('user')
        ->where('status', 'available')
        ->where('id', '!=', $schedule->driver_id)
        ->whereNotIn('user_id', $declined)
        ->get()
        ->sortBy(fn ($driver) => $driver->user->name)
        ->values();
}

// Moves the trip to a new driver, back to Pending, and logs it. Call inside a transaction.
private function assignDriver(Schedule $schedule, int $newDriverId, User $by, ?string $note): void
{
    $oldName = Driver::with('user')->find($schedule->driver_id)?->user->name ?? 'previous driver';
    $new = Driver::with('user')->findOrFail($newDriverId);

    $schedule->update([
        'driver_id'   => $new->id,
        'status'      => 'pending',
        'assigned_at' => now(),
    ]);

    $schedule->statusLogs()->create([
        'logged_by' => $by->id,
        'status'    => 'pending',
        'remarks'   => "Reassigned from {$oldName} to {$new->user->name}." . ($note ? " {$note}" : ''),
        'logged_at' => now(),
    ]);
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