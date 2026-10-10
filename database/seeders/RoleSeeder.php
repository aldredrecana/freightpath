<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (["admin", "dispatcher", "driver"] as $name) {
            \App\Models\Role::firstOrCreate(["name" => $name]);
        }
    }
}
