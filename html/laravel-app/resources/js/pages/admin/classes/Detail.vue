<template>
  <div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
      <div>
        <h1 class="text-2xl font-bold">クラス詳細</h1>
        <p v-if="classData" class="text-gray-600 mt-2">{{ classData.class_name }} - {{ classData.teacher_name }}</p>
      </div>
      <router-link to="/admin/classes">
        <Button variant="secondary">一覧に戻る</Button>
      </router-link>
    </div>

    <!-- クラス情報 -->
    <div v-if="classData" class="bg-white rounded-lg shadow p-6 mb-6">
      <h2 class="text-lg font-semibold mb-4">クラス情報</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <span class="text-sm text-gray-500">クラスID:</span>
          <span class="ml-2 font-medium">{{ classData.class_id }}</span>
        </div>
        <div>
          <span class="text-sm text-gray-500">クラス名:</span>
          <span class="ml-2 font-medium">{{ classData.class_name }}</span>
        </div>
        <div>
          <span class="text-sm text-gray-500">担任:</span>
          <span class="ml-2 font-medium">{{ classData.teacher_name }}</span>
        </div>
        <div>
          <span class="text-sm text-gray-500">メール:</span>
          <span class="ml-2 font-medium">{{ classData.teacher_email }}</span>
        </div>
        <div>
          <span class="text-sm text-gray-500">年度:</span>
          <span class="ml-2 font-medium">{{ classData.year_id }}</span>
        </div>
        <div>
          <span class="text-sm text-gray-500">生徒数:</span>
          <span class="ml-2 font-medium">{{ classData.students?.length || 0 }}人</span>
        </div>
      </div>
    </div>

    <!-- 検索フィルター -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <Input
          v-model="filters.date"
          type="date"
          placeholder="日付"
        />
        <Select
          v-model="filters.division"
          :options="divisionOptions"
          placeholder="区分"
        />
        <Button variant="primary" @click="fetchAbsences">検索</Button>
        <Button variant="secondary" @click="resetFilters">クリア</Button>
      </div>
    </div>

    <!-- 欠席連絡一覧 -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold">欠席連絡一覧</h2>
      </div>

      <div v-if="loading" class="p-8 text-center text-gray-500">
        読み込み中...
      </div>

      <div v-else-if="absences.length === 0" class="p-8 text-center text-gray-500">
        欠席連絡データがありません
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">日付</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">生徒ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">生徒名</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">区分</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">理由</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">連絡日時</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <template v-for="(item, index) in absences" :key="item.id">
              <!-- 日付区切り線 -->
              <tr v-if="index > 0 && item.absence_date !== absences[index - 1].absence_date">
                <td colspan="6" class="p-0">
                  <div class="border-t-4 border-green-200"></div>
                </td>
              </tr>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ item.absence_date }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ item.student.seito_id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ item.student.seito_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span :class="getDivisionClass(item.division)">
                    {{ item.division }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm">{{ item.reason }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDateTime(item.created_at) }}
                </td>
              </tr>
            </template>
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
import { ref, reactive, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import Button from '../../../components/Button.vue';
import Input from '../../../components/Input.vue';
import Select from '../../../components/Select.vue';

const route = useRoute();

const classData = ref(null);
const absences = ref([]);
const loading = ref(false);

const filters = reactive({
  date: '',
  division: ''
});

const divisionOptions = [
  { value: '欠席', label: '欠席' },
  { value: '遅刻', label: '遅刻' },
  { value: '早退', label: '早退' }
];

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0
});

const fetchClassData = async () => {
  try {
    const response = await axios.get(`/api/admin/classes/${route.params.id}`);
    classData.value = response.data;
  } catch (error) {
    console.error('クラス情報取得エラー:', error);
  }
};

const fetchAbsences = async (page = 1) => {
  loading.value = true;
  try {
    const params = {
      page,
      class_id: route.params.id,
      date: filters.date || undefined,
      division: filters.division || undefined
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
    console.error('欠席連絡取得エラー:', error);
  } finally {
    loading.value = false;
  }
};

const resetFilters = () => {
  filters.date = '';
  filters.division = '';
  fetchAbsences();
};

const changePage = (page) => {
  fetchAbsences(page);
};

const getDivisionClass = (division) => {
  const classes = {
    '欠席': 'px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800',
    '遅刻': 'px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800',
    '早退': 'px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800'
  };
  return classes[division] || 'px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800';
};

const formatDateTime = (dateTime) => {
  if (!dateTime) return '';
  const date = new Date(dateTime);
  return date.toLocaleString('ja-JP', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  });
};

onMounted(() => {
  fetchClassData();
  fetchAbsences();
});
</script>
