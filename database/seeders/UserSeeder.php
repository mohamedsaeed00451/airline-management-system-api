<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();

        User::query()->create([
            'name' => 'أوشــــن هـــارت',
            'email' => 'admin@oceanheart.com',
            'email_verified_at' => now(),
            'password' => Hash::make(12345678)
        ]);
    }
}
