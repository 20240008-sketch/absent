<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    /**
     * 本日の欠席一覧取得
     */
    public function today()
    {
        $absences = Absence::with(['student.classModel'])
            ->whereDate('absence_date', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($absences);
    }

    /**
     * 欠席一覧取得（日付指定可能）
     */
    public function index(Request $request)
    {
        $query = Absence::with(['student.classModel']);

        // 日付でフィルタ
        if ($request->has('date')) {
            $query->whereDate('absence_date', $request->date);
        }

        // 区分でフィルタ
        if ($request->has('division')) {
            $query->where('division', $request->division);
        }

        // 学年でフィルタ
        if ($request->has('grade')) {
            $grade = $request->grade;
            $query->whereHas('student.classModel', function ($q) use ($grade) {
                $q->where('class_name', 'LIKE', $grade . '%');
            });
        }

        // クラスでフィルタ
        if ($request->has('class_name')) {
            $query->whereHas('student.classModel', function ($q) use ($request) {
                $q->where('class_name', $request->class_name);
            });
        }

        $absences = $query->orderBy('absence_date', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 20));

        return response()->json($absences);
    }

    /**
     * 欠席統計情報取得
     */
    public function stats()
    {
        // 本日の欠席数
        $today = Absence::whereDate('absence_date', Carbon::today())
                       ->where('division', '欠席')
                       ->count();

        // 今週の欠席数（月曜日〜日曜日）
        $week = Absence::whereBetween('absence_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->where('division', '欠席')
                ->count();

        // 今月の欠席数
        $month = Absence::whereYear('absence_date', Carbon::now()->year)
                       ->whereMonth('absence_date', Carbon::now()->month)
                       ->where('division', '欠席')
                       ->count();

        // 総欠席数
        $total = Absence::where('division', '欠席')->count();

        return response()->json([
            'today' => $today,
            'week' => $week,
            'month' => $month,
            'total' => $total,
        ]);
    }

    /**
     * 月別欠席統計取得
     */
    public function monthly()
    {
        $currentYear = Carbon::now()->year;
        $monthlyData = [];

        // 過去6ヶ月分のデータを取得
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Absence::whereYear('absence_date', $date->year)
                           ->whereMonth('absence_date', $date->month)
                           ->where('division', '欠席')
                           ->count();

            $monthlyData[] = [
                'month' => $date->format('Y年n月'),
                'count' => $count
            ];
        }

        return response()->json($monthlyData);
    }

    /**
     * 欠席詳細取得
     */
    public function show($id)
    {
        $absence = Absence::with(['student.classModel'])->findOrFail($id);
        return response()->json($absence);
    }
}
