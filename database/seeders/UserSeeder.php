<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name'        => 'Admin',
            'last_name'         => 'Admin',
            'email'             => config('constant.ADMIN_EMAIL'),
            'password'          => Hash::make(config('constant.ADMIN_PASSWORD')),
            'role'              => config('constant.USER_ROLE.admin'),
            'status'            => config('constant.STATUS.approved'),
        ]);
    }
}
