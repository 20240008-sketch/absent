<template>
  <div>
    <h2 class="text-2xl font-bold text-center mb-6">保護者ログイン</h2>
    
    <form @submit.prevent="handleSubmit">
      <Input
        id="email"
        v-model="form.email"
        type="email"
        label="メールアドレス"
        placeholder="email@example.com"
        required
        :error="errors.email"
      />
      
      <Input
        id="password"
        v-model="form.password"
        type="password"
        label="パスワード"
        required
        :error="errors.password"
      />
      
      <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
        <p class="text-xs text-blue-800">
          💡 <strong>パスワードを忘れた場合</strong><br>
          学校から配布された「初期パスワード」でログインできます。<br>
          ログイン後、新しいパスワードに変更してください。
        </p>
      </div>
      
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
  email: '',
  password: ''
});

const errors = reactive({
  email: '',
  password: '',
  general: ''
});

const loading = ref(false);

const handleSubmit = async () => {
  errors.email = '';
  errors.password = '';
  errors.general = '';
  loading.value = true;
  
  console.log('Login attempt with:', { email: form.email });
  
  try {
    const response = await authStore.parentLogin(form);
    console.log('Login response:', response);
    
    // 2FA不要、直接ダッシュボードへ
    if (response.needs_password_change) {
      router.push({ name: 'parent.changePassword' });
    } else {
      router.push({ name: 'parent.dashboard' });
    }
  } catch (error) {
    console.error('Login error:', error);
    console.error('Error response:', error.response?.data);
    
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
