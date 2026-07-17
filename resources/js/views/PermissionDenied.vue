<template>
  <div class="p-6 flex items-center justify-center min-h-[60vh]">
    <div class="max-w-md w-full bg-white rounded-lg shadow border border-gray-200 p-8 text-center">
      <div class="mx-auto w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Permission Denied</h1>
      <p class="text-gray-600 mb-6">
        {{ message }}
      </p>
      <router-link
        to="/dashboard"
        class="inline-flex items-center px-5 py-2.5 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors"
      >
        Back to Dashboard
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

const message = computed(() => {
  return route.query.message
    || 'You do not have permission to access this page.';
});
</script>
