<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">{{ isEdit ? '生徒編集' : '生徒登録' }}</h1>
    
    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
      <form @submit.prevent="handleSubmit">
        <Input
          id="seito_id"
          v-model="form.seito_id"
          type="number"
          label="生徒ID"
          required
          :error="errors.seito_id"
          :disabled="isEdit"
        />
        
        <Input
          id="seito_name"
          v-model="form.seito_name"
          label="氏名"
          required
          :error="errors.seito_name"
        />
        
        <Input
          id="seito_number"
          v-model="form.seito_number"
          type="number"
          label="出席番号"
          required
          :error="errors.seito_number"
        />
        
        <Select
          id="class_id"
          v-model="form.class_id"
          label="クラス"
          :options="classOptions"
          placeholder="クラスを選択"
          required
          :error="errors.class_id"
        />
        
        <p v-if="errors.general" class="mb-4 text-sm text-red-600">{{ errors.general }}</p>
        
        <div class="flex gap-4">
          <Button
            type="submit"
            variant="primary"
            :disabled="loading"
          >
            {{ loading ? '保存中...' : '保存' }}
          </Button>
          <router-link to="/admin/students">
            <Button variant="secondary">キャンセル</Button>
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAdminStore } from '../../../stores/admin';
import Input from '../../../components/Input.vue';
import Select from '../../../components/Select.vue';
import Button from '../../../components/Button.vue';

const route = useRoute();
const router = useRouter();
const adminStore = useAdminStore();

const isEdit = computed(() => !!route.params.id);

const form = reactive({
  seito_id: '',
  seito_name: '',
  seito_number: '',
  class_id: ''
});

const errors = reactive({
  seito_id: '',
  seito_name: '',
  seito_number: '',
  class_id: '',
  general: ''
});

const loading = ref(false);
const classes = ref([]);

const classOptions = computed(() => {
  return classes.value.map(c => ({
    value: c.class_id,
    label: c.class_name
  }));
});

const fetchClasses = async () => {
  try {
    const response = await adminStore.fetchClasses({ per_page: 100 });
    classes.value = response.data || response;
  } catch (error) {
    console.error('クラス取得エラー:', error);
  }
};

const fetchData = async () => {
  if (!isEdit.value) return;
  
  loading.value = true;
  try {
    const data = await adminStore.fetchStudent(route.params.id);
    Object.assign(form, {
      seito_id: data.seito_id,
      seito_name: data.seito_name,
      seito_number: data.seito_number,
      class_id: data.class_id
    });
  } catch (error) {
    errors.general = 'データの取得に失敗しました';
  } finally {
    loading.value = false;
  }
};

const handleSubmit = async () => {
  Object.keys(errors).forEach(key => errors[key] = '');
  loading.value = true;
  
  try {
    if (isEdit.value) {
      await adminStore.updateStudent(route.params.id, form);
    } else {
      await adminStore.createStudent(form);
    }
    router.push('/admin/students');
  } catch (error) {
    if (error.response?.data?.errors) {
      Object.assign(errors, error.response.data.errors);
    } else {
      errors.general = error.response?.data?.message || '保存に失敗しました';
    }
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchClasses();
  fetchData();
});
</script>
