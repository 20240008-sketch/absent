<template>
  <div>
    <h2 class="text-2xl font-bold text-center mb-6">2段階認証</h2>
    
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded">
      <p class="text-sm text-blue-800">
        📧 <strong>{{ email }}</strong> にメールで認証コードを送信しました。<br>
        メールに記載された6桁のコードを入力してください。
      </p>
    </div>
    
    <form @submit.prevent="handleSubmit">
      <Input
        id="code"
        v-model="form.code"
        type="text"
        label="認証コード（6桁）"
        placeholder="000000"
        required
        maxlength="6"
        :error="errors.code"
        autocomplete="one-time-code"
      />
      
      <p v-if="errors.general" class="mb-4 text-sm text-red-600">{{ errors.general }}</p>
      
      <Button
        type="submit"
        variant="primary"
        class="w-full mb-3"
        :disabled="loading || form.code.length !== 6"
      >
        {{ loading ? '確認中...' : '認証する' }}
      </Button>
      
      <Button
        type="button"
        variant="secondary"
        class="w-full"
        @click="resendCode"
        :disabled="resending || resendCooldown > 0"
      >
        {{ resending ? '送信中...' : resendCooldown > 0 ? `再送信 (${resendCooldown}秒)` : 'コードを再送信' }}
      </Button>
    </form>
    
    <div class="mt-4 text-center">
      <button
        @click="goBack"
        class="text-sm text-gray-600 hover:text-gray-800 underline"
      >
        ← ログイン画面に戻る
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import Input from '../../components/Input.vue';
import Button from '../../components/Button.vue';
import axios from 'axios';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const email = ref(route.query.email || '');

const form = reactive({
  code: ''
});

const errors = reactive({
  code: '',
  general: ''
});

const loading = ref(false);
const resending = ref(false);
const resendCooldown = ref(0);
let cooldownInterval = null;

onMounted(() => {
  if (!email.value) {
    router.push({ name: 'parent.login' });
  }
});

onUnmounted(() => {
  if (cooldownInterval) {
    clearInterval(cooldownInterval);
  }
});

const handleSubmit = async () => {
  errors.code = '';
  errors.general = '';
  loading.value = true;
  
  try {
    const response = await axios.post('/api/parent/verify-2fa', {
      email: email.value,
      code: form.code
    });
    
    // 認証成功
    authStore.setUser(response.data.parent, 'parent');
    router.push({ name: 'parent.dashboard' });
  } catch (error) {
    console.error('2FA verification error:', error);
    
    if (error.response?.data?.errors) {
      Object.assign(errors, error.response.data.errors);
    } else {
      errors.general = error.response?.data?.message || '認証に失敗しました';
    }
  } finally {
    loading.value = false;
  }
};

const resendCode = async () => {
  resending.value = true;
  
  try {
    await axios.post('/api/parent/resend-2fa', {
      email: email.value
    });
    
    errors.general = '';
    errors.code = '';
    
    // クールダウン開始（60秒）
    resendCooldown.value = 60;
    cooldownInterval = setInterval(() => {
      resendCooldown.value--;
      if (resendCooldown.value <= 0) {
        clearInterval(cooldownInterval);
      }
    }, 1000);
    
  } catch (error) {
    errors.general = '認証コードの再送信に失敗しました';
  } finally {
    resending.value = false;
  }
};

const goBack = () => {
  router.push({ name: 'parent.login' });
};
</script>
