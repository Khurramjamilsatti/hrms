import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'

const STORAGE_KEY = 'hrms-theme'

function getPreferredTheme() {
  if (typeof window === 'undefined') return 'light'
  const saved = localStorage.getItem(STORAGE_KEY)
  if (saved === 'light' || saved === 'dark') return saved
  return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
}

function applyThemeClass(theme) {
  if (typeof document === 'undefined') return
  const root = document.documentElement
  root.classList.toggle('dark', theme === 'dark')
  root.setAttribute('data-theme', theme)
  root.style.colorScheme = theme
}

// Apply immediately so first paint matches stored preference
if (typeof document !== 'undefined') {
  applyThemeClass(getPreferredTheme())
}

export const useThemeStore = defineStore('theme', () => {
  const theme = ref(getPreferredTheme())

  const isDark = computed(() => theme.value === 'dark')
  const isLight = computed(() => theme.value === 'light')

  function setTheme(next) {
    if (next !== 'light' && next !== 'dark') return
    theme.value = next
    localStorage.setItem(STORAGE_KEY, next)
    applyThemeClass(next)
  }

  function toggleTheme() {
    setTheme(theme.value === 'dark' ? 'light' : 'dark')
  }

  watch(theme, (value) => applyThemeClass(value), { immediate: true })

  return {
    theme,
    isDark,
    isLight,
    setTheme,
    toggleTheme,
  }
})
