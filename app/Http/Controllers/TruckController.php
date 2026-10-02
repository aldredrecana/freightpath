<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TruckController extends Controller
{
    private const STATUSES = ['available', 'on_trip', 'maintenance'];

    public function index()
    {
        $trucks = Truck::latest()->paginate(10);

        return view('trucks.index', compact('trucks'));
    }

    public function create()
    {
        return view('trucks.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plate_no'    => ['required', 'string', 'max:20', 'unique:trucks,plate_no'],
            'model'       => ['required', 'string', 'max:255'],
            'capacity_kg' => ['required', 'integer', 'min:1'],
            'status'      => ['required', Rule::in(self::STATUSES)],
        ]);

        Truck::create($data);

        return redirect()->route('trucks.index')->with('status', 'Truck added.');
    }

    public function edit(Truck $truck)
    {
        return view('trucks.edit', compact('truck'));
    }

    public function update(Request $request, Truck $truck)
    {
        $data = $request->validate([
            'plate_no'    => ['required', 'string', 'max:20', Rule::unique('trucks', 'plate_no')->ignore($truck->id)],
            'model'       => ['required', 'string', 'max:255'],
            'capacity_kg' => ['required', 'integer', 'min:1'],
            'status'      => ['required', Rule::in(self::STATUSES)],
        ]);

        $truck->update($data);

        return redirect()->route('trucks.index')->with('status', 'Truck updated.');
    }

    public function destroy(Truck $truck)
    {
        try {
            $truck->delete();
        } catch (QueryException $e) {
            return back()->withErrors(['delete' => 'This truck still has schedules, so it cannot be deleted. Set the status to maintenance instead.']);
        }

        return redirect()->route('trucks.index')->with('status', 'Truck deleted.');
    }
}