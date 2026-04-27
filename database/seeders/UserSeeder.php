<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Password default kuat — wajib diganti setelah instalasi pertama.
        // Format: Rs@<NamaLoket><Tahun> agar mudah diingat tapi tidak mudah ditebak.
        $year = now()->format('Y');

        $users = [
            [
                'name'          => 'Administrator',
                'email'         => 'admin@rssehat.com',
                'password'      => Hash::make("Admin@RS{$year}!"),
                'display_pass'  => "Admin@RS{$year}!",
                'role'          => 'admin',
                'loket_id'      => null,
            ],
            [
                'name'          => 'Operator Loket 1',
                'email'         => 'loket1@rssehat.com',
                'password'      => Hash::make("Loket1@RS{$year}!"),
                'display_pass'  => "Loket1@RS{$year}!",
                'role'          => 'operator',
                'loket_id'      => 1,
            ],
            [
                'name'          => 'Operator Loket 2',
                'email'         => 'loket2@rssehat.com',
                'password'      => Hash::make("Loket2@RS{$year}!"),
                'display_pass'  => "Loket2@RS{$year}!",
                'role'          => 'operator',
                'loket_id'      => 2,
            ],
            [
                'name'          => 'Operator Loket 3',
                'email'         => 'loket3@rssehat.com',
                'password'      => Hash::make("Loket3@RS{$year}!"),
                'display_pass'  => "Loket3@RS{$year}!",
                'role'          => 'operator',
                'loket_id'      => 3,
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                collect($u)->except('display_pass')->toArray()
            );
        }

        $this->command->warn('⚠️  HARAP GANTI PASSWORD SETELAH LOGIN PERTAMA!');
        $this->command->info('✅ Users seeded:');
        $this->command->table(
            ['Name', 'Email', 'Password Default', 'Role', 'Loket'],
            collect($users)->map(fn($u) => [
                $u['name'],
                $u['email'],
                $u['display_pass'],
                $u['role'],
                $u['loket_id'] ?? 'semua',
            ])->toArray()
        );
    }
}
