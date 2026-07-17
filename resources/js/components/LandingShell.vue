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
          <router-link to="/contact" class="nav-link">Contact</router-link>
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
          <div v-if="hasApps" class="hidden items-center gap-1.5 sm:flex">
            <a
              v-if="settings.app_store_url"
              :href="settings.app_store_url"
              target="_blank"
              rel="noopener"
              class="app-icon-btn"
              aria-label="Download on the App Store"
              title="Download on the App Store"
            >
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M16.365 1.43c0 1.14-.417 2.2-1.25 3.02-.836.82-1.94 1.29-3.02 1.2-.13-1.1.41-2.24 1.19-3.02.84-.85 2.06-1.42 3.08-1.2zM20.5 17.02c-.55 1.27-.81 1.83-1.52 2.95-.99 1.57-2.39 3.52-4.12 3.53-1.54.01-1.94-1-4.03-.99-2.09.01-2.53 1.01-4.07.99-1.73-.01-3.06-1.78-4.05-3.34C.36 15.68-.14 10.9 1.9 8.35c1.02-1.29 2.63-2.11 4.17-2.11 1.56 0 2.54 1 3.83 1 1.25 0 2.01-1.01 3.82-1.01 1.36 0 2.8.74 3.83 2.02-3.37 1.84-2.82 6.64.95 8.77z" />
              </svg>
            </a>
            <a
              v-if="settings.play_store_url"
              :href="settings.play_store_url"
              target="_blank"
              rel="noopener"
              class="app-icon-btn"
              aria-label="Get it on Google Play"
              title="Get it on Google Play"
            >
              <svg class="h-5 w-5" viewBox="0 0 512 512" aria-hidden="true">
                <path fill="#00d7fe" d="M63 33 322 292l-73 73L48 164a34 34 0 0 1-11-25V49c0-7 4-13 10-16z" />
                <path fill="#00f076" d="M63 33c4-2 9-2 14 1l280 161-35 35L63 33z" />
                <path fill="#fee000" d="M357 195l70 40c14 8 14 34 0 42l-70 40-38-81 38-81z" />
                <path fill="#fe3d44" d="M322 292l35 35L91 481c-9 5-19 4-25-1L322 292z" />
              </svg>
            </a>
          </div>
          <ThemeToggle variant="header" />
          <router-link
            to="/contact?intent=demo"
            class="inline-flex items-center rounded-xl bg-accent px-3.5 py-2 text-sm font-semibold text-white shadow-card transition hover:bg-accent-dark sm:px-4"
          >
            Book a Demo
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

          <div v-if="hasApps" class="mt-6">
            <p class="text-xs font-bold uppercase tracking-wider text-white/40">Get the app</p>
            <div class="mt-3 flex flex-wrap gap-2.5">
              <a
                v-if="settings.app_store_url"
                :href="settings.app_store_url"
                target="_blank"
                rel="noopener"
                class="store-badge"
                aria-label="Download on the App Store"
              >
                <svg class="h-6 w-6 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M16.365 1.43c0 1.14-.417 2.2-1.25 3.02-.836.82-1.94 1.29-3.02 1.2-.13-1.1.41-2.24 1.19-3.02.84-.85 2.06-1.42 3.08-1.2zM20.5 17.02c-.55 1.27-.81 1.83-1.52 2.95-.99 1.57-2.39 3.52-4.12 3.53-1.54.01-1.94-1-4.03-.99-2.09.01-2.53 1.01-4.07.99-1.73-.01-3.06-1.78-4.05-3.34C.36 15.68-.14 10.9 1.9 8.35c1.02-1.29 2.63-2.11 4.17-2.11 1.56 0 2.54 1 3.83 1 1.25 0 2.01-1.01 3.82-1.01 1.36 0 2.8.74 3.83 2.02-3.37 1.84-2.82 6.64.95 8.77z" />
                </svg>
                <span class="flex flex-col leading-tight">
                  <span class="text-[9px] uppercase tracking-wide text-white/60">Download on the</span>
                  <span class="text-sm font-semibold">App Store</span>
                </span>
              </a>
              <a
                v-if="settings.play_store_url"
                :href="settings.play_store_url"
                target="_blank"
                rel="noopener"
                class="store-badge"
                aria-label="Get it on Google Play"
              >
                <svg class="h-6 w-6 shrink-0" viewBox="0 0 512 512" aria-hidden="true">
                  <path fill="#00d7fe" d="M63 33 322 292l-73 73L48 164a34 34 0 0 1-11-25V49c0-7 4-13 10-16z" />
                  <path fill="#00f076" d="M63 33c4-2 9-2 14 1l280 161-35 35L63 33z" />
                  <path fill="#fee000" d="M357 195l70 40c14 8 14 34 0 42l-70 40-38-81 38-81z" />
                  <path fill="#fe3d44" d="M322 292l35 35L91 481c-9 5-19 4-25-1L322 292z" />
                </svg>
                <span class="flex flex-col leading-tight">
                  <span class="text-[9px] uppercase tracking-wide text-white/60">Get it on</span>
                  <span class="text-sm font-semibold">Google Play</span>
                </span>
              </a>
            </div>
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
            <li><router-link to="/contact" class="hover:text-white">Contact</router-link></li>
            <li><router-link to="/contact?intent=demo" class="hover:text-white">Book a Demo</router-link></li>
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
import ThemeToggle from '@/components/ThemeToggle.vue';
import AppLogo from '@/components/AppLogo.vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  pages: { type: Array, default: () => [] },
});

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
const hasApps = computed(() =>
  !!(props.settings.app_store_url || props.settings.play_store_url)
);
</script>

<style scoped>
.nav-link {
  @apply rounded-lg px-3 py-2 text-sm font-medium text-white/70 transition hover:bg-white/10 hover:text-white;
}
.social-link {
  @apply flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-xs font-bold text-white/80 transition hover:bg-accent hover:text-white;
}
.app-icon-btn {
  @apply flex h-9 w-9 items-center justify-center rounded-xl border border-white/15 bg-white/5 text-white/80 transition hover:border-white/30 hover:bg-white/10 hover:text-white;
}
.store-badge {
  @apply inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/5 px-3 py-2 text-white transition hover:border-white/30 hover:bg-white/10;
}
</style>
