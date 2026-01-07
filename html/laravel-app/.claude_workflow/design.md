# 設計書 - 欠席連絡システム

## 1. システムアーキテクチャ

### 1.1 全体構成
```
┌─────────────────────────────────────┐
│       フロントエンド (Vue.js)        │
│    + Tailwind CSS                   │
└──────────────┬──────────────────────┘
               │ HTTP/JSON
               ▼
┌─────────────────────────────────────┐
│    バックエンド (Laravel 12)         │
│    - API Routes                     │
│    - Controllers                    │
│    - Models                         │
│    - Middleware (Auth, 2FA)         │
└──────────────┬──────────────────────┘
               │
               ▼
┌─────────────────────────────────────┐
│      データベース (SQLite)           │
└─────────────────────────────────────┘
```

### 1.2 技術選定理由

#### 1.2.1 Laravel 12
- 最新バージョンで長期サポート対象
- 標準で認証機能、バリデーション、メール送信機能を提供
- Eloquent ORMによる安全なデータベース操作

#### 1.2.2 Vue.js 3 + Composition API
- リアクティブなUI構築
- コンポーネント指向で再利用性が高い
- Laravel Viteと統合が容易

#### 1.2.3 SQLite
- セットアップが簡単
- 小規模〜中規模システムに最適
- ファイルベースで管理が容易

#### 1.2.4 Tailwind CSS
- ユーティリティファーストで開発速度が向上
- レスポンシブデザインが容易
- カスタマイズ性が高い

## 2. データベース詳細設計

### 2.1 ER図
```
┌─────────────┐      ┌─────────────┐
│   classes   │      │   students  │
├─────────────┤      ├─────────────┤
│ id (PK)     │◄────┤│ id (PK)     │
│ class_id    │      │ seito_id    │
│ class_name  │      │ seito_name  │
│ teacher_name│      │ seito_number│
│ teacher_email      │ class_id(FK)│
│ year_id     │      └──────┬──────┘
└─────────────┘             │
                            │
                            ▼
                    ┌─────────────┐
                    │   parents   │
                    ├─────────────┤
                    │ id (PK)     │
                    │ seito_id(FK)│
                    │ parent_name │
                    │ parent_rel..│
                    │ parent_tel  │
                    │ parent_ini..│
                    │ parent_email│
                    │ parent_pass.│
                    └──────┬──────┘
                           │
                           │
                    ┌──────┴──────┐
                    │  absences   │
                    ├─────────────┤
                    │ id (PK)     │
                    │ seito_id(FK)│
                    │ division    │
                    │ reason      │
                    │ scheduled_..│
                    │ absence_date│
                    └─────────────┘

┌─────────────┐
│   admins    │
├─────────────┤
│ id (PK)     │
│ name        │
│ email       │
│ password    │
└─────────────┘
```

### 2.2 マイグレーション実装順序
1. `create_admins_table` - 管理者テーブル
2. `create_classes_table` - クラステーブル
3. `create_students_table` - 生徒テーブル（外部キー: class_id）
4. `create_parents_table` - 保護者テーブル（外部キー: seito_id）
5. `create_absences_table` - 欠席連絡テーブル（外部キー: seito_id）
6. `create_two_factor_codes_table` - 2段階認証コードテーブル

### 2.3 インデックス設計
- `students.seito_id` - UNIQUE INDEX
- `students.class_id` - INDEX
- `classes.class_id` - UNIQUE INDEX
- `parents.seito_id` - INDEX
- `parents.parent_email` - UNIQUE INDEX
- `absences.seito_id` - INDEX
- `absences.absence_date` - INDEX
- `admins.email` - UNIQUE INDEX

## 3. バックエンド設計

### 3.1 ディレクトリ構成
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── StudentController.php
│   │   │   ├── ParentController.php
│   │   │   ├── ClassController.php
│   │   │   └── ImportController.php
│   │   ├── Auth/
│   │   │   ├── AdminLoginController.php
│   │   │   ├── ParentLoginController.php
│   │   │   └── TwoFactorController.php
│   │   └── Parent/
│   │       └── AbsenceController.php
│   ├── Middleware/
│   │   ├── AdminAuth.php
│   │   ├── ParentAuth.php
│   │   └── TwoFactorVerified.php
│   └── Requests/
│       ├── StoreStudentRequest.php
│       ├── UpdateStudentRequest.php
│       ├── StoreParentRequest.php
│       ├── StoreClassRequest.php
│       └── StoreAbsenceRequest.php
├── Models/
│   ├── Admin.php
│   ├── Student.php
│   ├── ParentModel.php
│   ├── ClassModel.php
│   ├── Absence.php
│   └── TwoFactorCode.php
└── Services/
    ├── CsvImportService.php
    └── TwoFactorService.php
```

### 3.2 ルーティング設計

#### 3.2.1 管理者ルート (prefix: /admin)
```php
// 認証
POST /admin/login
POST /admin/logout
POST /admin/verify-2fa

// 生徒管理
GET /admin/students
POST /admin/students
GET /admin/students/{id}
PUT /admin/students/{id}
DELETE /admin/students/{id}

// 保護者管理
GET /admin/parents
POST /admin/parents
GET /admin/parents/{id}
PUT /admin/parents/{id}
DELETE /admin/parents/{id}

// クラス管理
GET /admin/classes
POST /admin/classes
GET /admin/classes/{id}
PUT /admin/classes/{id}
DELETE /admin/classes/{id}

// CSVインポート
POST /admin/import/students
POST /admin/import/classes
POST /admin/import/teachers
```

#### 3.2.2 保護者ルート (prefix: /parent)
```php
// 認証
POST /parent/login
POST /parent/logout
POST /parent/verify-2fa

// 欠席連絡
GET /parent/absences
POST /parent/absences
GET /parent/absences/{id}
PUT /parent/absences/{id}
DELETE /parent/absences/{id}
```

### 3.3 認証・認可設計

#### 3.3.1 2段階認証フロー
```
1. ユーザーがメール・パスワードでログイン
   ↓
2. 認証情報が正しい場合、6桁の認証コードを生成
   ↓
3. 認証コードをメールで送信
   ↓
4. ユーザーが認証コードを入力
   ↓
5. コードが正しい場合、セッションを確立
```

#### 3.3.2 ガード設定
- `admin` ガード: 管理者用
- `parent` ガード: 保護者用

#### 3.3.3 ミドルウェア
- `AdminAuth`: 管理者認証チェック
- `ParentAuth`: 保護者認証チェック
- `TwoFactorVerified`: 2段階認証済みチェック

### 3.4 バリデーションルール

#### 3.4.1 生徒登録
```php
'seito_id' => 'required|string|unique:students',
'seito_name' => 'required|string|max:255',
'seito_number' => 'required|integer|min:1',
'class_id' => 'required|exists:classes,id',
```

#### 3.4.2 保護者登録
```php
'seito_id' => 'required|exists:students,seito_id',
'parent_name' => 'required|string|max:255',
'parent_relationship' => 'required|in:父,母,その他',
'parent_tel' => 'nullable|string|max:20',
'parent_email' => 'required|email|unique:parents',
'parent_password' => 'required|string|min:8',
```

#### 3.4.3 欠席連絡登録
```php
'seito_id' => 'required|exists:students,seito_id',
'division' => 'required|in:欠席,遅刻',
'reason' => 'required|string|max:500',
'scheduled_time' => 'required_if:division,遅刻|date_format:H:i',
'absence_date' => 'required|date',
```

## 4. フロントエンド設計

### 4.1 ディレクトリ構成
```
resources/
├── js/
│   ├── app.js
│   ├── components/
│   │   ├── Admin/
│   │   │   ├── StudentList.vue
│   │   │   ├── StudentForm.vue
│   │   │   ├── ParentList.vue
│   │   │   ├── ParentForm.vue
│   │   │   ├── ClassList.vue
│   │   │   ├── ClassForm.vue
│   │   │   └── CsvImport.vue
│   │   ├── Parent/
│   │   │   ├── AbsenceList.vue
│   │   │   └── AbsenceForm.vue
│   │   ├── Auth/
│   │   │   ├── AdminLogin.vue
│   │   │   ├── ParentLogin.vue
│   │   │   └── TwoFactorVerify.vue
│   │   └── Common/
│   │       ├── Header.vue
│   │       ├── Footer.vue
│   │       ├── Modal.vue
│   │       └── Table.vue
│   ├── layouts/
│   │   ├── AdminLayout.vue
│   │   └── ParentLayout.vue
│   └── router/
│       └── index.js
└── css/
    └── app.css
```

### 4.2 主要コンポーネント設計

#### 4.2.1 StudentList.vue
- 生徒一覧表示（テーブル形式）
- ソート・フィルタリング機能
- 編集・削除ボタン
- 新規登録ボタン

#### 4.2.2 AbsenceForm.vue
- 欠席/遅刻選択（ラジオボタン）
- 日付選択（カレンダー）
- 理由入力（テキストエリア）
- 登校予定時刻（遅刻の場合のみ表示）
- バリデーション表示

#### 4.2.3 CsvImport.vue
- ファイル選択
- プレビュー表示
- インポート実行ボタン
- エラー表示

### 4.3 状態管理

Pinia（Vue 3の推奨状態管理）を使用:
```
stores/
├── auth.js - 認証状態
├── admin.js - 管理者機能の状態
└── parent.js - 保護者機能の状態
```

### 4.4 レスポンシブブレークポイント
```
sm: 640px   - スマートフォン
md: 768px   - タブレット
lg: 1024px  - デスクトップ
xl: 1280px  - 大画面デスクトップ
```

## 5. CSV インポート設計

### 5.1 CSVフォーマット

#### 5.1.1 生徒データ
```csv
seito_id,seito_name,seito_number,class_id
S001,山田太郎,1,CL001
S002,佐藤花子,2,CL001
```

#### 5.1.2 クラスデータ
```csv
class_id,class_name,teacher_name,teacher_email,year_id
CL001,1年1組,田中先生,tanaka@school.jp,2025
CL002,1年2組,鈴木先生,suzuki@school.jp,2025
```

#### 5.1.3 教員データ（クラス担任として）
```csv
class_id,teacher_name,teacher_email
CL001,田中先生,tanaka@school.jp
CL002,鈴木先生,suzuki@school.jp
```

### 5.2 インポート処理フロー
```
1. CSVファイルアップロード
   ↓
2. ファイル形式チェック（拡張子、サイズ）
   ↓
3. CSVパース
   ↓
4. データバリデーション
   ↓
5. エラーがあればプレビューで表示
   ↓
6. 問題なければDBトランザクション開始
   ↓
7. 一括挿入/更新
   ↓
8. コミット/ロールバック
```

## 6. セキュリティ設計

### 6.1 認証セキュリティ
- パスワード: bcrypt（cost=12）
- セッション: httponly, secure, samesite=strict
- CSRF トークン: 全POST/PUT/DELETEリクエスト
- 2段階認証コード: 10分間有効、6桁数字、使用後削除

### 6.2 認可設計
- 管理者: 全機能アクセス可
- 保護者: 自分の子供の欠席連絡のみ

### 6.3 入力サンプル検証
- XSS対策: Laravel自動エスケープ
- SQLインジェクション対策: Eloquent ORM使用
- ファイルアップロード: MIME type検証、サイズ制限（2MB）

## 7. メール設計

### 7.1 2段階認証メール
```
件名: 【欠席連絡システム】認証コード

本文:
{name} 様

ログイン認証コードは以下の通りです。

認証コード: {code}

このコードは10分間有効です。
```

### 7.2 パスワードリセットメール
```
件名: 【欠席連絡システム】パスワードリセット

本文:
{name} 様

パスワードリセットのリクエストを受け付けました。
以下のリンクからパスワードを再設定してください。

{reset_url}

このリンクは24時間有効です。
```

## 8. エラーハンドリング設計

### 8.1 HTTPステータスコード
- 200: 成功
- 201: 作成成功
- 400: バリデーションエラー
- 401: 認証エラー
- 403: 権限エラー
- 404: リソース不存在
- 422: バリデーションエラー（詳細付き）
- 500: サーバーエラー

### 8.2 エラーレスポンス形式
```json
{
  "message": "エラーメッセージ",
  "errors": {
    "field_name": ["エラー詳細"]
  }
}
```

## 9. テスト設計

### 9.1 単体テスト
- Model: リレーション、スコープ
- Validation: 各バリデーションルール
- Service: CSVインポート、2段階認証

### 9.2 機能テスト
- 認証フロー
- CRUD操作
- CSVインポート
- 権限チェック

## 10. パフォーマンス最適化

### 10.1 データベース
- Eager Loading（N+1問題回避）
- インデックス活用
- ページネーション（1ページ20件）

### 10.2 フロントエンド
- コンポーネントの遅延読み込み
- 画像最適化
- Viteによるビルド最適化

## 11. 開発フェーズ計画

### Phase 1: 環境構築（完了）
- Laravel 12プロジェクト作成 ✓

### Phase 2: 基盤構築
- データベース設計・マイグレーション
- 認証システム構築
- 2段階認証実装

### Phase 3: 管理者機能
- 生徒CRUD
- 保護者CRUD
- クラスCRUD
- CSVインポート

### Phase 4: 保護者機能
- 欠席連絡CRUD
- 履歴表示

### Phase 5: フロントエンド
- Vue.js環境構築
- Tailwind CSS設定
- コンポーネント実装

### Phase 6: テスト・調整
- 機能テスト
- UIテスト
- パフォーマンステスト

## 12. 技術的課題と解決策

### 12.1 課題: 保護者の初期パスワード管理
**解決策**: 
- parent_initial_password（平文）とparent_password（ハッシュ）を分離
- 初回ログイン時にパスワード変更を促す

### 12.2 課題: 2段階認証のセッション管理
**解決策**:
- 一時セッション（2FA未完了）と本セッション（2FA完了）を分離
- ミドルウェアでチェック

### 12.3 課題: CSVインポートの大量データ処理
**解決策**:
- チャンク処理（1000件ずつ）
- トランザクション管理
- バックグラウンドジョブ（必要に応じて）

## 13. 未決定事項・今後の検討課題

1. メール送信方法（SMTP設定）
2. 本番環境のサーバー構成
3. バックアップ戦略
4. ログ管理方針
5. 複数子供を持つ保護者のUI設計
