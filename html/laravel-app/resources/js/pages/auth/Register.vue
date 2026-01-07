<template>
  <div>
    <h2 class="text-2xl font-bold text-center mb-6">初回登録 - Google Classroom認証</h2>
    <p class="text-center text-gray-600 mb-8">Google Classroomのメールアドレスとパスワードを入力してください</p>
    
    <form @submit.prevent="handleSubmit">
      <Input
        id="classroom_email"
        v-model="form.classroom_email"
        type="email"
        label="Classroomメールアドレス"
        placeholder="classroom@example.com"
        required
        :error="errors.classroom_email"
      />
      
      <Input
        id="classroom_password"
        v-model="form.classroom_password"
        type="password"
        label="Classroomパスワード"
        required
        :error="errors.classroom_password"
      />
      
      <p v-if="errors.general" class="mb-4 text-sm text-red-600">{{ errors.general }}</p>
      
      <Button
        type="submit"
        variant="primary"
        class="w-full"
        :disabled="loading"
      >
        {{ loading ? '認証中...' : '次へ' }}
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
  classroom_email: '',
  classroom_password: ''
});

const errors = reactive({
  classroom_email: '',
  classroom_password: '',
  general: ''
});

const loading = ref(false);

const handleSubmit = async () => {
  // エラーをクリア
  Object.keys(errors).forEach(key => errors[key] = '');
  loading.value = true;
  
  try {
    const response = await axios.post('/api/register/verify-classroom', form);
    
    // Classroom認証成功、保護者情報登録画面へ
    router.push({ 
      name: 'register.parent', 
      query: { 
        classroom_email: form.classroom_email 
      } 
    });
  } catch (error) {
    if (error.response?.data?.errors) {
      Object.assign(errors, error.response.data.errors);
    } else {
      errors.general = error.response?.data?.message || 'Classroom認証に失敗しました';
    }
  } finally {
    loading.value = false;
  }
};
</script>
