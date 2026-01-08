<template>
  <div>
    <h1 class="text-3xl font-bold mb-6">管理者ダッシュボード</h1>
    
    <div v-if="loading" class="text-center py-8">
      <p class="text-gray-500">読み込み中...</p>
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">クラス数</h3>
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
        to="/admin/absences/today"
        class="bg-white rounded-lg shadow p-6 hover:bg-red-50 transition-colors cursor-pointer"
      >
        <h3 class="text-lg font-semibold text-gray-700 mb-2">本日の欠席</h3>
        <p class="text-3xl font-bold text-red-600">{{ stats?.today_absences ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-2">クリックして詳細を表示 →</p>
      </router-link>
    </div>
    
    <div class="mt-8 bg-white rounded-lg shadow p-6">
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
