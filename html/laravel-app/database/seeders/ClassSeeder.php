<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');
        
        $classes = [
            ['class_id' => 'A101', 'class_name' => '1年A組', 'teacher_name' => '田中太郎', 'teacher_email' => 'tanaka@example.com', 'year_id' => $currentYear],
            ['class_id' => 'A102', 'class_name' => '1年B組', 'teacher_name' => '佐藤花子', 'teacher_email' => 'sato@example.com', 'year_id' => $currentYear],
            ['class_id' => 'A201', 'class_name' => '2年A組', 'teacher_name' => '鈴木一郎', 'teacher_email' => 'suzuki@example.com', 'year_id' => $currentYear],
            ['class_id' => 'A202', 'class_name' => '2年B組', 'teacher_name' => '高橋美咲', 'teacher_email' => 'takahashi@example.com', 'year_id' => $currentYear],
            ['class_id' => 'A301', 'class_name' => '3年A組', 'teacher_name' => '渡辺健', 'teacher_email' => 'watanabe@example.com', 'year_id' => $currentYear],
        ];

        foreach ($classes as $class) {
            ClassModel::create($class);
        }
    }
}
