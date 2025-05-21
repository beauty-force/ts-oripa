<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="flex flex-col items-center">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-theme"></div>
      <p class="mt-2 text-white text-sm">{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    default: '読み込み中...'
  }
})
</script> 