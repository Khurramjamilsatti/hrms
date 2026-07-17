<template>
  <div class="min-h-screen bg-surface text-ink flex flex-col">
    <header class="sticky top-0 z-40 border-b border-white/10 bg-brand/95 backdrop-blur-md">
      <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8">
        <AppLogo theme="brand" size="sm" variant="compact" :link-to="'/'" />

        <nav class="hidden items-center gap-1 md:flex">
          <a href="/#features" class="nav-link">Features</a>
          <a href="/#pricing" class="nav-link">Pricing</a>
          <a href="/#faq" class="nav-link">FAQ</a>
          <router-link to="/pages/about" class="nav-link">About</router-link>
          <a href="/#contact" class="nav-link">Contact</a>
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
          <ThemeToggle variant="header" />
          <router-link
            v-if="isAuthenticated"
            to="/dashboard"
            class="inline-flex items-center rounded-xl bg-accent px-3.5 py-2 text-sm font-semibold text-white shadow-card transition hover:bg-accent-dark sm:px-4"
          >
            Dashboard
          </router-link>
          <router-link
            v-else
            to="/login"
            class="inline-flex items-center rounded-xl bg-accent px-3.5 py-2 text-sm font-semibold text-white shadow-card transition hover:bg-accent-dark sm:px-4"
          >
            Sign In
          </router-link>
        </div>
      </div>
    </header>

    <main class="flex-1">
      <slot />
    </main>

    <footer class="border-t border-surface-border bg-brand text-white">
      <div class="mx-auto grid max-w-6xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-4 lg:px-8">
        <div class="md:col-span-1">
          <AppLogo theme="brand" size="sm" variant="compact" />
          <p class="mt-3 text-sm leading-relaxed text-white/60">
            {{ settings.brand_tagline || 'Modern HR & payroll for growing teams.' }}
          </p>
          <div v-if="hasSocial" class="mt-5 flex gap-3">
            <a v-if="settings.social_linkedin" :href="settings.social_linkedin" target="_blank" rel="noopener" class="social-link" aria-label="LinkedIn">in</a>
            <a v-if="settings.social_twitter" :href="settings.social_twitter" target="_blank" rel="noopener" class="social-link" aria-label="Twitter">X</a>
            <a v-if="settings.social_facebook" :href="settings.social_facebook" target="_blank" rel="noopener" class="social-link" aria-label="Facebook">f</a>
          </div>
        </div>

        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-white/40">Product</p>
          <ul class="mt-4 space-y-2 text-sm text-white/70">
            <li><a href="/#features" class="hover:text-white">Features</a></li>
            <li><a href="/#pricing" class="hover:text-white">Pricing</a></li>
            <li><a href="/#how-it-works" class="hover:text-white">How it works</a></li>
            <li><a href="/#faq" class="hover:text-white">FAQ</a></li>
          </ul>
        </div>

        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-white/40">Company</p>
          <ul class="mt-4 space-y-2 text-sm text-white/70">
            <li v-for="page in companyPages" :key="page.slug">
              <router-link :to="`/pages/${page.slug}`" class="hover:text-white">
                {{ page.nav_label || page.title }}
              </router-link>
            </li>
            <li><a href="/#contact" class="hover:text-white">Contact</a></li>
          </ul>
        </div>

        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-white/40">Legal</p>
          <ul class="mt-4 space-y-2 text-sm text-white/70">
            <li v-for="page in legalPages" :key="page.slug">
              <router-link :to="`/pages/${page.slug}`" class="hover:text-white">
                {{ page.nav_label || page.title }}
              </router-link>
            </li>
          </ul>
          <p v-if="settings.contact_email" class="mt-6 text-sm text-white/60">
            <a :href="`mailto:${settings.contact_email}`" class="hover:text-accent">{{ settings.contact_email }}</a>
          </p>
        </div>
      </div>
      <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-5 text-xs text-white/40 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
          <p>{{ settings.footer_text || `© ${year} ${brandName}. All rights reserved.` }}</p>
          <p>Built for HR & finance teams</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import ThemeToggle from '@/components/ThemeToggle.vue';
import AppLogo from '@/components/AppLogo.vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  pages: { type: Array, default: () => [] },
});

const authStore = useAuthStore();
const isAuthenticated = computed(() => authStore.isAuthenticated);
const brandName = computed(() => props.settings.brand_name || 'Payroll Digital');
const year = new Date().getFullYear();

const legalSlugs = ['privacy', 'terms', 'cookies', 'refund', 'security'];
const legalPages = computed(() =>
  (props.pages || []).filter((p) => legalSlugs.includes(p.slug)).sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
);
const companyPages = computed(() =>
  (props.pages || []).filter((p) => !legalSlugs.includes(p.slug)).sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
);
const hasSocial = computed(() =>
  !!(props.settings.social_linkedin || props.settings.social_twitter || props.settings.social_facebook)
);
</script>

<style scoped>
.nav-link {
  @apply rounded-lg px-3 py-2 text-sm font-medium text-white/70 transition hover:bg-white/10 hover:text-white;
}
.social-link {
  @apply flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-xs font-bold text-white/80 transition hover:bg-accent hover:text-white;
}
</style>
