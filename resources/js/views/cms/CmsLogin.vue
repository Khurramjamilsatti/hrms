<template>
  <div class="flex min-h-screen">
    <div class="relative hidden w-[46%] overflow-hidden bg-brand text-white lg:flex lg:flex-col lg:justify-between p-12">
      <div class="pointer-events-none absolute inset-0">
        <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-accent/25 blur-3xl" />
        <div class="absolute bottom-0 left-0 h-64 w-64 rounded-full bg-gold/10 blur-3xl" />
      </div>
      <div class="relative z-10">
        <AppLogo theme="brand" size="lg" tagline="Website Content CMS" />
        <p class="mt-6 max-w-sm text-sm leading-relaxed text-white/60">
          Manage landing page copy, pricing, FAQs, and legal pages — separate from the main application login.
        </p>
      </div>
      <p class="relative z-10 text-xs text-white/40">Payroll Digital · CMS only</p>
    </div>

    <div class="relative flex flex-1 flex-col justify-center bg-surface px-6 py-10 sm:px-10">
      <div class="absolute right-4 top-4 sm:right-6 sm:top-6">
        <ThemeToggle variant="muted" />
      </div>

      <div class="mx-auto w-full max-w-md">
        <div class="mb-8 lg:hidden">
          <AppLogo theme="light" size="md" tagline="Website CMS" />
          <h1 class="mt-4 text-2xl font-bold text-ink">Sign in</h1>
        </div>
        <div class="mb-8 hidden lg:block">
          <h2 class="text-3xl font-bold tracking-tight text-ink">Sign in to CMS</h2>
          <p class="mt-2 text-sm text-ink-muted">Use your CMS credentials — not your app login.</p>
        </div>

        <form class="rounded-2xl border border-surface-border bg-surface-card p-6 shadow-card sm:p-8 space-y-5" @submit.prevent="handleLogin">
          <div v-if="error" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
            {{ error }}
          </div>

          <div>
            <label for="cms-email" class="mb-2 block text-sm font-semibold text-ink-soft">Email</label>
            <input
              id="cms-email"
              v-model="form.email"
              type="email"
              required
              autocomplete="username"
              class="w-full rounded-xl border border-surface-border bg-surface-card px-4 py-3 text-ink focus:border-transparent focus:ring-2 focus:ring-accent"
              placeholder="cms@payroll-digital.com"
            />
          </div>

          <div>
            <label for="cms-password" class="mb-2 block text-sm font-semibold text-ink-soft">Password</label>
            <input
              id="cms-password"
              v-model="form.password"
              type="password"
              required
              autocomplete="current-password"
              class="w-full rounded-xl border border-surface-border bg-surface-card px-4 py-3 text-ink focus:border-transparent focus:ring-2 focus:ring-accent"
              placeholder="Enter password"
            />
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="flex w-full items-center justify-center rounded-xl bg-accent px-4 py-3 text-sm font-semibold text-white transition hover:bg-accent-dark disabled:opacity-60"
          >
            {{ loading ? 'Signing in…' : 'Sign in to CMS' }}
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-ink-muted">
          Looking for the app?
          <router-link to="/login" class="font-semibold text-accent hover:text-accent-dark">Employee login</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useCmsAuthStore } from '@/stores/cmsAuth';
import ThemeToggle from '@/components/ThemeToggle.vue';
import AppLogo from '@/components/AppLogo.vue';

const router = useRouter();
const cmsAuth = useCmsAuthStore();

const form = ref({ email: '', password: '' });
const loading = ref(false);
const error = ref('');

async function handleLogin() {
  loading.value = true;
  error.value = '';
  try {
    await cmsAuth.login(form.value);
    router.push('/cms/content');
  } catch (err) {
    error.value = err.response?.data?.message
      || err.response?.data?.errors?.email?.[0]
      || 'CMS login failed.';
  } finally {
    loading.value = false;
  }
}
</script>
