<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@elbilletazoperu.bet'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin@elbilletazoperu.bet'),
                'rol' => 'superadmin',
                'activo' => true,
            ]
        );
    }
}