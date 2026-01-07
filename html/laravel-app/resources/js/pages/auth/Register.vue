<template>
  <div>
    <h2 class="text-2xl font-bold text-center mb-6">初回登録</h2>
    <p class="text-center text-gray-600 mb-8">保護者アカウントを登録してください</p>
    
    <form @submit.prevent="handleSubmit">
      <Input
        id="name"
        v-model="form.name"
        type="text"
        label="お名前"
        placeholder="山田太郎"
        required
        :error="errors.name"
      />
      
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
        placeholder="8文字以上"
        required
        :error="errors.password"
      />
      
      <Input
        id="password_confirmation"
        v-model="form.password_confirmation"
        type="password"
        label="パスワード（確認）"
        placeholder="パスワードを再入力"
        required
        :error="errors.password_confirmation"
      />
      
      <Input
        id="phone"
        v-model="form.phone"
        type="tel"
        label="電話番号"
        placeholder="090-1234-5678"
        :error="errors.phone"
      />
      
      <p v-if="errors.general" class="mb-4 text-sm text-red-600">{{ errors.general }}</p>
      
      <Button
        type="submit"
        variant="primary"
        class="w-full"
        :disabled="loading"
      >
        {{ loading ? '登録中...' : '登録' }}
      </Button>
      
      <div class="text-center mt-4">
        <router-link
          to="/login"
          class="text-sm text-blue-600 hover:text-blue-800 underline"
        >
          ログイン画面に戻る
        </router-link>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Input from '../../components/Input.vue';
import Button from '../../components/Button.vue';

const router = useRouter();

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: ''
});

const errors = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  phone: '',
  general: ''
});

const loading = ref(false);

const handleSubmit = async () => {
  // エラーをクリア
  Object.keys(errors).forEach(key => errors[key] = '');
  loading.value = true;
  
  try {
    const response = await axios.post('/api/register', form);
    
    // 登録成功
    alert('登録が完了しました。ログインしてください。');
    router.push({ name: 'parent.login' });
  } catch (error) {
    if (error.response?.data?.errors) {
      Object.assign(errors, error.response.data.errors);
    } else {
      errors.general = error.response?.data?.message || '登録に失敗しました';
    }
  } finally {
    loading.value = false;
  }
};
</script>
