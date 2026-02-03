<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\ParentModel;
use App\Models\Absence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * ダッシュボードの統計情報を取得
     */
    public function stats()
    {
        $admin = Auth::guard('admin')->user();
        
        // スーパー管理者の場合は全体の統計
        if ($admin && $admin->is_super_admin) {
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
            
            // 本日の遅刻数
            $todayLate = Absence::whereDate('absence_date', Carbon::today())
                               ->where('division', '遅刻')
                               ->count();
        } else {
            // 担任の場合は自分のクラスのみ
            $classCount = 1; // 自分のクラスのみ
            
            // 自分のクラスの生徒数
            $studentCount = Student::where('class_id', $admin->class_id)->count();
            
            // 自分のクラスの生徒の保護者数
            $parentCount = ParentModel::whereIn('seito_id', function($query) use ($admin) {
                $query->select('seito_id')
                      ->from('students')
                      ->where('class_id', $admin->class_id);
            })->count();
            
            // 本日の自分のクラスの欠席数
            $todayAbsences = Absence::whereDate('absence_date', Carbon::today())
                                   ->where('division', '欠席')
                                   ->whereHas('student', function($q) use ($admin) {
                                       $q->where('class_id', $admin->class_id);
                                   })
                                   ->count();
            
            // 本日の自分のクラスの遅刻数
            $todayLate = Absence::whereDate('absence_date', Carbon::today())
                               ->where('division', '遅刻')
                               ->whereHas('student', function($q) use ($admin) {
                                   $q->where('class_id', $admin->class_id);
                               })
                               ->count();
        }

        return response()->json([
            'class_count' => $classCount,
            'student_count' => $studentCount,
            'parent_count' => $parentCount,
            'today_absences' => $todayAbsences,
            'today_late' => $todayLate,
            'is_super_admin' => $admin->is_super_admin ?? false,
            'class_name' => $admin->classModel->class_name ?? null,
        ]);
    }
}
