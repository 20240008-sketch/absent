<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">CSVインポート</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- 生徒インポート -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">生徒インポート</h2>
        <p class="text-sm text-gray-600 mb-4">
          生徒情報をCSVファイルからインポートします
        </p>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            CSVファイル選択
          </label>
          <input
            type="file"
            accept=".csv"
            @change="handleFileSelect($event, 'students')"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
          />
        </div>
        
        <div v-if="files.students" class="mb-4 p-3 bg-gray-50 rounded">
          <p class="text-sm text-gray-700">{{ files.students.name }}</p>
        </div>
        
        <Button
          variant="primary"
          class="w-full"
          :disabled="!files.students || uploading.students"
          @click="importFile('students')"
        >
          {{ uploading.students ? 'インポート中...' : '生徒をインポート' }}
        </Button>
        
        <div v-if="results.students" class="mt-4">
          <div v-if="results.students.success" class="p-3 bg-green-50 border border-green-200 rounded">
            <p class="text-sm text-green-800">{{ results.students.message }}</p>
          </div>
          <div v-else class="p-3 bg-red-50 border border-red-200 rounded">
            <p class="text-sm text-red-800">{{ results.students.message }}</p>
          </div>
        </div>
      </div>
      
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
      
      <!-- 教員インポート -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">教員インポート</h2>
        <p class="text-sm text-gray-600 mb-4">
          教員情報をCSVファイルからインポートします
        </p>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            CSVファイル選択
          </label>
          <input
            type="file"
            accept=".csv"
            @change="handleFileSelect($event, 'teachers')"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100"
          />
        </div>
        
        <div v-if="files.teachers" class="mb-4 p-3 bg-gray-50 rounded">
          <p class="text-sm text-gray-700">{{ files.teachers.name }}</p>
        </div>
        
        <Button
          variant="primary"
          class="w-full"
          :disabled="!files.teachers || uploading.teachers"
          @click="importFile('teachers')"
        >
          {{ uploading.teachers ? 'インポート中...' : '教員をインポート' }}
        </Button>
        
        <div v-if="results.teachers" class="mt-4">
          <div v-if="results.teachers.success" class="p-3 bg-green-50 border border-green-200 rounded">
            <p class="text-sm text-green-800">{{ results.teachers.message }}</p>
          </div>
          <div v-else class="p-3 bg-red-50 border border-red-200 rounded">
            <p class="text-sm text-red-800">{{ results.teachers.message }}</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- CSV形式の説明 -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-semibold mb-4">CSV形式について</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <h3 class="font-medium mb-2">生徒CSV</h3>
          <pre class="text-xs bg-gray-100 p-3 rounded overflow-x-auto">seito_id,seito_name,seito_number,class_id
1001,山田太郎,1,A101
1002,佐藤花子,2,A101</pre>
        </div>
        
        <div>
          <h3 class="font-medium mb-2">クラスCSV</h3>
          <pre class="text-xs bg-gray-100 p-3 rounded overflow-x-auto">class_id,class_name,teacher_name,teacher_email,year_id
A101,1年A組,田中先生,tanaka@example.com,2025</pre>
        </div>
        
        <div>
          <h3 class="font-medium mb-2">教員CSV</h3>
          <pre class="text-xs bg-gray-100 p-3 rounded overflow-x-auto">class_id,teacher_name,teacher_email
A101,田中先生,tanaka@example.com
A102,鈴木先生,suzuki@example.com</pre>
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
  classes: null,
  teachers: null
});

const uploading = reactive({
  students: false,
  classes: false,
  teachers: false
});

const results = reactive({
  students: null,
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
