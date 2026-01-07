# 要件定義書 - 欠席連絡システム

## 1. プロジェクト概要

### 1.1 目的
学校における欠席・遅刻連絡を保護者がオンラインで行えるシステムを構築する。
管理者（学校側）は生徒・クラス・教員情報を管理し、保護者からの連絡を確認できる。

### 1.2 技術スタック
- **バックエンド**: Laravel 12
- **フロントエンド**: Vue.js
- **データベース**: SQLite
- **スタイリング**: Tailwind CSS
- **認証**: Laravel標準認証 + メール2段階認証

### 1.3 主要ユーザー
- **管理者**: 学校職員（生徒・クラス・教員データの管理）
- **保護者**: 欠席・遅刻連絡を行うユーザー

## 2. 機能要件

### 2.1 認証機能
- ログイン機能（メール + パスワード）
- 2段階認証（メール送信による確認コード）
- ログアウト機能
- パスワードリセット機能

### 2.2 管理者機能
#### 2.2.1 生徒管理
- 生徒の登録
- 生徒の編集
- 生徒の削除
- 生徒一覧表示

#### 2.2.2 保護者管理
- 保護者の登録
- 保護者の編集
- 保護者の削除
- 保護者一覧表示

#### 2.2.3 クラス管理
- クラスの登録
- クラスの編集
- クラスの削除
- クラス一覧表示

#### 2.2.4 CSVインポート機能
- 生徒データのCSVインポート
- クラスデータのCSVインポート
- 教員データのCSVインポート（クラス担任情報として）

### 2.3 保護者機能
#### 2.3.1 欠席連絡機能
- 欠席連絡の登録（欠席/遅刻区分、理由、登校予定時刻）
- 欠席連絡の編集
- 欠席連絡の削除
- 欠席連絡履歴の表示

## 3. データベース設計

### 3.1 生徒テーブル (students)
| カラム名 | データ型 | 説明 | 制約 |
|---------|---------|------|------|
| id | INTEGER | 主キー | PRIMARY KEY, AUTO_INCREMENT |
| seito_id | STRING | 生徒ID | UNIQUE, NOT NULL |
| seito_name | STRING | 生徒氏名 | NOT NULL |
| seito_number | INTEGER | 出席番号 | NOT NULL |
| class_id | INTEGER | クラスID（外部キー） | FOREIGN KEY |
| created_at | TIMESTAMP | 作成日時 | |
| updated_at | TIMESTAMP | 更新日時 | |

### 3.2 クラステーブル (classes)
| カラム名 | データ型 | 説明 | 制約 |
|---------|---------|------|------|
| id | INTEGER | 主キー | PRIMARY KEY, AUTO_INCREMENT |
| class_id | STRING | クラスID | UNIQUE, NOT NULL |
| class_name | STRING | クラス名 | NOT NULL |
| teacher_name | STRING | 担任名 | NOT NULL |
| teacher_email | STRING | 担任メール | NOT NULL |
| year_id | INTEGER | 年度 | NOT NULL |
| created_at | TIMESTAMP | 作成日時 | |
| updated_at | TIMESTAMP | 更新日時 | |

### 3.3 保護者テーブル (parents)
| カラム名 | データ型 | 説明 | 制約 |
|---------|---------|------|------|
| id | INTEGER | 主キー | PRIMARY KEY, AUTO_INCREMENT |
| seito_id | STRING | 生徒ID | FOREIGN KEY |
| parent_name | STRING | 保護者氏名 | NOT NULL |
| parent_relationship | STRING | 保護者区分（父・母・その他） | NOT NULL |
| parent_tel | STRING | 保護者電話番号 | |
| parent_initial_email | STRING | デフォルトメールアドレス（平文） | |
| parent_initial_password | STRING | デフォルトパスワード（平文） | |
| parent_email | STRING | 登録メール（平文） | UNIQUE |
| parent_password | STRING | 登録パスワード（ハッシュ化） | |
| created_at | TIMESTAMP | 作成日時 | |
| updated_at | TIMESTAMP | 更新日時 | |

### 3.4 欠席連絡テーブル (absences)
| カラム名 | データ型 | 説明 | 制約 |
|---------|---------|------|------|
| id | INTEGER | 主キー | PRIMARY KEY, AUTO_INCREMENT |
| seito_id | STRING | 生徒ID | FOREIGN KEY |
| division | STRING | 区分（欠席/遅刻） | NOT NULL |
| reason | TEXT | 理由 | NOT NULL |
| scheduled_time | TIME | 登校予定時刻 | NULL（遅刻の場合のみ） |
| absence_date | DATE | 欠席日 | NOT NULL |
| created_at | TIMESTAMP | 作成日時 | |
| updated_at | TIMESTAMP | 更新日時 | |

### 3.5 管理者テーブル (admins)
| カラム名 | データ型 | 説明 | 制約 |
|---------|---------|------|------|
| id | INTEGER | 主キー | PRIMARY KEY, AUTO_INCREMENT |
| name | STRING | 管理者名 | NOT NULL |
| email | STRING | メールアドレス | UNIQUE, NOT NULL |
| password | STRING | パスワード（ハッシュ化） | NOT NULL |
| created_at | TIMESTAMP | 作成日時 | |
| updated_at | TIMESTAMP | 更新日時 | |

## 4. 非機能要件

### 4.1 レスポンシブデザイン
- スマートフォン、タブレット、PC全てに対応
- Tailwind CSSを使用したモバイルファースト設計

### 4.2 セキュリティ
- パスワードはbcryptでハッシュ化
- CSRF保護
- XSS対策
- SQLインジェクション対策
- 2段階認証によるセキュリティ強化

### 4.3 ユーザビリティ
- シンプルで直感的なUI
- わかりやすいエラーメッセージ
- フォームバリデーション

### 4.4 パフォーマンス
- ページロード時間: 2秒以内
- データベースクエリの最適化

## 5. 成功基準

### 5.1 機能面
- [ ] 管理者が生徒・クラス・保護者の登録、編集、削除ができる
- [ ] CSVファイルから生徒・クラス・教員データをインポートできる
- [ ] 保護者がログイン後、欠席・遅刻連絡を登録できる
- [ ] 2段階認証が正常に動作する
- [ ] レスポンシブデザインが全デバイスで正常に表示される

### 5.2 技術面
- [ ] SQLiteデータベースが正常に動作する
- [ ] Vue.jsコンポーネントが正常に動作する
- [ ] Tailwind CSSが適切に適用される
- [ ] Laravel 12の機能が正常に動作する

### 5.3 セキュリティ面
- [ ] 認証機能が正常に動作する
- [ ] パスワードが適切にハッシュ化される
- [ ] 2段階認証が正常に機能する
- [ ] 権限管理が適切に実装される

## 6. 制約事項

### 6.1 技術的制約
- データベースはSQLiteのみ使用
- フロントエンドはVue.jsを使用
- スタイリングはTailwind CSSを使用

### 6.2 運用制約
- 管理者機能は認証済み管理者のみアクセス可能
- 保護者は自分の子供の欠席連絡のみ操作可能

## 7. 既存システムとの関連

- 新規システムのため、既存システムとの連携は不要

## 8. 今後の拡張可能性

- 出席状況の統計機能
- 担任への通知機能
- 保護者への確認メール送信機能
- 年度切り替え機能
- 複数の子供を持つ保護者への対応
