<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ParentModel;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StudentLoginController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * 生徒ログイン（2FA不要）
     * 生徒のメールアドレス（Classroom）を使用して、対応する保護者情報を取得
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 生徒のメールアドレスで検索
        $student = Student::where('seito_initial_email', $credentials['email'])
            ->orWhere('seito_email', $credentials['email'])
            ->first();

        if (!$student) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        // この生徒に紐づく保護者を取得
        $parent = ParentModel::where('seito_id', $student->seito_id)->first();

        if (!$parent) {
            throw ValidationException::withMessages([
                'email' => ['保護者情報が登録されていません。初回登録を行ってください。'],
            ]);
        }

        // 保護者のパスワードで認証
        if (!Hash::check($credentials['password'], $parent->parent_password)) {
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ]);
        }

        // 直接ログイン
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
     * 2FA検証（保護者ログインと同じ）
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        // 生徒のメールアドレスから保護者を取得
        $student = Student::where('seito_initial_email', $request->email)
            ->orWhere('seito_email', $request->email)
            ->first();

        if (!$student) {
            throw ValidationException::withMessages([
                'code' => ['認証に失敗しました。'],
            ]);
        }

        $parent = ParentModel::where('seito_id', $student->seito_id)->first();

        if (!$parent) {
            throw ValidationException::withMessages([
                'code' => ['保護者情報が見つかりません。'],
            ]);
        }

        // 保護者のメールアドレスで2FAコードを検証
        if (!$this->twoFactorService->verify($parent->parent_email, $request->code, 'parent')) {
            throw ValidationException::withMessages([
                'code' => ['認証コードが正しくありません。'],
            ]);
        }

        // 保護者としてログイン
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
                'relationship' => $parent->parent_relationship,
            ],
        ]);
    }
}
