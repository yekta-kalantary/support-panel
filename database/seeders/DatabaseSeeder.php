<?php

namespace Database\Seeders;

use App\Enums\RecordStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => strtolower((string) config('support.admin.email'))],
            [
                'first_name' => config('support.admin.first_name'),
                'last_name' => config('support.admin.last_name'),
                'mobile' => config('support.admin.mobile'),
                'password' => config('support.admin.password'),
                'role' => UserRole::ADMIN,
                'status' => RecordStatus::ACTIVE,
                'email_verified_at' => now(),
            ],
        );
    }
}
