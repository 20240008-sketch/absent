<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">欠席記録</h1>
    
    <!-- 統計情報カード -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-600 mb-1">本日の欠席</h3>
        <p class="text-2xl font-bold text-red-600">{{ stats.today }}人</p>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-600 mb-1">今週の欠席</h3>
        <p class="text-2xl font-bold text-orange-600">{{ stats.week }}人</p>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-600 mb-1">今月の欠席</h3>
        <p class="text-2xl font-bold text-blue-600">{{ stats.month }}人</p>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-600 mb-1">総欠席数</h3>
        <p class="text-2xl font-bold text-purple-600">{{ stats.total }}人</p>
      </div>
    </div>

    <!-- 月別欠席者数グラフ -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <h2 class="text-xl font-semibold mb-4">月別欠席者数</h2>
      <div class="space-y-2">
        <div v-for="(item, index) in monthlyStats" :key="index" class="flex items-center">
          <div class="w-24 text-sm text-gray-600">{{ item.month }}</div>
          <div class="flex-1">
            <div class="h-8 bg-gray-200 rounded overflow-hidden">
              <div 
                class="h-full bg-blue-500 flex items-center justify-end pr-2 text-white text-sm font-semibold"
                :style="{ width: getBarWidth(item.count) }"
              >
                <span v-if="item.count > 0">{{ item.count }}人</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 検索フィルター -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <h2 class="text-lg font-semibold mb-4">日付別検索</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <Input
          v-model="filters.date"
          type="date"
          label="日付"
        />
        <Select
          v-model="filters.division"
          :options="divisionOptions"
          placeholder="区分で絞り込み"
          label="区分"
        />
        <Select
          v-model="filters.grade"
          :options="gradeOptions"
          placeholder="学年で絞り込み"
          label="学年"
        />
        <div class="flex items-end gap-2">
          <Button variant="primary" @click="fetchAbsences" class="flex-1">検索</Button>
          <Button variant="secondary" @click="resetFilters" class="flex-1">クリア</Button>
        </div>
      </div>
    </div>
    
    <!-- データテーブル -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold">
          欠席一覧
          <span v-if="filters.date" class="text-sm text-gray-600 ml-2">
            ({{ filters.date }})
          </span>
        </h2>
      </div>
      
      <div v-if="loading" class="p-8 text-center text-gray-500">
        読み込み中...
      </div>
      
      <div v-else-if="absences.length === 0" class="p-8 text-center text-gray-500">
        該当する欠席情報はありません
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">日付</th>
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
              <td class="px-6 py-4 whitespace-nowrap text-sm">{{ formatDate(item.absence_date) }}</td>
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
      
      <!-- ページネーション -->
      <div v-if="pagination.last_page > 1" class="px-6 py-4 border-t border-gray-200">
        <div class="flex justify-between items-center">
          <div class="text-sm text-gray-700">
            全{{ pagination.total }}件中 {{ pagination.from }}-{{ pagination.to }}件を表示
          </div>
          <div class="flex gap-2">
            <Button
              variant="secondary"
              size="sm"
              :disabled="pagination.current_page === 1"
              @click="changePage(pagination.current_page - 1)"
            >
              前へ
            </Button>
            <Button
              variant="secondary"
              size="sm"
              :disabled="pagination.current_page === pagination.last_page"
              @click="changePage(pagination.current_page + 1)"
            >
              次へ
            </Button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import Button from '../../../components/Button.vue';
import Input from '../../../components/Input.vue';
import Select from '../../../components/Select.vue';

const absences = ref([]);
const stats = ref({
  today: 0,
  week: 0,
  month: 0,
  total: 0
});
const monthlyStats = ref([]);
const loading = ref(false);

const filters = reactive({
  date: '',
  division: '',
  grade: ''
});

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0
});

const divisionOptions = [
  { value: '欠席', label: '欠席' },
  { value: '遅刻', label: '遅刻' },
  { value: '早退', label: '早退' }
];

const gradeOptions = [
  { value: '1', label: '1年' },
  { value: '2', label: '2年' },
  { value: '3', label: '3年' }
];

const maxMonthlyCount = computed(() => {
  return Math.max(...monthlyStats.value.map(m => m.count), 1);
});

const getBarWidth = (count) => {
  if (count === 0) return '0%';
  return `${(count / maxMonthlyCount.value) * 100}%`;
};

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

const formatDate = (date) => {
  if (!date) return '-';
  const d = new Date(date);
  return `${d.getFullYear()}/${String(d.getMonth() + 1).padStart(2, '0')}/${String(d.getDate()).padStart(2, '0')}`;
};

const fetchStats = async () => {
  try {
    const response = await axios.get('/api/admin/absences/stats');
    stats.value = response.data;
  } catch (error) {
    console.error('統計データ取得エラー:', error);
  }
};

const fetchMonthlyStats = async () => {
  try {
    const response = await axios.get('/api/admin/absences/monthly');
    monthlyStats.value = response.data;
  } catch (error) {
    console.error('月別統計データ取得エラー:', error);
  }
};

const fetchAbsences = async (page = 1) => {
  loading.value = true;
  try {
    const params = {
      page,
      date: filters.date || undefined,
      division: filters.division || undefined,
      grade: filters.grade || undefined
    };
    
    const response = await axios.get('/api/admin/absences', { params });
    absences.value = response.data.data || response.data;
    
    if (response.data.current_page) {
      Object.assign(pagination, {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        from: response.data.from,
        to: response.data.to,
        total: response.data.total
      });
    }
  } catch (error) {
    console.error('データ取得エラー:', error);
  } finally {
    loading.value = false;
  }
};

const resetFilters = () => {
  filters.date = '';
  filters.division = '';
  filters.grade = '';
  fetchAbsences();
};

const changePage = (page) => {
  fetchAbsences(page);
};

onMounted(() => {
  fetchStats();
  fetchMonthlyStats();
  fetchAbsences();
});
</script>
