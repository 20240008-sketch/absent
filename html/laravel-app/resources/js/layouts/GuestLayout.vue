<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8 relative">
      <!-- ナビゲーションボタン -->
      <div class="absolute top-4 right-4 flex gap-2">
        <button
          v-if="canGoBack"
          @click="goBack"
          class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors"
          title="前の画面に戻る"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button
          v-if="!isHome"
          @click="goHome"
          class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors"
          title="最初の画面に戻る"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
        </button>
      </div>

      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">欠席連絡システム</h1>
      </div>
      <RouterView />
    </div>
  </div>
</template>

<script setup>
import { RouterView, useRouter, useRoute } from 'vue-router';
import { computed } from 'vue';

const router = useRouter();
const route = useRoute();

const isHome = computed(() => {
  return route.name === 'login.select' || route.path === '/' || route.path === '/login';
});

const canGoBack = computed(() => {
  return window.history.length > 1 && !isHome.value;
});

const goBack = () => {
  router.go(-1);
};

const goHome = () => {
  router.push({ name: 'login.select' });
};
</script>
