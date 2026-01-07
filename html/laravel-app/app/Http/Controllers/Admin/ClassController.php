<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRequest;
use App\Http\Requests\UpdateClassRequest;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * クラス一覧取得
     */
    public function index(Request $request)
    {
        $query = ClassModel::query();

        // 検索
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('class_name', 'like', "%{$search}%")
                  ->orWhere('class_id', 'like', "%{$search}%")
                  ->orWhere('teacher_name', 'like', "%{$search}%");
            });
        }

        // 年度でフィルタ
        if ($request->has('year_id')) {
            $query->where('year_id', $request->year_id);
        }

        // ページネーション
        $classes = $query->orderBy('class_id')
                        ->paginate($request->get('per_page', 20));

        return response()->json($classes);
    }

    /**
     * クラス登録
     */
    public function store(StoreClassRequest $request)
    {
        $class = ClassModel::create($request->validated());

        return response()->json([
            'message' => 'クラスを登録しました',
            'class' => $class,
        ], 201);
    }

    /**
     * クラス詳細取得
     */
    public function show($id)
    {
        $class = ClassModel::with('students')->findOrFail($id);

        return response()->json($class);
    }

    /**
     * クラス更新
     */
    public function update(UpdateClassRequest $request, $id)
    {
        $class = ClassModel::findOrFail($id);
        $class->update($request->validated());

        return response()->json([
            'message' => 'クラスを更新しました',
            'class' => $class,
        ]);
    }

    /**
     * クラス削除
     */
    public function destroy($id)
    {
        $class = ClassModel::findOrFail($id);
        
        // 生徒が所属している場合は削除できない
        if ($class->students()->count() > 0) {
            return response()->json([
                'message' => '生徒が所属しているため削除できません',
            ], 422);
        }

        $class->delete();

        return response()->json([
            'message' => 'クラスを削除しました',
        ]);
    }
}
