<template>
  <div class="min-h-screen bg-gray-100">
    <Header user-type="parent" />
    
    <nav class="bg-white shadow-sm">
      <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
          <div class="flex gap-4 overflow-x-auto py-3">
            <router-link
              to="/parent/dashboard"
              class="px-3 py-2 text-sm font-medium rounded hover:bg-gray-100 whitespace-nowrap"
              :class="isActive('/parent/dashboard') ? 'bg-green-100 text-green-700' : 'text-gray-700'"
            >
              ダッシュボード
            </router-link>
            <router-link
              to="/parent/absences"
              class="px-3 py-2 text-sm font-medium rounded hover:bg-gray-100 whitespace-nowrap"
              :class="isActive('/parent/absences') ? 'bg-green-100 text-green-700' : 'text-gray-700'"
            >
              欠席連絡
            </router-link>
            <router-link
              v-if="authStore.needsPasswordChange"
              to="/parent/change-password"
              class="px-3 py-2 text-sm font-medium rounded hover:bg-gray-100 whitespace-nowrap bg-yellow-100 text-yellow-700"
            >
              パスワード変更（必須）
            </router-link>
          </div>
          
          <!-- ナビゲーションボタン -->
          <div class="flex gap-2 ml-4">
            <button
              v-if="canGoBack"
              @click="goBack"
              class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors"
              title="前の画面に戻る"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <button
              v-if="!isDashboard"
              @click="goHome"
              class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors"
              title="ダッシュボードに戻る"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </nav>
    
    <main class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router';
import { computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import Header from '../components/Header.vue';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const isActive = (path) => {
  return route.path.startsWith(path);
};

const isDashboard = computed(() => {
  return route.path === '/parent/dashboard';
});

const canGoBack = computed(() => {
  return window.history.length > 1 && !isDashboard.value;
});

const goBack = () => {
  router.go(-1);
};

const goHome = () => {
  router.push({ name: 'parent.dashboard' });
};
</script>
