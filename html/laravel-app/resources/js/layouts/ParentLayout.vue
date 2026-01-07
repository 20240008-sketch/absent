<template>
  <div class="min-h-screen bg-gray-100">
    <Header user-type="parent" />
    
    <nav class="bg-white shadow-sm">
      <div class="mx-auto px-4 sm:px-6 lg:px-8">
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
      </div>
    </nav>
    
    <main class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import Header from '../components/Header.vue';

const route = useRoute();
const authStore = useAuthStore();

const isActive = (path) => {
  return route.path.startsWith(path);
};
</script>
