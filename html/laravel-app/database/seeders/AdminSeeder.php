<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('seiei2026'),
        ]);

        Admin::create([
            'name' => '副管理者',
            'email' => 'admin2@example.com',
            'password' => Hash::make('seiei2026'),
        ]);
    }
}
