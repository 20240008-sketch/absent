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
     * ログイン処理（第1段階：メール・パスワード認証）
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'メールアドレスまたはパスワードが正しくありません'
            ], 401);
        }

        // 2段階認証コードを生成・送信
        $sent = $this->twoFactorService->createAndSend(
            $admin->email,
            'admin',
            $admin->name
        );

        if (!$sent) {
            return response()->json([
                'message' => '認証コードの送信に失敗しました'
            ], 500);
        }

        // 一時セッションに保存
        session(['two_factor_admin_id' => $admin->id]);

        return response()->json([
            'message' => '認証コードをメールで送信しました',
            'requires_2fa' => true,
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
