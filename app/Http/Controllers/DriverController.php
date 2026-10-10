<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Schedule;
use App\Models\DeliveryStatusLog;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query("q", ""));
        $status = $request->query("status");

        $drivers = Driver::with("user")
            ->when($q !== "", function ($query) use ($q) {
                $like = "%" . $q . "%";
                $query->where(function ($w) use ($like) {
                    $w->where("license_no", "ilike", $like)
                        ->orWhere("contact_no", "ilike", $like)
                        ->orWhereHas(
                            "user",
                            fn($u) => $u->where("name", "ilike", $like),
                        );
                });
            })
            ->when(
                in_array($status, ["available", "on_trip", "off_duty"], true),
                fn($query) => $query->where("status", $status),
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = Driver::selectRaw("status, count(*) as total")
            ->groupBy("status")
            ->pluck("total", "status");

        return view(
            "drivers.index",
            compact("drivers", "counts", "q", "status"),
        );
    }

    public function create()
    {
        return view("drivers.create");
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "email", "max:255", "unique:users,email"],
            "password" => ["required", "string", "min:8", "confirmed"],
            "license_no" => [
                "required",
                "string",
                "max:50",
                "unique:drivers,license_no",
            ],
            "license_expiry" => ["required", "date", "after:today"],
            "contact_no" => ["required", "string", "max:20"],
            "address" => ["required", "string"],
            "status" => [
                "required",
                Rule::in(["available", "on_trip", "off_duty"]),
            ],
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                "name" => $data["name"],
                "email" => $data["email"],
                "password" => Hash::make($data["password"]),
                "role_id" => Role::where("name", "driver")->value("id"),
            ]);

            $user->driver()->create([
                "license_no" => $data["license_no"],
                "license_expiry" => $data["license_expiry"],
                "contact_no" => $data["contact_no"],
                "address" => $data["address"],
                "status" => $data["status"],
            ]);
        });

        return redirect()
            ->route("drivers.index")
            ->with("status", "Driver added.");
    }

    public function edit(Driver $driver)
    {
        $driver->load("user");

        return view("drivers.edit", compact("driver"));
    }

    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "email",
                "max:255",
                Rule::unique("users", "email")->ignore($driver->user_id),
            ],
            "password" => ["nullable", "string", "min:8", "confirmed"],
            "license_no" => [
                "required",
                "string",
                "max:50",
                Rule::unique("drivers", "license_no")->ignore($driver->id),
            ],
            "license_expiry" => ["required", "date"],
            "contact_no" => ["required", "string", "max:20"],
            "address" => ["required", "string"],
            "status" => [
                "required",
                Rule::in(["available", "on_trip", "off_duty"]),
            ],
        ]);

        DB::transaction(function () use ($data, $driver) {
            $user = $driver->user;
            $user->name = $data["name"];
            $user->email = $data["email"];
            if (!empty($data["password"])) {
                $user->password = Hash::make($data["password"]);
            }
            $user->save();

            $driver->update([
                "license_no" => $data["license_no"],
                "license_expiry" => $data["license_expiry"],
                "contact_no" => $data["contact_no"],
                "address" => $data["address"],
                "status" => $data["status"],
            ]);
        });

        return redirect()
            ->route("drivers.index")
            ->with("status", "Driver updated.");
    }

    public function destroy(Driver $driver)
    {
        try {
            // Deleting the user also removes the driver row (ON DELETE CASCADE).
            $driver->user->delete();
        } catch (QueryException $e) {
            return back()->withErrors([
                "delete" =>
                    "This driver still has schedules, so it cannot be deleted. Set the status to inactive instead.",
            ]);
        }

        return redirect()
            ->route("drivers.index")
            ->with("status", "Driver deleted.");
    }

    public function show(Driver $driver)
    {
        $driver->load("user");
        $trips = Schedule::with("truck")
            ->where("driver_id", $driver->id)
            ->latest()
            ->take(5)
            ->get();

        $declines = DeliveryStatusLog::where(
            "status",
            "needs_reassignment",
        )->where("logged_by", $driver->user_id);
        $declineCount = (clone $declines)->count();
        $lateDeclineCount = (clone $declines)->where("is_late", true)->count();

        return view(
            "drivers.show",
            compact("driver", "trips", "declineCount", "lateDeclineCount"),
        );
    }
}
