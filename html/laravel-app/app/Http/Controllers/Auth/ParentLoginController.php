<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ParentModel;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ParentLoginController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * 保護者ログイン（2FA前）
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $parent = ParentModel::where('parent_email', $credentials['email'])->first();

        if (!$parent || !Hash::check($credentials['password'], $parent->parent_password)) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        // 初回パスワード変更が必要かチェック
        $needsPasswordChange = $parent->parent_initial_password !== null;

        // 2FAコード生成・送信
        $this->twoFactorService->createAndSend($credentials['email'], 'parent');

        return response()->json([
            'message' => '認証コードを送信しました。',
            'needs_password_change' => $needsPasswordChange,
        ]);
    }

    /**
     * 2FA検証
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        if (!$this->twoFactorService->verify($request->email, $request->code, 'parent')) {
            throw ValidationException::withMessages([
                'code' => ['認証コードが正しくありません。'],
            ]);
        }

        $parent = ParentModel::where('parent_email', $request->email)->first();

        Auth::guard('parent')->login($parent);
        $request->session()->regenerate();

        // 初回パスワード変更が必要かチェック
        $needsPasswordChange = $parent->parent_initial_password !== null;

        return response()->json([
            'message' => 'ログインしました。',
            'needs_password_change' => $needsPasswordChange,
            'parent' => [
                'id' => $parent->id,
                'name' => $parent->parent_name,
                'email' => $parent->parent_email,
                'seito_id' => $parent->seito_id,
            ],
        ]);
    }

    /**
     * ログアウト
     */
    public function logout(Request $request)
    {
        Auth::guard('parent')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'ログアウトしました。']);
    }

    /**
     * 現在のログイン保護者情報取得
     */
    public function me(Request $request)
    {
        $parent = Auth::guard('parent')->user();

        if (!$parent) {
            return response()->json(['message' => '認証されていません。'], 401);
        }

        return response()->json([
            'id' => $parent->id,
            'name' => $parent->parent_name,
            'email' => $parent->parent_email,
            'tel' => $parent->parent_tel,
            'relationship' => $parent->parent_relationship,
            'seito_id' => $parent->seito_id,
            'needs_password_change' => $parent->parent_initial_password !== null,
        ]);
    }

    /**
     * パスワード変更
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $parent = Auth::guard('parent')->user();

        // 現在のパスワードが正しいか確認
        if (!Hash::check($request->current_password, $parent->parent_password)) {
            throw ValidationException::withMessages([
                'current_password' => ['現在のパスワードが正しくありません。'],
            ]);
        }

        // 新しいパスワードを設定し、初期パスワードをクリア
        $parent->parent_password = Hash::make($request->new_password);
        $parent->parent_initial_password = null;
        $parent->save();

        return response()->json(['message' => 'パスワードを変更しました。']);
    }
}
