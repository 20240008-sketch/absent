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
     * 保護者ログイン（2FA必須）
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $parent = ParentModel::where('parent_email', $credentials['email'])->first();

        if (!$parent) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        // 通常のパスワードまたは初期パスワードでログイン可能
        $passwordValid = Hash::check($credentials['password'], $parent->parent_password);
        $initialPasswordValid = $parent->parent_initial_password && 
                               Hash::check($credentials['password'], $parent->parent_initial_password);

        if (!$passwordValid && !$initialPasswordValid) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        // 2段階認証コードを送信
        $sent = $this->twoFactorService->createAndSend(
            $parent->parent_email,
            'parent',
            $parent->parent_name
        );

        if (!$sent) {
            throw ValidationException::withMessages([
                'email' => ['認証コードの送信に失敗しました。しばらくしてから再度お試しください。'],
            ]);
        }

        return response()->json([
            'message' => '認証コードをメールで送信しました。',
            'requires_2fa' => true,
            'email' => $parent->parent_email,
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
     * 2FA認証コード再送信
     */
    public function resend2FA(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $parent = ParentModel::where('parent_email', $request->email)->first();

        if (!$parent) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスが正しくありません。'],
            ]);
        }

        $sent = $this->twoFactorService->createAndSend(
            $parent->parent_email,
            'parent',
            $parent->parent_name
        );

        if (!$sent) {
            throw ValidationException::withMessages([
                'email' => ['認証コードの送信に失敗しました。'],
            ]);
        }

        return response()->json([
            'message' => '認証コードを再送信しました。',
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

        /** @var \App\Models\ParentModel $parent */
        $parent = Auth::guard('parent')->user();

        // 現在のパスワードまたは初期パスワードが正しいか確認
        $currentPasswordValid = Hash::check($request->current_password, $parent->parent_password);
        $initialPasswordValid = $parent->parent_initial_password && 
                               Hash::check($request->current_password, $parent->parent_initial_password);

        if (!$currentPasswordValid && !$initialPasswordValid) {
            throw ValidationException::withMessages([
                'current_password' => ['現在のパスワードまたは初期パスワードが正しくありません。'],
            ]);
        }

        // 新しいパスワードを設定し、初期パスワードをクリア
        ParentModel::where('id', $parent->id)->update([
            'parent_password' => Hash::make($request->new_password),
            'parent_initial_password' => null,
        ]);

        return response()->json(['message' => 'パスワードを変更しました。']);
    }
}
