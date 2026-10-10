<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Schedule;

class TruckController extends Controller
{
    private const STATUSES = ["available", "on_trip", "maintenance"];

    public function index(Request $request)
    {
        $q = trim((string) $request->query("q", ""));
        $status = $request->query("status");

        $trucks = Truck::query()
            ->when($q !== "", function ($query) use ($q) {
                $like = "%" . $q . "%";
                $query->where(
                    fn($w) => $w
                        ->where("plate_no", "ilike", $like)
                        ->orWhere("model", "ilike", $like),
                );
            })
            ->when(
                in_array($status, self::STATUSES, true),
                fn($query) => $query->where("status", $status),
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = Truck::selectRaw("status, count(*) as total")
            ->groupBy("status")
            ->pluck("total", "status");

        return view("trucks.index", compact("trucks", "counts", "q", "status"));
    }

    public function show(Truck $truck)
    {
        $trips = Schedule::with("driver.user")
            ->where("truck_id", $truck->id)
            ->latest()
            ->take(5)
            ->get();

        return view("trucks.show", compact("truck", "trips"));
    }
    public function create()
    {
        return view("trucks.create");
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "plate_no" => [
                "required",
                "string",
                "max:20",
                "unique:trucks,plate_no",
            ],
            "model" => ["required", "string", "max:255"],
            "capacity_kg" => ["required", "integer", "min:1"],
            "status" => ["required", Rule::in(self::STATUSES)],
        ]);

        Truck::create($data);

        return redirect()
            ->route("trucks.index")
            ->with("status", "Truck added.");
    }

    public function edit(Truck $truck)
    {
        return view("trucks.edit", compact("truck"));
    }

    public function update(Request $request, Truck $truck)
    {
        $data = $request->validate([
            "plate_no" => [
                "required",
                "string",
                "max:20",
                Rule::unique("trucks", "plate_no")->ignore($truck->id),
            ],
            "model" => ["required", "string", "max:255"],
            "capacity_kg" => ["required", "integer", "min:1"],
            "status" => ["required", Rule::in(self::STATUSES)],
        ]);

        $truck->update($data);

        return redirect()
            ->route("trucks.index")
            ->with("status", "Truck updated.");
    }

    public function destroy(Truck $truck)
    {
        try {
            $truck->delete();
        } catch (QueryException $e) {
            return back()->withErrors([
                "delete" =>
                    "This truck still has schedules, so it cannot be deleted. Set the status to maintenance instead.",
            ]);
        }

        return redirect()
            ->route("trucks.index")
            ->with("status", "Truck deleted.");
    }
}
