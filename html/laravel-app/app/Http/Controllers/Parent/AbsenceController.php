<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAbsenceRequest;
use App\Http\Requests\UpdateAbsenceRequest;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    /**
     * 欠席連絡一覧取得（自分の子供のもののみ）
     */
    public function index(Request $request)
    {
        $parent = Auth::guard('parent')->user();
        
        $query = Absence::where('seito_id', $parent->seito_id)
            ->with('student');

        // 検索条件
        if ($request->filled('division')) {
            $query->where('division', $request->division);
        }

        if ($request->filled('date_from')) {
            $query->where('absence_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('absence_date', '<=', $request->date_to);
        }

        // ソート
        $query->orderBy('absence_date', 'desc')
              ->orderBy('created_at', 'desc');

        $absences = $query->paginate(20);

        return response()->json($absences);
    }

    /**
     * 欠席連絡登録
     */
    public function store(StoreAbsenceRequest $request)
    {
        $parent = Auth::guard('parent')->user();

        // 自分の子供の欠席連絡のみ登録可能
        if ($request->seito_id !== $parent->seito_id) {
            return response()->json([
                'message' => '他の生徒の欠席連絡は登録できません。',
            ], 403);
        }

        $absence = Absence::create($request->validated());

        return response()->json([
            'message' => '欠席連絡を登録しました。',
            'absence' => $absence->load('student'),
        ], 201);
    }

    /**
     * 欠席連絡詳細取得
     */
    public function show(string $id)
    {
        $parent = Auth::guard('parent')->user();
        
        $absence = Absence::with('student')->findOrFail($id);

        // 自分の子供の欠席連絡のみ閲覧可能
        if ($absence->seito_id !== $parent->seito_id) {
            return response()->json([
                'message' => '他の生徒の欠席連絡は閲覧できません。',
            ], 403);
        }

        return response()->json($absence);
    }

    /**
     * 欠席連絡更新
     */
    public function update(UpdateAbsenceRequest $request, string $id)
    {
        $parent = Auth::guard('parent')->user();
        
        $absence = Absence::findOrFail($id);

        // 自分の子供の欠席連絡のみ更新可能
        if ($absence->seito_id !== $parent->seito_id) {
            return response()->json([
                'message' => '他の生徒の欠席連絡は更新できません。',
            ], 403);
        }

        $absence->update($request->validated());

        return response()->json([
            'message' => '欠席連絡を更新しました。',
            'absence' => $absence->load('student'),
        ]);
    }

    /**
     * 欠席連絡削除
     */
    public function destroy(string $id)
    {
        $parent = Auth::guard('parent')->user();
        
        $absence = Absence::findOrFail($id);

        // 自分の子供の欠席連絡のみ削除可能
        if ($absence->seito_id !== $parent->seito_id) {
            return response()->json([
                'message' => '他の生徒の欠席連絡は削除できません。',
            ], 403);
        }

        $absence->delete();

        return response()->json([
            'message' => '欠席連絡を削除しました。',
        ]);
    }
}
