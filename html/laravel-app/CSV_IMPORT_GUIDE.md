# CSVインポート機能

## 概要
管理者が生徒、保護者、管理者のデータをCSVファイルから一括インポートできます。

## アクセス方法
管理者ダッシュボード → ナビゲーションメニュー「CSVインポート」

## 機能

### 1. 生徒データインポート
- **必須項目**:
  - `seito_id`: 生徒ID（文字列、例: 1001）
  - `seito_name`: 生徒名（例: 山田太郎）
  - `seito_number`: 出席番号（数値）
  - `class_id`: クラスID（例: 1TOKUSHIN, 2SHINGAKU）
- **任意項目**:
  - `seito_initial_email`: 初期メールアドレス

### 2. 保護者データインポート
- **必須項目**:
  - `seito_id`: 生徒ID（紐付ける生徒のID）
  - `parent_name`: 保護者名（例: 山田一郎）
  - `parent_email`: メールアドレス（ログイン用）
  - `parent_relationship`: 続柄（例: 父、母）
- **任意項目**:
  - `parent_tel`: 電話番号
  - `initial_password`: 初期パスワード（未指定の場合は"password123"）

### 3. 管理者データインポート
- **必須項目**:
  - `name`: 管理者名
  - `password`: パスワード
- **任意項目**:
  - `email`: メールアドレス

## CSVファイル例

### students.csv
```csv
seito_id,seito_name,seito_number,class_id,seito_initial_email
1001,山田太郎,1,1TOKUSHIN,1001@seiei.ac.jp
1002,佐藤花子,2,1TOKUSHIN,1002@seiei.ac.jp
2001,鈴木一郎,1,2SHINGAKU,2001@seiei.ac.jp
```

### parents.csv
```csv
seito_id,parent_name,parent_email,parent_tel,parent_relationship,initial_password
1001,山田一郎,yamada@example.com,090-1234-5678,父,password123
1002,佐藤美咲,sato@example.com,080-9876-5432,母,password456
```

### admins.csv
```csv
name,email,password
管理者,admin@seiei.ac.jp,seiei2026
副管理者,sub-admin@seiei.ac.jp,admin2026
```

## 使い方

1. **テンプレートダウンロード**
   - 各データカード内の「📥 テンプレートダウンロード」ボタンをクリック
   - サンプル行を含むCSVファイルがダウンロードされます

2. **データ編集**
   - ExcelやGoogleスプレッドシート、メモ帳などでCSVファイルを編集
   - ヘッダー行は削除しないでください
   - 文字コードはUTF-8を推奨

3. **ファイル選択**
   - 「📁 ファイルを選択」ボタンで編集したCSVファイルを選択

4. **インポート実行**
   - 「⬆️ インポート実行」ボタンをクリック
   - 成功件数とエラー件数が表示されます
   - エラーがある場合は詳細が画面下部に表示されます

## 注意事項

- ファイルサイズは2MB以下
- CSV形式（.csv拡張子）のみ対応
- 既存データと重複する場合は上書きされます
  - 生徒: seito_id で判定
  - 保護者: parent_email で判定
  - 管理者: email（未指定の場合はname）で判定
- 保護者データをインポートする前に、対応する生徒データが存在している必要があります
- 生徒データをインポートする前に、対応するクラスデータが存在している必要があります

## エラー処理

- バリデーションエラーがある行はスキップされます
- 成功した行のみがデータベースに登録されます
- エラー詳細には行番号とエラー理由が表示されます

## セキュリティ

- 管理者権限が必要です
- パスワードは自動的にハッシュ化されて保存されます
- 保護者の初期パスワードは初回ログイン後に変更を促されます
