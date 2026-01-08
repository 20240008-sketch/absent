<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\Absence;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * ダッシュボードの統計情報を取得
     */
    public function stats()
    {
        // クラス数
        $classCount = ClassModel::count();
        
        // 生徒数
        $studentCount = Student::count();
        
        // 保護者数
        $parentCount = ParentModel::count();
        
        // 本日の欠席数
        $todayAbsences = Absence::whereDate('absence_date', Carbon::today())
                               ->where('division', '欠席')
                               ->count();

        return response()->json([
            'class_count' => $classCount,
            'student_count' => $studentCount,
            'parent_count' => $parentCount,
            'today_absences' => $todayAbsences,
        ]);
    }
}
