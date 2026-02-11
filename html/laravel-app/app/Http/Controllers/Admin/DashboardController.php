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
        
        if (!$admin) {
            return response()->json(['error' => '認証エラー'], 401);
        }
        
        // スーパー管理者の場合は全体の統計
        if ($admin->is_super_admin) {
            // クラス数
            $classCount = ClassModel::count();
            
            // 生徒数
            $studentCount = Student::count();
            
            // 保護者数
            $parentCount = ParentModel::count();
            
            // 本日の欠席数
            $todayAbsences = Absence::where('is_deleted', false)
                                   ->whereDate('absence_date', Carbon::today())
                                   ->where('division', '欠席')
                                   ->count();
            
            // 本日の遅刻数
            $todayLate = Absence::where('is_deleted', false)
                               ->whereDate('absence_date', Carbon::today())
                               ->where('division', '遅刻')
                               ->count();
        } else {
            // 担任の場合は自分のクラスのみ
            if (!$admin->class_id) {
                // クラスが割り当てられていない場合
                return response()->json([
                    'class_count' => 0,
                    'student_count' => 0,
                    'parent_count' => 0,
                    'today_absences' => 0,
                    'today_late' => 0,
                    'is_super_admin' => false,
                    'class_name' => null,
                    'warning' => 'クラスが割り当てられていません',
                ]);
            }
            
            $classCount = 1; // 自分のクラスのみ
            
            // 自分のクラスの生徒IDリストを取得
            $studentIds = Student::where('class_id', $admin->class_id)
                                ->pluck('seito_id')
                                ->toArray();
            
            // 自分のクラスの生徒数
            $studentCount = count($studentIds);
            
            // 自分のクラスの生徒の保護者数
            $parentCount = 0;
            if (!empty($studentIds)) {
                $parentCount = ParentModel::whereIn('seito_id', $studentIds)->count();
            }
            
            // 本日の自分のクラスの欠席数
            $todayAbsences = 0;
            if (!empty($studentIds)) {
                $todayAbsences = Absence::where('is_deleted', false)
                                       ->whereDate('absence_date', Carbon::today())
                                       ->where('division', '欠席')
                                       ->whereIn('seito_id', $studentIds)
                                       ->count();
            }
            
            // 本日の自分のクラスの遅刻数
            $todayLate = 0;
            if (!empty($studentIds)) {
                $todayLate = Absence::where('is_deleted', false)
                                   ->whereDate('absence_date', Carbon::today())
                                   ->where('division', '遅刻')
                                   ->whereIn('seito_id', $studentIds)
                                   ->count();
            }
        }

        return response()->json([
            'class_count' => $classCount ?? 0,
            'student_count' => $studentCount ?? 0,
            'parent_count' => $parentCount ?? 0,
            'today_absences' => $todayAbsences ?? 0,
            'today_late' => $todayLate ?? 0,
            'is_super_admin' => $admin->is_super_admin ?? false,
            'class_name' => $admin->classModel->class_name ?? null,
        ]);
    }
}
