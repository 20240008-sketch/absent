<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 overflow-y-auto"
    @click.self="handleClose"
  >
    <div class="flex min-h-screen items-center justify-center p-4">
      <div class="fixed inset-0 bg-black opacity-30" @click="handleClose"></div>
      
      <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md p-6 z-10">
        <div class="flex justify-between items-start mb-4">
          <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
          <button
            type="button"
            class="text-gray-400 hover:text-gray-500"
            @click="handleClose"
          >
            <span class="text-2xl">&times;</span>
          </button>
        </div>
        
        <div class="mb-6">
          <slot />
        </div>
        
        <div class="flex justify-end gap-3">
          <slot name="footer">
            <Button variant="secondary" @click="handleClose">
              キャンセル
            </Button>
            <Button variant="primary" @click="handleConfirm">
              確定
            </Button>
          </slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Button from './Button.vue';

defineProps({
  show: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['close', 'confirm']);

const handleClose = () => {
  emit('close');
};

const handleConfirm = () => {
  emit('confirm');
};
</script>
