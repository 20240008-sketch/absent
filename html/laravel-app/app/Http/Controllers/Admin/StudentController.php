<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * 生徒一覧取得
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $query = Student::with('classModel');

        // 担任の場合は自分のクラスの生徒のみに制限
        if (!$admin->is_super_admin) {
            $query->where('class_id', $admin->class_id);
        }

        // 検索
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('seito_name', 'like', "%{$search}%")
                  ->orWhere('seito_id', 'like', "%{$search}%");
            });
        }

        // クラスでフィルタ（class_nameで検索）
        if ($request->has('class_name')) {
            $className = $request->class_name;
            $query->whereHas('classModel', function ($q) use ($className) {
                $q->where('class_name', $className);
            });
        }
        
        // 旧形式のclass_idでのフィルタも残す（互換性のため）
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
        $admin = Auth::guard('admin')->user();
        
        // 担任の場合は自分のクラスにのみ登録可能
        $data = $request->validated();
        if (!$admin->is_super_admin) {
            $data['class_id'] = $admin->class_id;
        }
        
        $student = Student::create($data);
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
        $admin = Auth::guard('admin')->user();
        $query = Student::with(['classModel', 'parents', 'absences']);
        
        // 担任の場合は自分のクラスの生徒のみアクセス可能
        if (!$admin->is_super_admin) {
            $query->where('class_id', $admin->class_id);
        }
        
        $student = $query->findOrFail($id);

        return response()->json($student);
    }

    /**
     * 生徒更新
     */
    public function update(UpdateStudentRequest $request, $id)
    {
        $admin = Auth::guard('admin')->user();
        $query = Student::query();
        
        // 担任の場合は自分のクラスの生徒のみ更新可能
        if (!$admin->is_super_admin) {
            $query->where('class_id', $admin->class_id);
        }
        
        $student = $query->findOrFail($id);
        
        $data = $request->validated();
        // 担任の場合はクラス変更を防ぐ
        if (!$admin->is_super_admin) {
            $data['class_id'] = $admin->class_id;
        }
        
        $student->update($data);
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
        $admin = Auth::guard('admin')->user();
        $query = Student::query();
        
        // 担任の場合は自分のクラスの生徒のみ削除可能
        if (!$admin->is_super_admin) {
            $query->where('class_id', $admin->class_id);
        }
        
        $student = $query->findOrFail($id);
        
        // 保護者や欠席連絡は外部キー制約でカスケード削除される
        $student->delete();

        return response()->json([
            'message' => '生徒を削除しました',
        ]);
    }
}
