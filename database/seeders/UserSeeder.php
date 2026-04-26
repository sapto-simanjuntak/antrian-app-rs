<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Administrator',
                'email'    => 'admin@rssehat.com',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'loket_id' => null,
            ],
            [
                'name'     => 'Operator Loket 1',
                'email'    => 'loket1@rssehat.com',
                'password' => Hash::make('loket1234'),
                'role'     => 'operator',
                'loket_id' => 1,
            ],
            [
                'name'     => 'Operator Loket 2',
                'email'    => 'loket2@rssehat.com',
                'password' => Hash::make('loket1234'),
                'role'     => 'operator',
                'loket_id' => 2,
            ],
            [
                'name'     => 'Operator Loket 3',
                'email'    => 'loket3@rssehat.com',
                'password' => Hash::make('loket1234'),
                'role'     => 'operator',
                'loket_id' => 3,
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(['email' => $u['email']], $u);
        }

        $this->command->info('✅ Users seeded:');
        $this->command->table(
            ['Name', 'Email', 'Password', 'Role', 'Loket'],
            collect($users)->map(fn($u) => [
                $u['name'],
                $u['email'],
                $u['email'] === 'admin@rssehat.com' ? 'admin123' : 'loket1234',
                $u['role'],
                $u['loket_id'] ?? 'semua',
            ])->toArray()
        );
    }
}
