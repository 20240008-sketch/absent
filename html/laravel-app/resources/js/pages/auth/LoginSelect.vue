<template>
  <div>
    <h2 class="text-2xl font-bold text-center mb-6">欠席連絡システム</h2>
    <p class="text-center text-gray-600 mb-8">ログインするユーザーを選択してください</p>
    
    <div class="space-y-4">
      <Button
        variant="primary"
        class="w-full py-4 text-lg"
        @click="goToStudentLogin"
      >
        生徒ログイン
      </Button>
      
      <Button
        variant="primary"
        class="w-full py-4 text-lg"
        @click="goToParentLogin"
      >
        保護者ログイン
      </Button>

      <Button
        variant="secondary"
        class="w-full py-4 text-lg"
        @click="goToRegister"
      >
        初回登録
      </Button>

      <div class="text-center mt-6">
        <button
          @click="goToAdminLogin"
          class="text-sm text-gray-500 hover:text-gray-700 underline"
        >
          管理者ログインはこちら
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import Button from '../../components/Button.vue';

const router = useRouter();

const goToStudentLogin = () => {
  // 現時点では生徒ログインは保護者ログインと同じページに遷移
  // 後で生徒専用ログインページを作成する場合は変更可能
  router.push({ name: 'student.login' });
};

const goToParentLogin = () => {
  router.push({ name: 'parent.login' });
};

const goToRegister = () => {
  router.push({ name: 'register' });
};

const goToAdminLogin = () => {
  router.push({ name: 'admin.login' });
};
</script>

