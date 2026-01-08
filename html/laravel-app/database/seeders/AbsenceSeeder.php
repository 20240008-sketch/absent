<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absence;
use App\Models\Student;
use Carbon\Carbon;

class AbsenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 生徒データを取得
        $students = Student::all();
        
        if ($students->isEmpty()) {
            $this->command->warn('生徒データが存在しません。先にStudentSeederを実行してください。');
            return;
        }

        // 各種理由のパターン
        $reasons = [
            '体調不良のため',
            '発熱のため',
            '腹痛のため',
            '頭痛のため',
            '家庭の事情により',
            '通院のため',
            '忌引きのため',
            '風邪のため',
            'インフルエンザのため',
            '急な用事のため',
        ];

        $divisions = ['欠席', '遅刻', '早退'];
        $scheduledTimes = ['1限目', '2限目', '3限目', '4限目', '5限目', '6限目', '午前中', '午後', null];

        // 最近30日分のテストデータを作成
        $absences = [];
        
        // ランダムに15件の欠席データを作成
        for ($i = 0; $i < 15; $i++) {
            $student = $students->random();
            $division = $divisions[array_rand($divisions)];
            $absenceDate = Carbon::now()->subDays(rand(0, 30));
            
            $absences[] = [
                'seito_id' => $student->seito_id,
                'division' => $division,
                'reason' => $reasons[array_rand($reasons)],
                'scheduled_time' => $division === '欠席' ? null : $scheduledTimes[array_rand($scheduledTimes)],
                'absence_date' => $absenceDate->format('Y-m-d'),
                'created_at' => $absenceDate,
                'updated_at' => $absenceDate,
            ];
        }

        // 特定の生徒の履歴を作成（テスト用）
        $testStudent = Student::where('seito_id', 1001)->first();
        if ($testStudent) {
            // 山田太郎の欠席履歴
            $absences[] = [
                'seito_id' => $testStudent->seito_id,
                'division' => '欠席',
                'reason' => '発熱のため（38.5度）',
                'scheduled_time' => null,
                'absence_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ];
            
            $absences[] = [
                'seito_id' => $testStudent->seito_id,
                'division' => '遅刻',
                'reason' => '寝坊のため',
                'scheduled_time' => '1限目',
                'absence_date' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ];
            
            $absences[] = [
                'seito_id' => $testStudent->seito_id,
                'division' => '早退',
                'reason' => '体調不良のため',
                'scheduled_time' => '5限目',
                'absence_date' => Carbon::now()->subDays(14)->format('Y-m-d'),
                'created_at' => Carbon::now()->subDays(14),
                'updated_at' => Carbon::now()->subDays(14),
            ];
        }

        foreach ($absences as $absence) {
            Absence::create($absence);
        }

        $this->command->info('欠席データを ' . count($absences) . ' 件作成しました。');
    }
}
