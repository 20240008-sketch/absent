<template>
  <div>
    <div class="mb-6">
      <h1 class="text-3xl font-bold">管理者ダッシュボード</h1>
      <p v-if="stats && !stats.is_super_admin && stats.class_name" class="text-lg text-gray-600 mt-2">
        📚 {{ stats.class_name }} 担任
      </p>
      <p v-else-if="stats && stats.is_super_admin" class="text-lg text-gray-600 mt-2">
        🔑 スーパー管理者（全体管理）
      </p>
    </div>
    
    <div v-if="loading" class="text-center py-8">
      <p class="text-gray-500">読み込み中...</p>
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">
          {{ stats?.is_super_admin ? 'クラス数' : '担当クラス' }}
        </h3>
        <p class="text-3xl font-bold text-blue-600">{{ stats?.class_count ?? 0 }}</p>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">生徒数</h3>
        <p class="text-3xl font-bold text-green-600">{{ stats?.student_count ?? 0 }}</p>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">保護者数</h3>
        <p class="text-3xl font-bold text-purple-600">{{ stats?.parent_count ?? 0 }}</p>
      </div>
      
      <router-link
        to="/admin/absences"
        class="bg-white rounded-lg shadow p-6 hover:bg-red-50 transition-colors cursor-pointer"
      >
        <h3 class="text-lg font-semibold text-gray-700 mb-2">本日の欠席、遅刻</h3>
        <div class="flex items-baseline gap-3">
          <div>
            <span class="text-sm text-gray-600">欠席</span>
            <p class="text-3xl font-bold text-red-600 inline ml-1">{{ stats?.today_absences ?? 0 }}</p>
          </div>
          <div>
            <span class="text-sm text-gray-600">遅刻</span>
            <p class="text-3xl font-bold text-orange-600 inline ml-1">{{ stats?.today_late ?? 0 }}</p>
          </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">クリックして詳細を表示 →</p>
      </router-link>
    </div>
    
    <div v-if="stats && stats.is_super_admin" class="mt-8 bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-semibold mb-4">クイックアクション</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <router-link
          to="/admin/classes/create"
          class="p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 text-center"
        >
          <p class="font-medium">クラス登録</p>
        </router-link>
        <router-link
          to="/admin/students/create"
          class="p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 text-center"
        >
          <p class="font-medium">生徒登録</p>
        </router-link>
        <router-link
          to="/admin/import"
          class="p-4 border-2 border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 text-center"
        >
          <p class="font-medium">CSVインポート</p>
        </router-link>
      </div>
    </div>
    
    <div v-else class="mt-8 bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-semibold mb-4">担任メニュー</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <router-link
          to="/admin/absences"
          class="p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 text-center"
        >
          <div class="text-3xl mb-2">📋</div>
          <p class="font-medium">欠席連絡を確認</p>
        </router-link>
        <router-link
          to="/admin/students"
          class="p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 text-center"
        >
          <div class="text-3xl mb-2">👥</div>
          <p class="font-medium">生徒一覧</p>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAdminStore } from '../../stores/admin';

const adminStore = useAdminStore();
const stats = ref(null);
const loading = ref(true);

const fetchStats = async () => {
  try {
    stats.value = await adminStore.fetchDashboardStats();
  } catch (error) {
    console.error('統計データ取得エラー:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchStats();
});
</script>
