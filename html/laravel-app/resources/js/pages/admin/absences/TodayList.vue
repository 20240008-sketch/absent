<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">本日の欠席情報</h1>
      <div class="text-sm text-gray-600">
        {{ today }}
      </div>
    </div>
    
    <!-- データテーブル -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-gray-500">
        読み込み中...
      </div>
      
      <div v-else-if="absences.length === 0" class="p-8 text-center text-gray-500">
        本日の欠席情報はありません
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">学年</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">クラス</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">出席番号</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">氏名</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">区分</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">理由</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">予定時刻</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="item in absences" :key="item.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm">{{ getGrade(item.student?.class_model?.class_name) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">{{ item.student?.class_model?.class_name || '-' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">{{ item.student?.seito_number || '-' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ item.student?.seito_name || '-' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <span 
                  class="px-2 py-1 rounded text-xs font-semibold"
                  :class="getDivisionClass(item.division)"
                >
                  {{ item.division }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm">{{ item.reason }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">{{ item.scheduled_time || '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- 統計情報 -->
    <div v-if="absences.length > 0" class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-red-50 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-red-700">欠席</h3>
        <p class="text-2xl font-bold text-red-600">{{ stats.absences }}人</p>
      </div>
      <div class="bg-yellow-50 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-yellow-700">遅刻</h3>
        <p class="text-2xl font-bold text-yellow-600">{{ stats.late }}人</p>
      </div>
      <div class="bg-blue-50 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-700">早退</h3>
        <p class="text-2xl font-bold text-blue-600">{{ stats.earlyLeave }}人</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const absences = ref([]);
const loading = ref(false);

const today = computed(() => {
  const date = new Date();
  return `${date.getFullYear()}年${date.getMonth() + 1}月${date.getDate()}日`;
});

const stats = computed(() => {
  return {
    absences: absences.value.filter(a => a.division === '欠席').length,
    late: absences.value.filter(a => a.division === '遅刻').length,
    earlyLeave: absences.value.filter(a => a.division === '早退').length,
  };
});

const getGrade = (className) => {
  if (!className) return '-';
  const match = className.match(/^(\d)/);
  return match ? `${match[1]}年` : '-';
};

const getDivisionClass = (division) => {
  switch (division) {
    case '欠席':
      return 'bg-red-100 text-red-800';
    case '遅刻':
      return 'bg-yellow-100 text-yellow-800';
    case '早退':
      return 'bg-blue-100 text-blue-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
};

const fetchData = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/absences/today');
    absences.value = response.data;
  } catch (error) {
    console.error('データ取得エラー:', error);
    alert('データの取得に失敗しました');
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchData();
});
</script>
