<template>
  <div class="min-h-screen flex">
    <!-- Brand panel -->
    <div class="hidden lg:flex lg:w-[52%] relative overflow-hidden bg-brand text-white flex-col justify-between p-12 xl:p-16">
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-accent/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 rounded-full bg-gold/10 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/3 w-64 h-64 rounded-full bg-white/[0.03] blur-2xl"></div>
        <svg class="absolute inset-0 w-full h-full opacity-[0.04]" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="grid" width="32" height="32" patternUnits="userSpaceOnUse">
              <path d="M 32 0 L 0 0 0 32" fill="none" stroke="white" stroke-width="1"/>
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
      </div>

      <div class="relative z-10">
        <div class="flex items-center gap-3">
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 ring-1 ring-white/10">
            <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-gold">HRMS</h1>
            <p class="text-sm text-white/50 font-medium">Payroll Digital</p>
          </div>
        </div>
      </div>

      <div class="relative z-10 max-w-lg">
        <h2 class="text-4xl xl:text-5xl font-bold leading-tight tracking-tight">
          Manage your workforce with confidence
        </h2>
        <p class="mt-5 text-lg text-white/60 leading-relaxed">
          Streamline payroll, attendance, leaves, and employee records — all in one unified platform.
        </p>

        <div class="mt-10 grid grid-cols-2 gap-3">
          <div
            v-for="feature in features"
            :key="feature.label"
            class="flex items-center gap-3 rounded-xl bg-white/5 ring-1 ring-white/10 px-4 py-3 backdrop-blur-sm"
          >
            <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center">
              <svg class="w-4.5 h-4.5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
              </svg>
            </div>
            <span class="text-sm font-medium text-white/80">{{ feature.label }}</span>
          </div>
        </div>
      </div>

      <p class="relative z-10 text-sm text-white/40">
        &copy; {{ new Date().getFullYear() }} Payroll Digital. All rights reserved.
      </p>
    </div>

    <!-- Login form -->
    <div class="flex-1 flex flex-col min-h-screen bg-surface">
      <!-- Mobile header -->
      <div class="lg:hidden bg-brand px-6 py-8 text-white">
        <div class="flex items-center gap-3">
          <div class="bg-white/10 rounded-lg p-2">
            <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <div>
            <h1 class="text-lg font-bold text-gold">HRMS</h1>
            <p class="text-xs text-white/50">Payroll Digital</p>
          </div>
        </div>
      </div>

      <div class="flex-1 flex items-center justify-center px-6 py-10 sm:px-10 relative">
        <div class="absolute top-4 right-4 sm:top-6 sm:right-6">
          <ThemeToggle variant="muted" />
        </div>
        <div class="w-full max-w-md">
          <div class="mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-ink tracking-tight">Welcome back</h2>
            <p class="mt-2 text-ink-muted">Sign in to your account to continue</p>
          </div>

          <div class="bg-surface-card rounded-2xl shadow-card border border-surface-border p-6 sm:p-8">
            <form @submit.prevent="handleLogin" class="space-y-5">
              <div
                v-if="error"
                class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl"
                role="alert"
              >
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium">{{ error }}</p>
              </div>

              <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                  Email address
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    autocomplete="email"
                    class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-accent focus:border-transparent transition-shadow"
                    placeholder="you@company.com"
                  />
                </div>
              </div>

              <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                  Password
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                  </div>
                  <input
                    id="password"
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    required
                    autocomplete="current-password"
                    class="w-full pl-11 pr-12 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-accent focus:border-transparent transition-shadow"
                    placeholder="Enter your password"
                  />
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                  >
                    <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
              </div>

              <button
                type="submit"
                :disabled="loading"
                class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-accent text-white font-semibold rounded-xl hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent disabled:opacity-60 disabled:cursor-not-allowed transition-all duration-200"
              >
                <svg
                  v-if="loading"
                  class="animate-spin h-5 w-5 text-white"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                {{ loading ? 'Signing in...' : 'Sign in' }}
              </button>
            </form>
          </div>

          <p class="mt-8 text-center text-sm text-brand/40 lg:hidden">
            &copy; {{ new Date().getFullYear() }} Payroll Digital
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import ThemeToggle from '@/components/ThemeToggle.vue';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  email: '',
  password: '',
});

const loading = ref(false);
const error = ref(null);
const showPassword = ref(false);

const features = [
  { label: 'Payroll', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
  { label: 'Attendance', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
  { label: 'Employees', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
  { label: 'Leave Mgmt', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
];

const handleLogin = async () => {
  loading.value = true;
  error.value = null;

  try {
    await authStore.login(form.value);
    router.push('/dashboard');
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed. Please check your credentials.';
  } finally {
    loading.value = false;
  }
};
</script>
