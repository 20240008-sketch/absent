<template>
  <header class="bg-white shadow">
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center">
          <router-link :to="homeRoute" class="text-xl font-bold text-gray-800">
            欠席連絡システム
          </router-link>
        </div>
        
        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-600">{{ userName }}</span>
          <button
            @click="handleLogout"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-700"
          >
            ログアウト
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const props = defineProps({
  userType: {
    type: String,
    required: true,
    validator: (value) => ['admin', 'parent'].includes(value)
  }
});

const router = useRouter();
const authStore = useAuthStore();

const userName = computed(() => authStore.user?.name || authStore.user?.parent_name || '');
const homeRoute = computed(() => props.userType === 'admin' ? '/admin/dashboard' : '/parent/dashboard');

const handleLogout = async () => {
  await authStore.logout();
  router.push(`/${props.userType}/login`);
};
</script>
