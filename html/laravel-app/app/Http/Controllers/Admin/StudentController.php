<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * 生徒一覧取得
     */
    public function index(Request $request)
    {
        $query = Student::with('classModel');

        // 検索
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('seito_name', 'like', "%{$search}%")
                  ->orWhere('seito_id', 'like', "%{$search}%");
            });
        }

        // クラスでフィルタ
        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // ページネーション
        $students = $query->orderBy('class_id')
                         ->orderBy('seito_number')
                         ->paginate($request->get('per_page', 20));

        return response()->json($students);
    }

    /**
     * 生徒登録
     */
    public function store(StoreStudentRequest $request)
    {
        $student = Student::create($request->validated());
        $student->load('classModel');

        return response()->json([
            'message' => '生徒を登録しました',
            'student' => $student,
        ], 201);
    }

    /**
     * 生徒詳細取得
     */
    public function show($id)
    {
        $student = Student::with(['classModel', 'parents', 'absences'])
                         ->findOrFail($id);

        return response()->json($student);
    }

    /**
     * 生徒更新
     */
    public function update(UpdateStudentRequest $request, $id)
    {
        $student = Student::findOrFail($id);
        $student->update($request->validated());
        $student->load('classModel');

        return response()->json([
            'message' => '生徒を更新しました',
            'student' => $student,
        ]);
    }

    /**
     * 生徒削除
     */
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        
        // 保護者や欠席連絡は外部キー制約でカスケード削除される
        $student->delete();

        return response()->json([
            'message' => '生徒を削除しました',
        ]);
    }
}
