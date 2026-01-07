<template>
  <div class="min-h-screen bg-gray-100">
    <Header user-type="admin" />
    
    <nav class="bg-white shadow-sm">
      <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex gap-4 overflow-x-auto py-3">
          <router-link
            to="/admin/dashboard"
            class="px-3 py-2 text-sm font-medium rounded hover:bg-gray-100 whitespace-nowrap"
            :class="isActive('/admin/dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700'"
          >
            ダッシュボード
          </router-link>
          <router-link
            to="/admin/classes"
            class="px-3 py-2 text-sm font-medium rounded hover:bg-gray-100 whitespace-nowrap"
            :class="isActive('/admin/classes') ? 'bg-blue-100 text-blue-700' : 'text-gray-700'"
          >
            クラス管理
          </router-link>
          <router-link
            to="/admin/students"
            class="px-3 py-2 text-sm font-medium rounded hover:bg-gray-100 whitespace-nowrap"
            :class="isActive('/admin/students') ? 'bg-blue-100 text-blue-700' : 'text-gray-700'"
          >
            生徒管理
          </router-link>
          <router-link
            to="/admin/parents"
            class="px-3 py-2 text-sm font-medium rounded hover:bg-gray-100 whitespace-nowrap"
            :class="isActive('/admin/parents') ? 'bg-blue-100 text-blue-700' : 'text-gray-700'"
          >
            保護者管理
          </router-link>
          <router-link
            to="/admin/import"
            class="px-3 py-2 text-sm font-medium rounded hover:bg-gray-100 whitespace-nowrap"
            :class="isActive('/admin/import') ? 'bg-blue-100 text-blue-700' : 'text-gray-700'"
          >
            CSVインポート
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
import Header from '../components/Header.vue';

const route = useRoute();

const isActive = (path) => {
  return route.path.startsWith(path);
};
</script>
