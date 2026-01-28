<?php

namespace App\Http\Controllers;

use App\Models\ParentModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DemoController extends Controller
{
    /**
     * デモ用：保護者として直接ログイン
     */
    public function parentLogin(Request $request)
    {
        // 最初の保護者アカウントを取得
        $parent = ParentModel::with('student')->first();

        if (!$parent) {
            return response()->json([
                'message' => '保護者アカウントが存在しません'
            ], 404);
        }

        // 直接ログイン
        Auth::guard('parent')->login($parent);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'デモログインに成功しました',
            'parent' => [
                'id' => $parent->id,
                'seito_id' => $parent->seito_id,
                'parent_name' => $parent->parent_name,
                'parent_email' => $parent->parent_email,
                'relationship' => $parent->relationship,
            ],
        ]);
    }
}
