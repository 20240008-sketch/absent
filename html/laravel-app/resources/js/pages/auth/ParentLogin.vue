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
      
      <div class="mb-4">
        <Input
          id="password"
          v-model="form.password"
          type="password"
          label="パスワード"
          required
          :error="errors.password"
        />
        <div class="mt-2 flex gap-2">
          <Button
            type="button"
            variant="secondary"
            size="sm"
            @click="savePassword"
            class="flex-1"
          >
            💾 パスワードを保存
          </Button>
          <Button
            type="button"
            variant="secondary"
            size="sm"
            @click="resetPassword"
            class="flex-1"
          >
            🔄 パスワードをリセット
          </Button>
        </div>
      </div>
      
      <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
        <p class="text-xs text-blue-800">
          💡 <strong>パスワードを忘れた場合</strong><br>
          学校から配布された「初期パスワード」でログインできます。<br>
          ログイン後、新しいパスワードに変更してください。
        </p>
      </div>
      
      <p v-if="errors.general" class="mb-4 text-sm text-red-600">{{ errors.general }}</p>
      <p v-if="successMessage" class="mb-4 text-sm text-green-600">{{ successMessage }}</p>
      
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
const successMessage = ref('');

const savePassword = () => {
  if (!form.email || !form.password) {
    errors.general = 'メールアドレスとパスワードを入力してください';
    return;
  }
  
  // ブラウザのローカルストレージに保存
  localStorage.setItem('saved_parent_email', form.email);
  localStorage.setItem('saved_parent_password', form.password);
  
  successMessage.value = 'パスワードを保存しました';
  setTimeout(() => {
    successMessage.value = '';
  }, 3000);
};

const resetPassword = () => {
  form.email = '';
  form.password = '';
  
  // ローカルストレージから削除
  localStorage.removeItem('saved_parent_email');
  localStorage.removeItem('saved_parent_password');
  
  successMessage.value = 'パスワードをリセットしました';
  setTimeout(() => {
    successMessage.value = '';
  }, 3000);
};

// ページ読み込み時に保存されたパスワードを復元
const loadSavedCredentials = () => {
  const savedEmail = localStorage.getItem('saved_parent_email');
  const savedPassword = localStorage.getItem('saved_parent_password');
  
  if (savedEmail) form.email = savedEmail;
  if (savedPassword) form.password = savedPassword;
};

// コンポーネントマウント時に実行
loadSavedCredentials();

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
