<template>
  <div class="min-h-screen bg-surface text-ink">
    <!-- Loading -->
    <div v-if="loading" class="min-h-screen flex items-center justify-center bg-brand">
      <div class="flex flex-col items-center gap-4">
        <div class="h-10 w-10 rounded-full border-2 border-gold/30 border-t-gold animate-spin" />
        <p class="text-sm text-white/60">Loading…</p>
      </div>
    </div>

    <template v-else>
      <!-- Sticky nav -->
      <header class="sticky top-0 z-40 border-b border-white/10 bg-brand/95 backdrop-blur-md">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
          <router-link to="/" class="text-lg font-bold tracking-tight text-gold sm:text-xl">
            {{ brandName }}
          </router-link>
          <nav class="flex items-center gap-3 sm:gap-4">
            <ThemeToggle variant="header" />
            <a
              href="#features"
              class="hidden text-sm font-medium text-white/70 transition hover:text-white sm:inline"
            >
              Features
            </a>
            <router-link
              v-if="isAuthenticated"
              to="/dashboard"
              class="inline-flex items-center rounded-xl bg-accent px-4 py-2 text-sm font-semibold text-white shadow-card transition hover:bg-accent-dark"
            >
              Go to Dashboard
            </router-link>
            <router-link
              v-else
              to="/login"
              class="inline-flex items-center rounded-xl bg-accent px-4 py-2 text-sm font-semibold text-white shadow-card transition hover:bg-accent-dark"
            >
              Sign In
            </router-link>
          </nav>
        </div>
      </header>

      <!-- Full-bleed hero -->
      <section class="relative overflow-hidden bg-brand text-white">
        <div class="pointer-events-none absolute inset-0">
          <div class="absolute -left-32 -top-40 h-[28rem] w-[28rem] rounded-full bg-accent/20 blur-3xl" />
          <div class="absolute -right-20 bottom-0 h-80 w-80 rounded-full bg-gold/10 blur-3xl" />
          <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 28px 28px;" />
        </div>

        <div class="relative mx-auto flex min-h-[78vh] max-w-6xl flex-col justify-center px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
          <p v-if="settings.brand_tagline" class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-gold">
            {{ settings.brand_tagline }}
          </p>
          <h1 class="max-w-3xl text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
            {{ settings.hero_title || 'Modern HR & Payroll' }}
          </h1>
          <p class="mt-5 max-w-2xl text-base leading-relaxed text-white/70 sm:text-lg">
            {{ settings.hero_subtitle || 'Run payroll, attendance, leaves, and employee records from one platform.' }}
          </p>
          <div class="mt-10 flex flex-wrap items-center gap-3">
            <a
              :href="primaryCtaHref"
              class="inline-flex items-center rounded-xl bg-accent px-6 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-accent-dark"
            >
              {{ settings.hero_cta_text || 'Sign In' }}
            </a>
            <a
              v-if="settings.hero_secondary_cta_text"
              :href="settings.hero_secondary_cta_link || '#features'"
              class="inline-flex items-center rounded-xl border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/10"
            >
              {{ settings.hero_secondary_cta_text }}
            </a>
          </div>
        </div>
      </section>

      <!-- Stats -->
      <section v-if="stats.length" class="relative z-10 -mt-10 pb-4">
        <div class="mx-auto grid max-w-6xl grid-cols-2 gap-3 px-4 sm:gap-4 sm:px-6 md:grid-cols-4 lg:px-8">
          <div
            v-for="stat in stats"
            :key="stat.id"
            class="rounded-xl border border-surface-border bg-surface-card p-5 shadow-card"
          >
            <p class="text-2xl font-bold text-ink sm:text-3xl">{{ stat.value }}</p>
            <p class="mt-1 text-sm font-medium text-ink-muted">{{ stat.label }}</p>
          </div>
        </div>
      </section>

      <!-- Features -->
      <section id="features" class="scroll-mt-20 py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="max-w-2xl">
            <h2 class="text-3xl font-bold tracking-tight text-ink sm:text-4xl">
              {{ settings.features_title || 'Features' }}
            </h2>
            <p class="mt-3 text-base text-ink-muted">
              {{ settings.features_subtitle || 'Powerful modules that work together out of the box.' }}
            </p>
          </div>

          <div v-if="features.length" class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <article
              v-for="feature in features"
              :key="feature.id"
              class="rounded-xl border border-surface-border bg-surface-card p-6 shadow-card transition hover:shadow-soft"
            >
              <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-brand text-gold">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" :d="iconPath(feature.icon)" />
                </svg>
              </div>
              <h3 class="text-lg font-bold text-ink">{{ feature.title }}</h3>
              <p class="mt-2 text-sm leading-relaxed text-ink-muted">
                {{ feature.description || '—' }}
              </p>
            </article>
          </div>
          <p v-else class="mt-10 text-sm text-ink-muted">Features will appear here once published.</p>
        </div>
      </section>

      <!-- About -->
      <section class="border-y border-surface-border bg-surface-card py-16 sm:py-20">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8">
          <div>
            <h2 class="text-3xl font-bold tracking-tight text-ink sm:text-4xl">
              {{ settings.about_title || 'About us' }}
            </h2>
            <p class="mt-5 text-base leading-relaxed text-ink-soft whitespace-pre-line">
              {{ settings.about_body || 'Our HRMS helps teams automate payroll, track attendance, manage leave, and keep every employee record in sync.' }}
            </p>
          </div>
          <div class="relative overflow-hidden rounded-xl bg-brand p-8 text-white shadow-soft sm:p-10">
            <div class="pointer-events-none absolute -right-8 -top-8 h-40 w-40 rounded-full bg-accent/30 blur-2xl" />
            <p class="relative text-sm font-semibold uppercase tracking-[0.18em] text-gold">Why teams choose us</p>
            <ul class="relative mt-6 space-y-4 text-sm text-white/80">
              <li class="flex gap-3">
                <span class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-accent" />
                Unified payroll, attendance, and leave workflows
              </li>
              <li class="flex gap-3">
                <span class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-accent" />
                Clear approvals for managers and HR
              </li>
              <li class="flex gap-3">
                <span class="mt-1 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-accent" />
                Employee self-service without spreadsheet chaos
              </li>
            </ul>
          </div>
        </div>
      </section>

      <!-- Testimonials -->
      <section class="py-16 sm:py-20">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <h2 class="max-w-2xl text-3xl font-bold tracking-tight text-ink sm:text-4xl">
            {{ settings.testimonials_title || 'What teams say' }}
          </h2>

          <div v-if="testimonials.length" class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <blockquote
              v-for="item in testimonials"
              :key="item.id"
              class="flex flex-col rounded-xl border border-surface-border bg-surface-card p-6 shadow-card"
            >
              <p class="flex-1 text-sm leading-relaxed text-ink-soft">“{{ item.quote }}”</p>
              <footer class="mt-6 flex items-center gap-3 border-t border-surface-border pt-4">
                <div
                  v-if="item.avatar_url"
                  class="h-10 w-10 overflow-hidden rounded-full bg-surface"
                >
                  <img :src="item.avatar_url" :alt="item.name" class="h-full w-full object-cover" />
                </div>
                <div
                  v-else
                  class="flex h-10 w-10 items-center justify-center rounded-full bg-brand text-sm font-bold text-gold"
                >
                  {{ initials(item.name) }}
                </div>
                <div>
                  <p class="text-sm font-semibold text-ink">{{ item.name }}</p>
                  <p class="text-xs text-ink-muted">
                    {{ [item.role, item.company].filter(Boolean).join(' · ') || 'Customer' }}
                  </p>
                </div>
              </footer>
            </blockquote>
          </div>
          <p v-else class="mt-10 text-sm text-ink-muted">Testimonials coming soon.</p>
        </div>
      </section>

      <!-- CTA band -->
      <section class="bg-brand py-16 text-white sm:py-20">
        <div class="mx-auto flex max-w-6xl flex-col items-start justify-between gap-8 px-4 sm:px-6 lg:flex-row lg:items-center lg:px-8">
          <div class="max-w-xl">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
              {{ settings.cta_title || 'Ready to simplify HR?' }}
            </h2>
            <p class="mt-3 text-base text-white/70">
              {{ settings.cta_body || 'Sign in to manage payroll, attendance, and your workforce in one place.' }}
            </p>
          </div>
          <a
            :href="ctaButtonHref"
            class="inline-flex flex-shrink-0 items-center rounded-xl bg-accent px-6 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-accent-dark"
          >
            {{ settings.cta_button_text || 'Go to Login' }}
          </a>
        </div>
      </section>

      <!-- Footer -->
      <footer class="border-t border-surface-border bg-surface-card py-10">
        <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 sm:flex-row sm:items-start sm:justify-between sm:px-6 lg:px-8">
          <div>
            <p class="text-base font-bold text-gold">{{ brandName }}</p>
            <p class="mt-2 max-w-sm text-sm text-ink-muted">
              {{ settings.footer_text || `© ${new Date().getFullYear()} ${brandName}. All rights reserved.` }}
            </p>
          </div>
          <div class="space-y-1 text-sm text-ink-muted">
            <p v-if="settings.contact_email">
              <a :href="`mailto:${settings.contact_email}`" class="hover:text-accent">{{ settings.contact_email }}</a>
            </p>
            <p v-if="settings.contact_phone">{{ settings.contact_phone }}</p>
            <p v-if="settings.contact_address">{{ settings.contact_address }}</p>
          </div>
        </div>
      </footer>
    </template>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import ThemeToggle from '@/components/ThemeToggle.vue';

const authStore = useAuthStore();
const isAuthenticated = computed(() => authStore.isAuthenticated);

const loading = ref(true);
const settings = ref({});
const features = ref([]);
const stats = ref([]);
const testimonials = ref([]);

const brandName = computed(() => settings.value.brand_name || 'Payroll Digital');

const primaryCtaHref = computed(() => {
  if (isAuthenticated.value) return '/dashboard';
  return settings.value.hero_cta_link || '/login';
});

const ctaButtonHref = computed(() => {
  if (isAuthenticated.value) return '/dashboard';
  return settings.value.cta_button_link || '/login';
});

const ICON_PATHS = {
  payroll: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
  attendance: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
  leaves: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
  employees: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
  shifts: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
  recruitment: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
  modules: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
  workflows: 'M13 10V3L4 14h7v7l9-11h-7z',
  users: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
  approvals: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
};

const DEFAULT_ICON = 'M13 10V3L4 14h7v7l9-11h-7z';

function iconPath(key) {
  if (!key) return DEFAULT_ICON;
  return ICON_PATHS[key] || DEFAULT_ICON;
}

function initials(name) {
  return String(name || '?')
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((p) => p[0]?.toUpperCase() || '')
    .join('') || '?';
}

async function loadLanding() {
  loading.value = true;
  try {
    const { data } = await axios.get('/landing');
    settings.value = data.settings || {};
    features.value = data.features || [];
    stats.value = data.stats || [];
    testimonials.value = data.testimonials || [];
  } catch (err) {
    console.error('Failed to load landing page', err);
    settings.value = {
      brand_name: 'Payroll Digital',
      hero_title: 'Modern HR & Payroll',
      hero_subtitle: 'Something went wrong loading this page. You can still sign in.',
      hero_cta_text: 'Sign In',
      hero_cta_link: '/login',
    };
    features.value = [];
    stats.value = [];
    testimonials.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(loadLanding);
</script>
