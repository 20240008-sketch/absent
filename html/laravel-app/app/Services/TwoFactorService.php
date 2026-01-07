<?php

namespace App\Services;

use App\Models\TwoFactorCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class TwoFactorService
{
    /**
     * 6桁の認証コードを生成
     */
    public function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * 認証コードを作成してメール送信
     */
    public function createAndSend(string $email, string $guard, string $name): bool
    {
        // 既存のコードを削除
        TwoFactorCode::where('email', $email)
            ->where('guard', $guard)
            ->delete();

        // 新しいコードを生成
        $code = $this->generateCode();
        
        // データベースに保存（10分間有効）
        TwoFactorCode::create([
            'email' => $email,
            'code' => $code,
            'guard' => $guard,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // メール送信
        try {
            Mail::raw(
                "{$name} 様\n\nログイン認証コードは以下の通りです。\n\n認証コード: {$code}\n\nこのコードは10分間有効です。",
                function ($message) use ($email) {
                    $message->to($email)
                        ->subject('【欠席連絡システム】認証コード');
                }
            );
            return true;
        } catch (\Exception $e) {
            \Log::error('2FA メール送信エラー: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 認証コードを検証
     */
    public function verify(string $email, string $code, string $guard): bool
    {
        $record = TwoFactorCode::where('email', $email)
            ->where('code', $code)
            ->where('guard', $guard)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($record) {
            // 使用済みコードを削除
            $record->delete();
            return true;
        }

        return false;
    }

    /**
     * 期限切れコードを削除
     */
    public function cleanExpired(): int
    {
        return TwoFactorCode::where('expires_at', '<', Carbon::now())->delete();
    }
}
