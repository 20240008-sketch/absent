<template>
  <div>
    <h2 class="text-2xl font-bold text-center mb-6">初回登録 - 保護者情報入力</h2>
    <p class="text-center text-gray-600 mb-8">保護者情報を入力してください</p>
    
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
        label="保護者メールアドレス（二段階認証用）"
        placeholder="parent@example.com"
        required
        :error="errors.email"
      />
      
      <Input
        id="password"
        v-model="form.password"
        type="password"
        label="新しいパスワード"
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
      
      <div class="mb-4">
        <label for="relationship" class="block text-sm font-medium text-gray-700 mb-1">
          続柄 <span class="text-red-600">*</span>
        </label>
        <select
          id="relationship"
          v-model="form.relationship"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          :class="{ 'border-red-500': errors.relationship }"
        >
          <option value="">選択してください</option>
          <option value="父">父</option>
          <option value="母">母</option>
          <option value="祖父">祖父</option>
          <option value="祖母">祖母</option>
          <option value="その他">その他</option>
        </select>
        <p v-if="errors.relationship" class="mt-1 text-sm text-red-600">{{ errors.relationship }}</p>
      </div>
      
      <Input
        id="phone"
        v-model="form.phone"
        type="tel"
        label="電話番号"
        placeholder="090-1234-5678"
        required
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
import { reactive, ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import Input from '../../components/Input.vue';
import Button from '../../components/Button.vue';

const router = useRouter();
const route = useRoute();

const form = reactive({
  classroom_email: '',
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  relationship: '',
  phone: ''
});

const errors = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  relationship: '',
  phone: '',
  general: ''
});

const loading = ref(false);

onMounted(() => {
  // Classroom認証画面から渡されたメールアドレスを取得
  if (route.query.classroom_email) {
    form.classroom_email = route.query.classroom_email;
  } else {
    // Classroomメールアドレスがない場合は最初の画面に戻す
    router.push({ name: 'register' });
  }
});

const handleSubmit = async () => {
  // エラーをクリア
  Object.keys(errors).forEach(key => errors[key] = '');
  loading.value = true;
  
  try {
    const response = await axios.post('/api/register/parent', form);
    
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
