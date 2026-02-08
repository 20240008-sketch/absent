<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">CSVインポート</h1>
    
    <!-- 第1行: 生徒、保護者、管理者 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <!-- 生徒データインポート -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">生徒データ</h2>
        <div class="mb-4">
          <p class="text-xs text-gray-600 mb-2">CSVファイル形式:</p>
          <ul class="text-xs text-gray-700 space-y-1 mb-3">
            <li>• seito_id (生徒ID)</li>
            <li>• seito_name (生徒名)</li>
            <li>• seito_number (出席番号)</li>
            <li>• class_id (クラスID)</li>
            <li>• seito_initial_email (初期メール)</li>
          </ul>
          <a
            href="/templates/students_template.csv"
            download
            class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mb-3"
          >
            📥 テンプレートダウンロード
          </a>
        </div>
        
        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            📁 ファイルを選択
          </label>
          <input
            type="file"
            accept=".csv"
            @change="handleFileSelect($event, 'students')"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
          />
        </div>
        
        <div v-if="files.students" class="mb-3 p-2 bg-gray-50 rounded">
          <p class="text-xs text-gray-700">{{ files.students.name }}</p>
        </div>
        
        <Button
          variant="primary"
          class="w-full"
          :disabled="!files.students || uploading.students"
          @click="importFile('students')"
        >
          {{ uploading.students ? 'インポート中...' : '⬆️ インポート実行' }}
        </Button>
        
        <div v-if="results.students" class="mt-3">
          <div v-if="results.students.success" class="p-2 bg-green-50 border border-green-200 rounded">
            <p class="text-xs text-green-800">{{ results.students.message }}</p>
          </div>
          <div v-else class="p-2 bg-red-50 border border-red-200 rounded">
            <p class="text-xs text-red-800">{{ results.students.message }}</p>
          </div>
        </div>
      </div>

      <!-- 保護者データインポート -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">保護者データ</h2>
        <div class="mb-4">
          <p class="text-xs text-gray-600 mb-2">CSVファイル形式:</p>
          <ul class="text-xs text-gray-700 space-y-1 mb-3">
            <li>• seito_id (生徒ID)</li>
            <li>• parent_name (保護者名)</li>
            <li>• parent_email (メールアドレス)</li>
            <li>• parent_tel (電話番号)</li>
            <li>• parent_relationship (続柄)</li>
            <li>• initial_password (初期パスワード)</li>
          </ul>
          <a
            href="/templates/parents_template.csv"
            download
            class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mb-3"
          >
            📥 テンプレートダウンロード
          </a>
        </div>
        
        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            📁 ファイルを選択
          </label>
          <input
            type="file"
            accept=".csv"
            @change="handleFileSelect($event, 'parents')"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
          />
        </div>
        
        <div v-if="files.parents" class="mb-3 p-2 bg-gray-50 rounded">
          <p class="text-xs text-gray-700">{{ files.parents.name }}</p>
        </div>
        
        <Button
          variant="success"
          class="w-full"
          :disabled="!files.parents || uploading.parents"
          @click="importFile('parents')"
        >
          {{ uploading.parents ? 'インポート中...' : '⬆️ インポート実行' }}
        </Button>
        
        <div v-if="results.parents" class="mt-3">
          <div v-if="results.parents.success" class="p-2 bg-green-50 border border-green-200 rounded">
            <p class="text-xs text-green-800">{{ results.parents.message }}</p>
          </div>
          <div v-else class="p-2 bg-red-50 border border-red-200 rounded">
            <p class="text-xs text-red-800">{{ results.parents.message }}</p>
          </div>
        </div>
      </div>

      <!-- 管理者データインポート -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">管理者データ</h2>
        <div class="mb-4">
          <p class="text-xs text-gray-600 mb-2">CSVファイル形式:</p>
          <ul class="text-xs text-gray-700 space-y-1 mb-3">
            <li>• name (管理者名)</li>
            <li>• email (メールアドレス)</li>
            <li>• password (パスワード)</li>
            <li>• class_id (担当クラスID - 担任の場合)</li>
            <li>• is_super_admin (スーパー管理者: true/false)</li>
          </ul>
          <div class="mt-2 p-2 bg-blue-50 rounded">
            <p class="text-xs text-blue-800 font-medium mb-1">📌 よく使うアカウント:</p>
            <p class="text-xs text-blue-700">• スーパー管理者: seiei2026 / 0000</p>
            <p class="text-xs text-blue-700">• 担任: teacher1tokushin@seiei.ac.jp / seiei2026</p>
            <p class="text-xs text-blue-700">• 担任: teacher2shingaku@seiei.ac.jp / seiei2026</p>
          </div>
          <a
            href="/templates/admins_template.csv"
            download
            class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mb-3 mt-3"
          >
            📥 テンプレートダウンロード
          </a>
        </div>
        
        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            📁 ファイルを選択
          </label>
          <input
            type="file"
            accept=".csv"
            @change="handleFileSelect($event, 'admins')"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100"
          />
        </div>
        
        <div v-if="files.admins" class="mb-3 p-2 bg-gray-50 rounded">
          <p class="text-xs text-gray-700">{{ files.admins.name }}</p>
        </div>
        
        <Button
          variant="primary"
          class="w-full"
          :disabled="!files.admins || uploading.admins"
          @click="importFile('admins')"
        >
          {{ uploading.admins ? 'インポート中...' : '⬆️ インポート実行' }}
        </Button>
        
        <div v-if="results.admins" class="mt-3">
          <div v-if="results.admins.success" class="p-2 bg-green-50 border border-green-200 rounded">
            <p class="text-xs text-green-800">{{ results.admins.message }}</p>
          </div>
          <div v-else class="p-2 bg-red-50 border border-red-200 rounded">
            <p class="text-xs text-red-800">{{ results.admins.message }}</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 第2行: クラス、担任 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- クラスインポート -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">クラスインポート</h2>
        <p class="text-sm text-gray-600 mb-4">
          クラス情報をCSVファイルからインポートします
        </p>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            CSVファイル選択
          </label>
          <input
            type="file"
            accept=".csv"
            @change="handleFileSelect($event, 'classes')"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
          />
        </div>
        
        <div v-if="files.classes" class="mb-4 p-3 bg-gray-50 rounded">
          <p class="text-sm text-gray-700">{{ files.classes.name }}</p>
        </div>
        
        <Button
          variant="success"
          class="w-full"
          :disabled="!files.classes || uploading.classes"
          @click="importFile('classes')"
        >
          {{ uploading.classes ? 'インポート中...' : 'クラスをインポート' }}
        </Button>
        
        <div v-if="results.classes" class="mt-4">
          <div v-if="results.classes.success" class="p-3 bg-green-50 border border-green-200 rounded">
            <p class="text-sm text-green-800">{{ results.classes.message }}</p>
          </div>
          <div v-else class="p-3 bg-red-50 border border-red-200 rounded">
            <p class="text-sm text-red-800">{{ results.classes.message }}</p>
          </div>
        </div>
      </div>
      
      <!-- 担任データインポート -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">担任データ</h2>
        <div class="mb-4">
          <p class="text-xs text-gray-600 mb-2">CSVファイル形式:</p>
          <ul class="text-xs text-gray-700 space-y-1 mb-3">
            <li>• name (担任名)</li>
            <li>• email (メールアドレス)</li>
            <li>• password (パスワード)</li>
            <li>• class_id (担当クラスID)</li>
          </ul>
          <a
            href="/templates/teachers_template.csv"
            download
            class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mb-3"
          >
            📥 テンプレートダウンロード
          </a>
        </div>
        
        <div class="mb-3">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            📁 ファイルを選択
          </label>
          <input
            type="file"
            accept=".csv"
            @change="handleFileSelect($event, 'teachers')"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100"
          />
        </div>
        
        <div v-if="files.teachers" class="mb-3 p-2 bg-gray-50 rounded">
          <p class="text-xs text-gray-700">{{ files.teachers.name }}</p>
        </div>
        
        <Button
          variant="primary"
          class="w-full"
          :disabled="!files.teachers || uploading.teachers"
          @click="importFile('teachers')"
        >
          {{ uploading.teachers ? 'インポート中...' : '⬆️ インポート実行' }}
        </Button>
        
        <div v-if="results.teachers" class="mt-3">
          <div v-if="results.teachers.success" class="p-2 bg-green-50 border border-green-200 rounded">
            <p class="text-xs text-green-800">{{ results.teachers.message }}</p>
          </div>
          <div v-else class="p-2 bg-red-50 border border-red-200 rounded">
            <p class="text-xs text-red-800">{{ results.teachers.message }}</p>
          </div>
        </div>
      </div>
    </div>
    </div>
    
    <!-- CSV形式の説明 -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-semibold mb-4">CSV形式サンプル</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div>
          <h3 class="font-medium mb-2">生徒データCSV</h3>
          <pre class="text-xs bg-gray-100 p-3 rounded overflow-x-auto">seito_id,seito_name,seito_number,class_id,seito_initial_email
1001,山田太郎,1,1TOKUSHIN,yamada@example.com
1002,佐藤花子,2,1TOKUSHIN,sato@example.com</pre>
        </div>
        
        <div>
          <h3 class="font-medium mb-2">保護者データCSV</h3>
          <pre class="text-xs bg-gray-100 p-3 rounded overflow-x-auto">seito_id,parent_name,parent_email,parent_tel,parent_relationship,initial_password
1001,山田一郎,yamada.p@example.com,090-1234-5678,父,password123
1002,佐藤美香,sato.p@example.com,080-9876-5432,母,password456</pre>
        </div>
        
        <div>
          <h3 class="font-medium mb-2">管理者データCSV</h3>
          <pre class="text-xs bg-gray-100 p-3 rounded overflow-x-auto">name,email,password,class_id,is_super_admin
スーパー管理者,seiei2026,0000,,true
田中太郎,teacher1tokushin@seiei.ac.jp,seiei2026,1TOKUSHIN,false
佐藤花子,teacher2shingaku@seiei.ac.jp,seiei2026,2SHINGAKU,false</pre>
          <p class="text-xs text-gray-600 mt-2">
            ※ 担任はclass_idを指定し、is_super_admin=false
          </p>
        </div>
        
        <div>
          <h3 class="font-medium mb-2">クラスCSV</h3>
          <pre class="text-xs bg-gray-100 p-3 rounded overflow-x-auto">class_id,class_name,teacher_name,teacher_email,year_id
1TOKUSHIN,1年特進,田中先生,tanaka@seiei.ac.jp,2026
1SHINGAKU,1年進学,佐藤先生,sato@seiei.ac.jp,2026</pre>
        </div>
        
        <div>
          <h3 class="font-medium mb-2">担任データCSV</h3>
          <pre class="text-xs bg-gray-100 p-3 rounded overflow-x-auto">name,email,password,class_id
田中太郎,teacher1tokushin@seiei.ac.jp,seiei2026,1TOKUSHIN
佐藤花子,teacher1shingaku@seiei.ac.jp,seiei2026,1SHINGAKU</pre>
          <p class="text-xs text-gray-600 mt-2">
            ※ 担任は自動的に is_super_admin=false として登録されます
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue';
import { useAdminStore } from '../../stores/admin';
import Button from '../../components/Button.vue';

const adminStore = useAdminStore();

const files = reactive({
  students: null,
  parents: null,
  admins: null,
  classes: null,
  teachers: null
});

const uploading = reactive({
  students: false,
  parents: false,
  admins: false,
  classes: false,
  teachers: false
});

const results = reactive({
  students: null,
  parents: null,
  admins: null,
  classes: null,
  teachers: null
});

const handleFileSelect = (event, type) => {
  const file = event.target.files[0];
  if (file) {
    files[type] = file;
    results[type] = null;
  }
};

const importFile = async (type) => {
  if (!files[type]) return;
  
  uploading[type] = true;
  results[type] = null;
  
  try {
    const response = await adminStore.importCsv(type, files[type]);
    results[type] = {
      success: true,
      message: response.message || 'インポートが完了しました'
    };
    files[type] = null;
    // ファイル入力をリセット
    const input = document.querySelector(`input[type="file"][accept=".csv"]`);
    if (input) input.value = '';
  } catch (error) {
    results[type] = {
      success: false,
      message: error.response?.data?.message || 'インポートに失敗しました'
    };
  } finally {
    uploading[type] = false;
  }
};
</script>
