<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\ParentModel;

class ParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parents = [
            // 1年A組の保護者
            [
                'seito_id' => 1001,
                'parent_name' => '山田一郎',
                'parent_relationship' => '父',
                'parent_tel' => '090-1234-5601',
                'parent_initial_email' => 'yamada@example.com',
                'parent_initial_password' => Hash::make('password123'),
                'parent_email' => 'yamada@example.com',
                'parent_password' => Hash::make('password123'),
            ],
            [
                'seito_id' => 1002,
                'parent_name' => '田中良子',
                'parent_relationship' => '母',
                'parent_tel' => '090-1234-5602',
                'parent_initial_email' => 'tanaka@example.com',
                'parent_initial_password' => Hash::make('password123'),
                'parent_email' => 'tanaka@example.com',
                'parent_password' => Hash::make('password123'),
            ],
            [
                'seito_id' => 1003,
                'parent_name' => '佐藤健',
                'parent_relationship' => '父',
                'parent_tel' => '090-1234-5603',
                'parent_initial_email' => 'sato@example.com',
                'parent_initial_password' => Hash::make('password123'),
                'parent_email' => 'sato@example.com',
                'parent_password' => Hash::make('password123'),
            ],
            [
                'seito_id' => 1004,
                'parent_name' => '鈴木由美',
                'parent_relationship' => '母',
                'parent_tel' => '090-1234-5604',
                'parent_initial_email' => 'suzuki@example.com',
                'parent_initial_password' => Hash::make('password123'),
                'parent_email' => 'suzuki@example.com',
                'parent_password' => Hash::make('password123'),
            ],
            [
                'seito_id' => 1005,
                'parent_name' => '高橋誠',
                'parent_relationship' => '父',
                'parent_tel' => '090-1234-5605',
                'parent_initial_email' => 'takahashi-p@example.com',
                'parent_initial_password' => Hash::make('password123'),
                'parent_email' => 'takahashi-p@example.com',
                'parent_password' => Hash::make('password123'),
            ],
            
            // 1年B組の保護者
            [
                'seito_id' => 1006,
                'parent_name' => '伊藤明',
                'parent_relationship' => '父',
                'parent_tel' => '090-1234-5606',
                'parent_initial_email' => 'ito@example.com',
                'parent_initial_password' => Hash::make('password123'),
                'parent_email' => 'ito@example.com',
                'parent_password' => Hash::make('password123'),
            ],
            [
                'seito_id' => 1007,
                'parent_name' => '渡辺京子',
                'parent_relationship' => '母',
                'parent_tel' => '090-1234-5607',
                'parent_initial_email' => 'watanabe-p@example.com',
                'parent_initial_password' => Hash::make('password123'),
                'parent_email' => 'watanabe-p@example.com',
                'parent_password' => Hash::make('password123'),
            ],
            
            // 2年A組の保護者
            [
                'seito_id' => 2001,
                'parent_name' => '吉田雅彦',
                'parent_relationship' => '父',
                'parent_tel' => '090-1234-5608',
                'parent_initial_email' => 'yoshida@example.com',
                'parent_initial_password' => Hash::make('password123'),
                'parent_email' => 'yoshida@example.com',
                'parent_password' => Hash::make('password123'),
            ],
            
            // 3年A組の保護者
            [
                'seito_id' => 3001,
                'parent_name' => '木村浩',
                'parent_relationship' => '父',
                'parent_tel' => '090-1234-5609',
                'parent_initial_email' => 'kimura@example.com',
                'parent_initial_password' => Hash::make('password123'),
                'parent_email' => 'kimura@example.com',
                'parent_password' => Hash::make('password123'),
            ],
        ];

        foreach ($parents as $parent) {
            ParentModel::create($parent);
        }
    }
}
