<template>
  <div>
    <h2 class="text-2xl font-bold text-center mb-6">生徒ログイン</h2>
    
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
  
  try {
    // 現時点では保護者ログインと同じ処理を使用
    // 後で生徒専用のログイン処理を追加する場合は変更可能
    await authStore.parentLogin(form);
    router.push({ name: 'parent.verify2fa' });
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
