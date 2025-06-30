<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = '$1Password;';
        $users = [
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'username' => 'admin',
                'phone' => '+2340000000000',
                'email' => 'admin@example.com',
                'password' => $password,
            ],
            [
                'first_name' => 'Demo',
                'last_name' => 'User',
                'username' => 'demo',
                'phone' => '+2340000000001',
                'email' => 'demo@example.com',
                'password' => $password,
            ],
        ];

        foreach ($users as $user) {
            $existing = User::withTrashed()->where('username', $user['username'])
                ->orWhere('phone', $user['phone'])
                ->orWhere('email', $user['email'])->exists();
            if ($existing) {
                continue; // Skip creating a user that already exists in the database
            }

            User::create($user);
        }
    }
}
