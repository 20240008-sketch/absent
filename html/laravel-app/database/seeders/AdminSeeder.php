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
        // スーパー管理者
        Admin::create([
            'name' => 'スーパー管理者',
            'email' => 'seiei2026',
            'password' => Hash::make('0000'),
            'class_id' => null,
            'is_super_admin' => true,
        ]);

        Admin::create([
            'name' => '管理者',
            'email' => 'admin@seiei.ac.jp',
            'password' => Hash::make('seiei2026'),
            'class_id' => null,
            'is_super_admin' => true,
        ]);

        Admin::create([
            'name' => '副管理者',
            'email' => 'admin2@example.com',
            'password' => Hash::make('seiei2026'),
            'class_id' => null,
            'is_super_admin' => false,
        ]);
    }
}
