<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevUserSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['name' => 'Demo'],
            ['active_location_name' => null]
        );

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
