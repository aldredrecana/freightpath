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

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::with('user')->latest()->paginate(10);

        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'license_no'     => ['required', 'string', 'max:50', 'unique:drivers,license_no'],
            'license_expiry' => ['required', 'date', 'after:today'],
            'contact_no'     => ['required', 'string', 'max:20'],
            'address'        => ['required', 'string'],
            'status'         => ['required', Rule::in(['available', 'on_trip', 'off_duty'])],
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id'  => Role::where('name', 'driver')->value('id'),
            ]);

            $user->driver()->create([
                'license_no'     => $data['license_no'],
                'license_expiry' => $data['license_expiry'],
                'contact_no'     => $data['contact_no'],
                'address'        => $data['address'],
                'status'         => $data['status'],
            ]);
        });

        return redirect()->route('drivers.index')->with('status', 'Driver added.');
    }

    public function edit(Driver $driver)
    {
        $driver->load('user');

        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($driver->user_id)],
            'password'       => ['nullable', 'string', 'min:8', 'confirmed'],
            'license_no'     => ['required', 'string', 'max:50', Rule::unique('drivers', 'license_no')->ignore($driver->id)],
            'license_expiry' => ['required', 'date'],
            'contact_no'     => ['required', 'string', 'max:20'],
            'address'        => ['required', 'string'],
            'status'         => ['required', Rule::in(['available', 'on_trip', 'off_duty'])],
        ]);

        DB::transaction(function () use ($data, $driver) {
            $user = $driver->user;
            $user->name  = $data['name'];
            $user->email = $data['email'];
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->save();

            $driver->update([
                'license_no'     => $data['license_no'],
                'license_expiry' => $data['license_expiry'],
                'contact_no'     => $data['contact_no'],
                'address'        => $data['address'],
                'status'         => $data['status'],
            ]);
        });

        return redirect()->route('drivers.index')->with('status', 'Driver updated.');
    }

    public function destroy(Driver $driver)
    {
        try {
            // Deleting the user also removes the driver row (ON DELETE CASCADE).
            $driver->user->delete();
        } catch (QueryException $e) {
            return back()->withErrors(['delete' => 'This driver still has schedules, so it cannot be deleted. Set the status to inactive instead.']);
        }

        return redirect()->route('drivers.index')->with('status', 'Driver deleted.');
    }
}