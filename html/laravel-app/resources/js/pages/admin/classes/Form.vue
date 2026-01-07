<template>
  <div>
    <h1 class="text-2xl font-bold mb-6">{{ isEdit ? 'クラス編集' : 'クラス登録' }}</h1>
    
    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
      <form @submit.prevent="handleSubmit">
        <Input
          id="class_id"
          v-model="form.class_id"
          label="クラスID"
          required
          :error="errors.class_id"
          :disabled="isEdit"
        />
        
        <Input
          id="class_name"
          v-model="form.class_name"
          label="クラス名"
          required
          :error="errors.class_name"
        />
        
        <Input
          id="teacher_name"
          v-model="form.teacher_name"
          label="担任名"
          required
          :error="errors.teacher_name"
        />
        
        <Input
          id="teacher_email"
          v-model="form.teacher_email"
          type="email"
          label="担任メール"
          required
          :error="errors.teacher_email"
        />
        
        <Input
          id="year_id"
          v-model="form.year_id"
          type="number"
          label="年度"
          required
          :error="errors.year_id"
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
          <router-link to="/admin/classes">
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
import Button from '../../../components/Button.vue';

const route = useRoute();
const router = useRouter();
const adminStore = useAdminStore();

const isEdit = computed(() => !!route.params.id);

const form = reactive({
  class_id: '',
  class_name: '',
  teacher_name: '',
  teacher_email: '',
  year_id: new Date().getFullYear()
});

const errors = reactive({
  class_id: '',
  class_name: '',
  teacher_name: '',
  teacher_email: '',
  year_id: '',
  general: ''
});

const loading = ref(false);

const fetchData = async () => {
  if (!isEdit.value) return;
  
  loading.value = true;
  try {
    const data = await adminStore.fetchClass(route.params.id);
    Object.assign(form, {
      class_id: data.class_id,
      class_name: data.class_name,
      teacher_name: data.teacher_name,
      teacher_email: data.teacher_email,
      year_id: data.year_id
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
      await adminStore.updateClass(route.params.id, form);
    } else {
      await adminStore.createClass(form);
    }
    router.push('/admin/classes');
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
  fetchData();
});
</script>
