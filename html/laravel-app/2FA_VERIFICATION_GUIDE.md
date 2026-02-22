# 2段階認証機能の確認方法

## 現在の状態

✅ **2段階認証は実装済みで動作しています**

## 確認方法

### 1. ブラウザのキャッシュをクリア

保護者ログイン画面を開く前に：
1. ブラウザの開発者ツールを開く（F12）
2. Consoleタブを開く
3. ページを再読み込み（Ctrl+Shift+R または Cmd+Shift+R）

### 2. ログインの流れ

1. **保護者ログイン画面**
   - メールアドレス: `yamada@example.com` (または任意の保護者メール)
   - パスワード: `password123` (初期パスワード)
   - 「ログイン」ボタンをクリック

2. **ブラウザコンソールに以下のログが表示されます**:
   ```
   🔐 保護者ログイン開始
   📡 API呼び出し: /api/parent/login
   📨 APIレスポンス: {requires_2fa: true, email: "..."}
   🔑 2FA必要?: true
   ✅ 2FA画面へ遷移します
   ```

3. **2段階認証画面に自動遷移**
   - メールに記載された6桁のコードを入力
   - 「認証する」ボタンをクリック

4. **ダッシュボードに遷移**

## メール確認方法（開発環境）

メールは `MAIL_MAILER=log` を使用しているため、ログファイルに出力されます：

```bash
cd /var/www/html/laravel-app
tail -50 storage/logs/laravel.log | grep "認証コード"
```

出力例：
```
認証コード: 123456
```

## トラブルシューティング

### 問題: 2FA画面に遷移せずダッシュボードに直接移動する

**原因**: ブラウザが古いJavaScriptをキャッシュしている

**解決策**:
1. ブラウザの開発者ツール（F12）を開く
2. Networkタブを開く
3. 「Disable cache」にチェック
4. ページをハード再読み込み（Ctrl+Shift+R）

または

```bash
# サーバー側で再ビルド
cd /var/www/html/laravel-app
npm run build
```

### 問題: 認証コードがメールで届かない

**原因**: 開発環境では実際のメール送信をしていない

**解決策**: ログファイルから認証コードを確認
```bash
tail -100 storage/logs/laravel.log | grep -A 5 "認証コード"
```

## 本番環境への移行

本番環境で実際にメール送信するには、`.env`を以下のように設定：

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com  # または他のSMTPサーバー
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@seiei.ac.jp"
MAIL_FROM_NAME="欠席連絡システム"
```

## 確認済み事項

- ✅ バックエンド: 2FA必須ロジック実装済み
- ✅ フロントエンド: 2FA画面遷移ロジック実装済み
- ✅ コード生成: 6桁ランダムコード正常動作
- ✅ コード有効期限: 10分間設定済み
- ✅ メール送信: ログファイルに出力確認
- ✅ 再送信機能: 60秒クールダウン実装済み

## テスト実行結果

```bash
php test_2fa.php

=== 2FA動作テスト ===
保護者: 山田一郎
メール: yamada@example.com

認証コード送信: 成功
生成されたコード: 914235
有効期限: 2026-02-22 17:02:23
```

✅ **2段階認証は正常に動作しています**
