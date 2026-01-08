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
     * 欠席詳細取得
     */
    public function show($id)
    {
        $absence = Absence::with(['student.classModel'])->findOrFail($id);
        return response()->json($absence);
    }
}
