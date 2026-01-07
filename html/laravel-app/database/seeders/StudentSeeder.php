<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            // 1年A組
            ['seito_id' => 1001, 'seito_name' => '山田太郎', 'seito_number' => 1, 'class_id' => 'A101'],
            ['seito_id' => 1002, 'seito_name' => '田中花子', 'seito_number' => 2, 'class_id' => 'A101'],
            ['seito_id' => 1003, 'seito_name' => '佐藤次郎', 'seito_number' => 3, 'class_id' => 'A101'],
            ['seito_id' => 1004, 'seito_name' => '鈴木美咲', 'seito_number' => 4, 'class_id' => 'A101'],
            ['seito_id' => 1005, 'seito_name' => '高橋健太', 'seito_number' => 5, 'class_id' => 'A101'],
            
            // 1年B組
            ['seito_id' => 1006, 'seito_name' => '伊藤さくら', 'seito_number' => 1, 'class_id' => 'A102'],
            ['seito_id' => 1007, 'seito_name' => '渡辺大輔', 'seito_number' => 2, 'class_id' => 'A102'],
            ['seito_id' => 1008, 'seito_name' => '中村愛', 'seito_number' => 3, 'class_id' => 'A102'],
            ['seito_id' => 1009, 'seito_name' => '小林翔太', 'seito_number' => 4, 'class_id' => 'A102'],
            ['seito_id' => 1010, 'seito_name' => '加藤結衣', 'seito_number' => 5, 'class_id' => 'A102'],
            
            // 2年A組
            ['seito_id' => 2001, 'seito_name' => '吉田陽介', 'seito_number' => 1, 'class_id' => 'A201'],
            ['seito_id' => 2002, 'seito_name' => '山本優奈', 'seito_number' => 2, 'class_id' => 'A201'],
            ['seito_id' => 2003, 'seito_name' => '岡田浩二', 'seito_number' => 3, 'class_id' => 'A201'],
            
            // 2年B組
            ['seito_id' => 2004, 'seito_name' => '松本美咲', 'seito_number' => 1, 'class_id' => 'A202'],
            ['seito_id' => 2005, 'seito_name' => '井上太一', 'seito_number' => 2, 'class_id' => 'A202'],
            
            // 3年A組
            ['seito_id' => 3001, 'seito_name' => '木村里奈', 'seito_number' => 1, 'class_id' => 'A301'],
            ['seito_id' => 3002, 'seito_name' => '林大樹', 'seito_number' => 2, 'class_id' => 'A301'],
            ['seito_id' => 3003, 'seito_name' => '斉藤葵', 'seito_number' => 3, 'class_id' => 'A301'],
        ];

        foreach ($students as $student) {
            Student::create($student);
        }
    }
}
