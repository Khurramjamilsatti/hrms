<template>
  <button
    type="button"
    @click="themeStore.toggleTheme()"
    class="relative inline-flex items-center justify-center rounded-xl p-2 transition-colors"
    :class="buttonClass"
    :aria-label="themeStore.isDark ? 'Switch to light theme' : 'Switch to dark theme'"
    :title="themeStore.isDark ? 'Light mode' : 'Dark mode'"
  >
    <!-- Sun (show when dark — click to go light) -->
    <svg
      v-if="themeStore.isDark"
      class="h-5 w-5"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
      />
    </svg>
    <!-- Moon (show when light — click to go dark) -->
    <svg
      v-else
      class="h-5 w-5"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
      />
    </svg>
  </button>
</template>

<script setup>
import { computed } from 'vue'
import { useThemeStore } from '@/stores/theme'

const props = defineProps({
  variant: {
    type: String,
    default: 'header', // header | light | muted
  },
})

const themeStore = useThemeStore()

const buttonClass = computed(() => {
  if (props.variant === 'header') {
    return 'text-gold hover:bg-white/10'
  }
  if (props.variant === 'light') {
    return 'text-brand dark:text-gold bg-white/10 hover:bg-white/20 dark:bg-white/5'
  }
  return 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 border border-gray-200 dark:border-white/10'
})
</script>
