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

        // クラス名でフィルタ
        if ($request->has('class_name') && $request->class_name !== '') {
            $query->where('class_name', $request->class_name);
        }

        // 教員名でフィルタ
        if ($request->has('teacher_name') && $request->teacher_name !== '') {
            $query->where('teacher_name', 'like', "%{$request->teacher_name}%");
        }

        // 年度でフィルタ
        if ($request->has('year_id') && $request->year_id !== '') {
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
