<template>
  <div class="mb-4">
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <input
      :id="id"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :class="inputClasses"
      @input="handleInput"
    />
    <p v-if="errorMessage" class="mt-1 text-sm text-red-600">{{ errorMessage }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  id: String,
  label: String,
  type: {
    type: String,
    default: 'text'
  },
  modelValue: [String, Number],
  placeholder: String,
  required: Boolean,
  disabled: Boolean,
  error: [String, Array]
});

const emit = defineEmits(['update:modelValue']);

// エラーメッセージを文字列として取得
const errorMessage = computed(() => {
  if (!props.error) return '';
  if (Array.isArray(props.error)) {
    return props.error[0] || '';
  }
  return props.error;
});

const inputClasses = computed(() => {
  const base = 'block w-full rounded border px-3 py-2 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0';
  const state = errorMessage.value 
    ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
    : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500';
  const disabled = props.disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white';
  
  return [base, state, disabled].join(' ');
});

const handleInput = (event) => {
  emit('update:modelValue', event.target.value);
};
</script>
