<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminLoginController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * ログイン処理（パスワード認証のみ - 2FAなし）
     */
    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        // 固定の管理者アカウントを取得（最初の管理者）
        $admin = Admin::first();

        if (!$admin) {
            return response()->json([
                'message' => '管理者アカウントが存在しません'
            ], 401);
        }

        // パスワードチェック
        if (!Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'パスワードが正しくありません'
            ], 401);
        }

        // 直接ログイン（2FAなし）
        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'ログインに成功しました',
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
        ]);
    }

    /**
     * 2段階認証コード検証
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $adminId = session('two_factor_admin_id');
        
        if (!$adminId) {
            return response()->json([
                'message' => 'セッションが無効です。最初からやり直してください'
            ], 400);
        }

        $admin = Admin::find($adminId);
        
        if (!$admin) {
            return response()->json([
                'message' => '管理者が見つかりません'
            ], 404);
        }

        // 2段階認証コードを検証
        $verified = $this->twoFactorService->verify(
            $admin->email,
            $request->code,
            'admin'
        );

        if (!$verified) {
            return response()->json([
                'message' => '認証コードが正しくないか、有効期限切れです'
            ], 401);
        }

        // 本ログイン
        Auth::guard('admin')->login($admin);
        
        // 2段階認証完了フラグを設定
        session(['two_factor_verified' => true]);
        session()->forget('two_factor_admin_id');

        return response()->json([
            'message' => 'ログインに成功しました',
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
        ]);
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return response()->json([
            'message' => 'ログアウトしました'
        ]);
    }

    /**
     * 現在のログイン情報取得
     */
    public function me()
    {
        $admin = Auth::guard('admin')->user();
        
        if (!$admin) {
            return response()->json([
                'message' => '認証されていません'
            ], 401);
        }

        return response()->json([
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
        ]);
    }
}
