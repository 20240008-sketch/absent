<template>
  <div>
    <h2 class="text-2xl font-bold text-center mb-6">欠席連絡システム</h2>
    <p class="text-center text-gray-600 mb-8">ログインするユーザーを選択してください</p>
    
    <div class="space-y-4">
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

      <!-- お試しモードボタン -->
      <div class="mt-6 p-4 bg-yellow-50 border-2 border-yellow-300 rounded-lg">
        <p class="text-sm text-yellow-800 mb-3 text-center font-semibold">
          🚀 試作段階用
        </p>
        <div class="space-y-2">
          <Button
            variant="warning"
            class="w-full py-3 text-base"
            @click="goToDemoAdmin"
          >
            お試し：管理者モード
          </Button>
          <Button
            variant="warning"
            class="w-full py-3 text-base"
            @click="goToDemoParent"
          >
            お試し：保護者モード
          </Button>
        </div>
        <p class="text-xs text-yellow-700 mt-2 text-center">
          ※ メール設定不要で各画面を確認できます
        </p>
      </div>

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
import { useAuthStore } from '../../stores/auth';
import Button from '../../components/Button.vue';

const router = useRouter();
const authStore = useAuthStore();

const goToParentLogin = () => {
  router.push({ name: 'parent.login' });
};

const goToRegister = () => {
  router.push({ name: 'register' });
};

const goToAdminLogin = () => {
  router.push({ name: 'admin.login' });
};

const goToDemoAdmin = async () => {
  try {
    // お試しモード：管理者として直接ログイン
    await authStore.demoAdminLogin();
    router.push({ name: 'admin.dashboard' });
  } catch (error) {
    console.error('お試しモード（管理者）エラー:', error);
    alert('お試しモード（管理者）の起動に失敗しました');
  }
};

const goToDemoParent = async () => {
  try {
    // お試しモード：保護者として直接ログイン
    await authStore.demoParentLogin();
    router.push({ name: 'parent.dashboard' });
  } catch (error) {
    console.error('お試しモード（保護者）エラー:', error);
    alert('お試しモード（保護者）の起動に失敗しました');
  }
};
</script>

