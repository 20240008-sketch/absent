<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">CSVインポート</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- 生徒データインポート -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-blue-600">生徒データ</h2>
        
        <div class="mb-4">
          <p class="text-sm text-gray-600 mb-2">CSVファイル形式:</p>
          <ul class="text-xs text-gray-500 list-disc list-inside mb-3">
            <li>seito_id (生徒ID)</li>
            <li>seito_name (生徒名)</li>
            <li>seito_number (出席番号)</li>
            <li>class_id (クラスID)</li>
            <li>seito_initial_email (初期メール)</li>
          </ul>
          <Button
            variant="secondary"
            size="sm"
            @click="downloadTemplate('students')"
            class="w-full mb-3"
          >
            📥 テンプレートダウンロード
          </Button>
        </div>
        
        <div class="mb-4">
          <input
            ref="studentsFileInput"
            type="file"
            accept=".csv"
            @change="handleFileSelect($event, 'students')"
            class="hidden"
          />
          <Button
            variant="primary"
            @click="$refs.studentsFileInput.click()"
            class="w-full"
            :disabled="uploading.students"
          >
            📁 ファイルを選択
          </Button>
          <p v-if="selectedFiles.students" class="text-sm text-gray-600 mt-2">
            {{ selectedFiles.students.name }}
          </p>
        </div>
        
        <Button
          variant="success"
          @click="uploadFile('students')"
          :disabled="!selectedFiles.students || uploading.students"
          class="w-full"
        >
          {{ uploading.students ? 'アップロード中...' : '⬆️ インポート実行' }}
        </Button>
        
        <div v-if="results.students" class="mt-4 text-sm">
          <p class="text-green-600 font-semibold">
            ✓ {{ results.students.success }}件 成功
          </p>
          <p v-if="results.students.errors && results.students.errors.length > 0" class="text-red-600">
            ✗ {{ results.students.errors.length }}件 エラー
          </p>
        </div>
      </div>
      
      <!-- 保護者データインポート -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-green-600">保護者データ</h2>
        
        <div class="mb-4">
          <p class="text-sm text-gray-600 mb-2">CSVファイル形式:</p>
          <ul class="text-xs text-gray-500 list-disc list-inside mb-3">
            <li>seito_id (生徒ID)</li>
            <li>parent_name (保護者名)</li>
            <li>parent_email (メールアドレス)</li>
            <li>parent_tel (電話番号)</li>
            <li>parent_relationship (続柄)</li>
            <li>initial_password (初期パスワード)</li>
          </ul>
          <Button
            variant="secondary"
            size="sm"
            @click="downloadTemplate('parents')"
            class="w-full mb-3"
          >
            📥 テンプレートダウンロード
          </Button>
        </div>
        
        <div class="mb-4">
          <input
            ref="parentsFileInput"
            type="file"
            accept=".csv"
            @change="handleFileSelect($event, 'parents')"
            class="hidden"
          />
          <Button
            variant="primary"
            @click="$refs.parentsFileInput.click()"
            class="w-full"
            :disabled="uploading.parents"
          >
            📁 ファイルを選択
          </Button>
          <p v-if="selectedFiles.parents" class="text-sm text-gray-600 mt-2">
            {{ selectedFiles.parents.name }}
          </p>
        </div>
        
        <Button
          variant="success"
          @click="uploadFile('parents')"
          :disabled="!selectedFiles.parents || uploading.parents"
          class="w-full"
        >
          {{ uploading.parents ? 'アップロード中...' : '⬆️ インポート実行' }}
        </Button>
        
        <div v-if="results.parents" class="mt-4 text-sm">
          <p class="text-green-600 font-semibold">
            ✓ {{ results.parents.success }}件 成功
          </p>
          <p v-if="results.parents.errors && results.parents.errors.length > 0" class="text-red-600">
            ✗ {{ results.parents.errors.length }}件 エラー
          </p>
        </div>
      </div>
      
      <!-- 管理者データインポート -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-purple-600">管理者データ</h2>
        
        <div class="mb-4">
          <p class="text-sm text-gray-600 mb-2">CSVファイル形式:</p>
          <ul class="text-xs text-gray-500 list-disc list-inside mb-3">
            <li>name (管理者名)</li>
            <li>email (メールアドレス)</li>
            <li>password (パスワード)</li>
          </ul>
          <Button
            variant="secondary"
            size="sm"
            @click="downloadTemplate('admins')"
            class="w-full mb-3"
          >
            📥 テンプレートダウンロード
          </Button>
        </div>
        
        <div class="mb-4">
          <input
            ref="adminsFileInput"
            type="file"
            accept=".csv"
            @change="handleFileSelect($event, 'admins')"
            class="hidden"
          />
          <Button
            variant="primary"
            @click="$refs.adminsFileInput.click()"
            class="w-full"
            :disabled="uploading.admins"
          >
            📁 ファイルを選択
          </Button>
          <p v-if="selectedFiles.admins" class="text-sm text-gray-600 mt-2">
            {{ selectedFiles.admins.name }}
          </p>
        </div>
        
        <Button
          variant="success"
          @click="uploadFile('admins')"
          :disabled="!selectedFiles.admins || uploading.admins"
          class="w-full"
        >
          {{ uploading.admins ? 'アップロード中...' : '⬆️ インポート実行' }}
        </Button>
        
        <div v-if="results.admins" class="mt-4 text-sm">
          <p class="text-green-600 font-semibold">
            ✓ {{ results.admins.success }}件 成功
          </p>
          <p v-if="results.admins.errors && results.admins.errors.length > 0" class="text-red-600">
            ✗ {{ results.admins.errors.length }}件 エラー
          </p>
        </div>
      </div>
    </div>
    
    <!-- エラー詳細表示 -->
    <div v-if="hasErrors" class="mt-8 bg-red-50 border border-red-200 rounded-lg p-6">
      <h3 class="text-lg font-semibold text-red-800 mb-4">⚠️ エラー詳細</h3>
      
      <div v-for="(result, type) in results" :key="type">
        <div v-if="result && result.errors && result.errors.length > 0" class="mb-4">
          <h4 class="font-semibold text-red-700 mb-2">{{ getTypeName(type) }}</h4>
          <div v-for="(error, index) in result.errors" :key="index" class="text-sm mb-2">
            <p class="font-medium">行 {{ error.row }}:</p>
            <ul class="list-disc list-inside ml-4 text-red-600">
              <li v-for="(msg, i) in error.errors" :key="i">{{ msg }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 使い方ガイド -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
      <h3 class="text-lg font-semibold text-blue-800 mb-4">📖 使い方</h3>
      <ol class="list-decimal list-inside text-sm text-gray-700 space-y-2">
        <li>各データの「テンプレートダウンロード」ボタンでCSVテンプレートを取得</li>
        <li>テンプレートに従ってデータを入力（Excelやメモ帳で編集可能）</li>
        <li>「ファイルを選択」ボタンで作成したCSVファイルを選択</li>
        <li>「インポート実行」ボタンでデータをアップロード</li>
        <li>成功件数とエラー件数が表示されます</li>
      </ol>
      <p class="mt-4 text-xs text-gray-600">
        ※ 既存データと重複する場合は上書きされます<br>
        ※ ファイルサイズは2MB以下、UTF-8エンコーディングを推奨
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import axios from 'axios';
import Button from '../../../components/Button.vue';

const selectedFiles = reactive({
  students: null,
  parents: null,
  admins: null,
});

const uploading = reactive({
  students: false,
  parents: false,
  admins: false,
});

const results = reactive({
  students: null,
  parents: null,
  admins: null,
});

const hasErrors = computed(() => {
  return Object.values(results).some(result => 
    result && result.errors && result.errors.length > 0
  );
});

const handleFileSelect = (event, type) => {
  const file = event.target.files[0];
  if (file && file.name.endsWith('.csv')) {
    selectedFiles[type] = file;
    results[type] = null; // 前回の結果をクリア
  } else {
    alert('CSVファイルを選択してください');
  }
};

const uploadFile = async (type) => {
  if (!selectedFiles[type]) return;
  
  uploading[type] = true;
  
  const formData = new FormData();
  formData.append('file', selectedFiles[type]);
  
  try {
    const response = await axios.post(`/api/admin/import/${type}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    
    results[type] = response.data.result;
    alert(response.data.message);
    
    // ファイル選択をリセット
    selectedFiles[type] = null;
    
  } catch (error) {
    console.error('Upload error:', error);
    alert(error.response?.data?.message || 'アップロードに失敗しました');
  } finally {
    uploading[type] = false;
  }
};

const downloadTemplate = async (type) => {
  try {
    const response = await axios.get(`/api/admin/import/template/${type}`, {
      responseType: 'blob',
    });
    
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `${type}_template.csv`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    
  } catch (error) {
    console.error('Download error:', error);
    alert('テンプレートのダウンロードに失敗しました');
  }
};

const getTypeName = (type) => {
  const names = {
    students: '生徒データ',
    parents: '保護者データ',
    admins: '管理者データ',
  };
  return names[type] || type;
};
</script>
