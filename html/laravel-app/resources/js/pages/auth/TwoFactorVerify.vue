<template>
  <div>
    <h2 class="text-2xl font-bold text-center mb-6">二段階認証</h2>
    
    <p class="text-sm text-gray-600 mb-6 text-center">
      メールに送信された6桁の認証コードを入力してください
    </p>
    
    <form @submit.prevent="handleSubmit">
      <Input
        id="code"
        v-model="form.code"
        type="text"
        label="認証コード"
        placeholder="123456"
        required
        maxlength="6"
        :error="errors.code"
      />
      
      <p v-if="errors.general" class="mb-4 text-sm text-red-600">{{ errors.general }}</p>
      
      <Button
        type="submit"
        variant="primary"
        class="w-full"
        :disabled="loading"
      >
        {{ loading ? '確認中...' : '確認' }}
      </Button>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import Input from '../../components/Input.vue';
import Button from '../../components/Button.vue';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const form = reactive({
  code: ''
});

const errors = reactive({
  code: '',
  general: ''
});

const loading = ref(false);

const handleSubmit = async () => {
  errors.code = '';
  errors.general = '';
  loading.value = true;
  
  try {
    await authStore.verify2FA(form.code);
    
    // 認証成功後、適切なダッシュボードにリダイレクト
    const guard = route.meta.guard || authStore.guard;
    
    // パスワード変更が必要な場合
    if (guard === 'parent' && authStore.needsPasswordChange) {
      router.push({ name: 'parent.changePassword' });
    } else {
      router.push({ name: `${guard}.dashboard` });
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      Object.assign(errors, error.response.data.errors);
    } else {
      errors.general = error.response?.data?.message || '認証に失敗しました';
    }
  } finally {
    loading.value = false;
  }
};
</script>
