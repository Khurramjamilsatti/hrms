<template>
  <div class="flex min-h-screen bg-surface">
    <aside class="flex w-64 flex-col bg-brand text-white shadow-soft">
      <div class="border-b border-white/10 p-5">
        <AppLogo theme="brand" size="md" tagline="Website CMS" link-to="/cms/content" />
      </div>

      <nav class="flex-1 space-y-1 p-4">
        <router-link
          to="/cms/content"
          class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition"
          :class="isActive('/cms/content') ? 'bg-accent text-white' : 'text-white/70 hover:bg-white/10 hover:text-white'"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          Landing Content
        </router-link>
        <a
          href="/"
          target="_blank"
          rel="noopener"
          class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium text-white/70 transition hover:bg-white/10 hover:text-white"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
          </svg>
          View website
        </a>
      </nav>

      <div class="border-t border-white/10 p-4">
        <div class="mb-3 rounded-xl bg-white/5 px-3 py-3 ring-1 ring-white/10">
          <p class="truncate text-sm font-semibold text-white">{{ user?.name }}</p>
          <p class="truncate text-xs capitalize text-white/50">{{ user?.role }} · {{ user?.email }}</p>
        </div>
        <button
          type="button"
          class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 px-4 py-2.5 text-sm font-medium text-white/75 transition hover:border-accent/40 hover:bg-accent/20 hover:text-white"
          @click="handleLogout"
        >
          Sign out
        </button>
      </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
      <header class="flex items-center justify-between bg-brand px-6 py-4 shadow-soft">
        <h2 class="text-xl font-bold text-white">{{ pageTitle }}</h2>
        <ThemeToggle variant="header" />
      </header>
      <main class="flex-1 overflow-auto bg-surface">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useCmsAuthStore } from '@/stores/cmsAuth';
import ThemeToggle from '@/components/ThemeToggle.vue';
import AppLogo from '@/components/AppLogo.vue';

const route = useRoute();
const router = useRouter();
const cmsAuth = useCmsAuthStore();

const user = computed(() => cmsAuth.user);
const pageTitle = computed(() => {
  if (route.path.startsWith('/cms/content')) return 'Landing Content';
  return 'Website CMS';
});

const isActive = (path) => route.path === path || route.path.startsWith(path + '/');

async function handleLogout() {
  await cmsAuth.logout();
  router.push('/cms/login');
}
</script>
