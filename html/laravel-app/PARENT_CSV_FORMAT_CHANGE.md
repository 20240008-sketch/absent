# 保護者CSVインポート形式変更

## 変更内容

保護者データのCSVインポート形式を以下のように変更しました。

### 新しいCSVファイル形式

```csv
seito_id,parent_name,parent_initial_email,parent_initial_password
1001,山田一郎,yamada@example.com,password123
1002,佐藤美咲,sato@example.com,password456
2001,鈴木花子,suzuki@example.com,pass2001
```

### 必須項目

| 項目名 | 説明 | 例 |
|--------|------|-----|
| seito_id | 生徒ID（紐付ける生徒のID） | 1001 |
| parent_name | 保護者名 | 山田一郎 |
| parent_initial_email | 初期メールアドレス（ログイン用） | yamada@example.com |
| parent_initial_password | 初期パスワード | password123 |

### 変更点

#### 削除された項目
- ~~parent_tel~~（電話番号）
- ~~parent_relationship~~（続柄）

これらの項目は削除され、システム側で自動的に以下の値が設定されます：
- `parent_tel`: null（未設定）
- `parent_relationship`: "保護者"（固定値）

#### 変更された項目名
- `parent_email` → `parent_initial_email`
- `initial_password` → `parent_initial_password`

## 機能

### インポート後の認証情報表示

保護者データをインポートすると、成功した場合に以下の情報が画面に表示されます：

- 🔐 登録された認証情報
  - 生徒ID
  - 生徒名
  - 保護者名
  - メールアドレス
  - パスワード

**※ この情報は必ず保護者へ伝達してください**

### テンプレートダウンロード

CSVインポート画面の「📥 テンプレートダウンロード」ボタンから、新しい形式のテンプレートをダウンロードできます。

## 使い方

1. CSVインポート画面を開く
2. 保護者データの「📥 テンプレートダウンロード」をクリック
3. ダウンロードしたCSVファイルを編集
4. 「📁 ファイルを選択」で編集したCSVを選択
5. 「⬆️ インポート実行」をクリック
6. 表示された認証情報を保護者へ伝達

## 注意事項

- 保護者データをインポートする前に、対応する生徒データが存在している必要があります
- 既存の保護者（同じメールアドレス）がいる場合は上書きされます
- パスワードは自動的にハッシュ化されて保存されます（セキュリティ対策）
- 認証情報は画面表示後、再度確認できません。必ずメモまたは印刷してください

## 関連ファイル

- `/app/Services/CsvImportService.php` - インポート処理
- `/app/Http/Controllers/Admin/CsvImportController.php` - テンプレート生成
- `/resources/js/pages/admin/import/Index.vue` - UI画面
- `/CSV_IMPORT_GUIDE.md` - 詳細マニュアル
