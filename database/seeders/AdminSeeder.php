<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(['email' => config('app.admin_email')], [
            'id' => config('app.admin_id'),
            'name' => config('app.admin_name'),
            'phone' => config('app.admin_phone'),
            'password' => config('app.admin_password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        \Helper::afterNewUserCreated($user);
    }
}
