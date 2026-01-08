<template>
  <div>
    <h2 class="text-2xl font-bold text-center mb-6">管理者ログイン</h2>
    
    <form @submit.prevent="handleSubmit">
      <Input
        id="password"
        v-model="form.password"
        type="password"
        label="パスワード"
        placeholder="パスワードを入力してください"
        required
        :error="errors.password"
      />
      
      <p v-if="errors.general" class="mb-4 text-sm text-red-600">{{ errors.general }}</p>
      
      <Button
        type="submit"
        variant="primary"
        class="w-full"
        :disabled="loading"
      >
        {{ loading ? 'ログイン中...' : 'ログイン' }}
      </Button>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import Input from '../../components/Input.vue';
import Button from '../../components/Button.vue';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
  password: ''
});

const errors = reactive({
  password: '',
  general: ''
});

const loading = ref(false);

const handleSubmit = async () => {
  errors.password = '';
  errors.general = '';
  loading.value = true;
  
  try {
    const response = await authStore.adminLogin(form);
    // 2FAなしで直接ダッシュボードへ
    authStore.user = response.admin;
    authStore.isAuthenticated = true;
    authStore.guard = 'admin';
    router.push({ name: 'admin.dashboard' });
  } catch (error) {
    if (error.response?.data?.errors) {
      Object.assign(errors, error.response.data.errors);
    } else {
      errors.general = error.response?.data?.message || 'ログインに失敗しました';
    }
  } finally {
    loading.value = false;
  }
};
</script>
