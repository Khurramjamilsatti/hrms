<template>
  <Teleport to="body">
    <Transition name="dialog-fade">
      <div
        v-if="state.open"
        class="fixed inset-0 z-[200] flex items-center justify-center bg-black/50 px-4"
        @click.self="handleCancel"
      >
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden border border-gray-200">
          <div class="px-6 py-5">
            <div class="flex items-start gap-4">
              <div
                class="flex-shrink-0 w-11 h-11 rounded-full flex items-center justify-center"
                :class="iconWrapClass"
              >
                <!-- Danger -->
                <svg v-if="state.variant === 'danger'" class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
                <!-- Success -->
                <svg v-else-if="state.variant === 'success'" class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <!-- Primary / info -->
                <svg v-else class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                </svg>
              </div>

              <div class="flex-1 min-w-0 pt-0.5">
                <h3 class="text-lg font-bold text-gray-900">{{ state.title }}</h3>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">{{ state.message }}</p>
              </div>
            </div>
          </div>

          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
            <button
              type="button"
              @click="handleCancel"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              {{ state.cancelText }}
            </button>
            <button
              type="button"
              @click="handleConfirm"
              class="px-5 py-2 text-sm font-medium text-white rounded-lg transition-colors shadow"
              :class="confirmButtonClass"
            >
              {{ state.confirmText }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue';
import { useDialog } from '@/composables/useDialog';

const { state, handleConfirm, handleCancel } = useDialog();

const iconWrapClass = computed(() => ({
  danger: 'bg-red-50',
  success: 'bg-green-50',
  primary: 'bg-gray-100',
}[state.variant] || 'bg-gray-100'));

const confirmButtonClass = computed(() => ({
  danger: 'bg-red-600 hover:bg-red-700',
  success: 'bg-green-600 hover:bg-green-700',
  primary: 'bg-accent hover:bg-accent-dark',
}[state.variant] || 'bg-accent hover:bg-accent-dark'));
</script>

<style scoped>
.dialog-fade-enter-active,
.dialog-fade-leave-active {
  transition: opacity 0.2s ease;
}
.dialog-fade-enter-active .bg-white,
.dialog-fade-leave-active .bg-white {
  transition: transform 0.2s ease, opacity 0.2s ease;
}
.dialog-fade-enter-from,
.dialog-fade-leave-to {
  opacity: 0;
}
.dialog-fade-enter-from .bg-white,
.dialog-fade-leave-to .bg-white {
  transform: translateY(8px) scale(0.98);
  opacity: 0;
}
</style>
