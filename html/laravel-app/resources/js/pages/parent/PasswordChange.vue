<template>
  <div>
    <h1 class="text-3xl font-bold mb-6">パスワード変更</h1>
    
    <div class="bg-white rounded-lg shadow p-6 max-w-md">
      <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
        <p class="text-sm text-yellow-800">
          ⚠️ <strong>セキュリティのため、パスワードを変更してください</strong><br>
          初期パスワードでログインした場合、新しいパスワードの設定が必要です。
        </p>
      </div>
      
      <form @submit.prevent="handleSubmit">
        <Input
          id="current_password"
          v-model="form.current_password"
          type="password"
          label="現在のパスワード（または初期パスワード）"
          required
          :error="errors.current_password"
        />
        
        <Input
          id="new_password"
          v-model="form.new_password"
          type="password"
          label="新しいパスワード（8文字以上）"
          required
          :error="errors.new_password"
        />
        
        <Input
          id="new_password_confirmation"
          v-model="form.new_password_confirmation"
          type="password"
          label="新しいパスワード（確認）"
          required
          :error="errors.new_password_confirmation"
        />
        
        <p v-if="errors.general" class="mb-4 text-sm text-red-600">{{ errors.general }}</p>
        <p v-if="success" class="mb-4 text-sm text-green-600">{{ success }}</p>
        
        <Button
          type="submit"
          variant="primary"
          :disabled="loading"
        >
          {{ loading ? '変更中...' : 'パスワードを変更' }}
        </Button>
      </form>
    </div>
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
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
});

const errors = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
  general: ''
});

const loading = ref(false);
const success = ref('');

const handleSubmit = async () => {
  Object.keys(errors).forEach(key => errors[key] = '');
  success.value = '';
  loading.value = true;
  
  try {
    await authStore.changePassword(form);
    success.value = 'パスワードを変更しました';
    
    setTimeout(() => {
      router.push({ name: 'parent.dashboard' });
    }, 1500);
  } catch (error) {
    if (error.response?.data?.errors) {
      Object.assign(errors, error.response.data.errors);
    } else {
      errors.general = error.response?.data?.message || 'パスワード変更に失敗しました';
    }
  } finally {
    loading.value = false;
  }
};
</script>
