<template>
  <div>
    <div v-if="loading" class="flex min-h-screen items-center justify-center bg-brand">
      <div class="flex flex-col items-center gap-4">
        <div class="h-10 w-10 animate-spin rounded-full border-2 border-gold/30 border-t-gold" />
        <p class="text-sm text-white/60">Loading…</p>
      </div>
    </div>

    <LandingShell v-else :settings="settings" :pages="pages">
      <!-- Hero -->
      <section class="relative overflow-hidden bg-brand text-white">
        <div class="pointer-events-none absolute inset-0">
          <div class="absolute -left-32 -top-40 h-[28rem] w-[28rem] rounded-full bg-accent/25 blur-3xl" />
          <div class="absolute -right-20 bottom-0 h-80 w-80 rounded-full bg-gold/15 blur-3xl" />
          <div class="absolute inset-0 opacity-[0.05]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 28px 28px;" />
        </div>

        <div class="relative mx-auto grid max-w-6xl gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8 lg:py-28">
          <div>
            <p v-if="settings.brand_tagline" class="mb-4 inline-flex items-center rounded-full border border-gold/30 bg-gold/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-gold">
              {{ settings.brand_tagline }}
            </p>
            <h1 class="text-4xl font-bold leading-[1.1] tracking-tight sm:text-5xl lg:text-[3.25rem]">
              {{ settings.hero_title || 'Modern HR & Payroll' }}
            </h1>
            <p class="mt-5 max-w-xl text-base leading-relaxed text-white/70 sm:text-lg">
              {{ settings.hero_subtitle }}
            </p>
            <div class="mt-9 flex flex-wrap items-center gap-3">
              <a :href="primaryCtaHref" class="inline-flex items-center rounded-xl bg-accent px-6 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-accent-dark">
                {{ settings.hero_cta_text || 'Get Started' }}
              </a>
              <a
                v-if="settings.hero_secondary_cta_text"
                :href="settings.hero_secondary_cta_link || '#pricing'"
                class="inline-flex items-center rounded-xl border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/10"
              >
                {{ settings.hero_secondary_cta_text }}
              </a>
            </div>
            <div class="mt-10 flex flex-wrap gap-6 text-sm text-white/50">
              <span class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-gold" /> Role-based access</span>
              <span class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-gold" /> Multi-level approvals</span>
              <span class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-gold" /> Employee self-service</span>
            </div>
          </div>

          <div class="relative hidden lg:block">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-soft backdrop-blur">
              <div class="mb-5 flex items-center justify-between">
                <p class="text-sm font-semibold text-white/90">Workforce overview</p>
                <span class="rounded-full bg-accent/20 px-2.5 py-0.5 text-xs font-medium text-accent-muted">Live</span>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div v-for="stat in stats.slice(0, 4)" :key="stat.id" class="rounded-xl bg-brand-soft/80 p-4 ring-1 ring-white/10">
                  <p class="text-2xl font-bold text-gold">{{ stat.value }}</p>
                  <p class="mt-1 text-xs text-white/55">{{ stat.label }}</p>
                </div>
              </div>
              <div class="mt-5 rounded-xl bg-white/5 p-4 ring-1 ring-white/10">
                <div class="mb-3 flex items-center justify-between text-xs text-white/50">
                  <span>Payroll readiness</span>
                  <span class="text-gold">92%</span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-white/10">
                  <div class="h-full w-[92%] rounded-full bg-accent-gradient" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Stats strip (mobile / fallback) -->
      <section v-if="stats.length" class="relative z-10 -mt-8 lg:hidden">
        <div class="mx-auto grid max-w-6xl grid-cols-2 gap-3 px-4 sm:grid-cols-4 sm:px-6">
          <div v-for="stat in stats" :key="'m-' + stat.id" class="rounded-xl border border-surface-border bg-surface-card p-4 shadow-card">
            <p class="text-xl font-bold text-ink">{{ stat.value }}</p>
            <p class="mt-1 text-xs font-medium text-ink-muted">{{ stat.label }}</p>
          </div>
        </div>
      </section>

      <!-- Features -->
      <section id="features" class="scroll-mt-24 py-20 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-accent">Capabilities</p>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-ink sm:text-4xl">
              {{ settings.features_title || 'Everything you need' }}
            </h2>
            <p class="mt-3 text-base text-ink-muted">{{ settings.features_subtitle }}</p>
          </div>
          <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <article
              v-for="feature in features"
              :key="feature.id"
              class="group rounded-2xl border border-surface-border bg-surface-card p-6 shadow-card transition hover:-translate-y-0.5 hover:shadow-soft"
            >
              <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-accent-soft text-accent transition group-hover:bg-accent group-hover:text-white">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" :d="iconPath(feature.icon)" />
                </svg>
              </div>
              <h3 class="text-base font-bold text-ink">{{ feature.title }}</h3>
              <p class="mt-2 text-sm leading-relaxed text-ink-muted">{{ feature.description }}</p>
            </article>
          </div>
        </div>
      </section>

      <!-- How it works -->
      <section id="how-it-works" class="scroll-mt-24 border-y border-surface-border bg-surface-muted/60 py-20 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-accent">Onboarding</p>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-ink sm:text-4xl">
              {{ settings.how_it_works_title || 'How it works' }}
            </h2>
            <p class="mt-3 text-base text-ink-muted">{{ settings.how_it_works_subtitle }}</p>
          </div>
          <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            <div v-for="(step, index) in steps" :key="step.id" class="relative rounded-2xl border border-surface-border bg-surface-card p-6 shadow-card">
              <span class="mb-4 inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand text-xs font-bold text-gold">
                {{ String(index + 1).padStart(2, '0') }}
              </span>
              <h3 class="text-base font-bold text-ink">{{ step.title }}</h3>
              <p class="mt-2 text-sm leading-relaxed text-ink-muted">{{ step.description }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- About / Security band -->
      <section id="about" class="scroll-mt-24 py-20 sm:py-24">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
          <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-accent">About</p>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-ink sm:text-4xl">
              {{ settings.about_title || 'People operations, simplified' }}
            </h2>
            <p class="mt-4 whitespace-pre-line text-base leading-relaxed text-ink-soft">
              {{ settings.about_body }}
            </p>
            <router-link to="/pages/about" class="mt-6 inline-flex text-sm font-semibold text-accent hover:text-accent-dark">
              Learn more about us →
            </router-link>
          </div>
          <div class="rounded-2xl border border-surface-border bg-brand p-8 text-white shadow-soft">
            <p class="text-sm font-semibold uppercase tracking-wider text-gold">
              {{ settings.security_title || 'Security' }}
            </p>
            <p class="mt-4 text-base leading-relaxed text-white/75 whitespace-pre-line">
              {{ settings.security_body }}
            </p>
            <router-link to="/pages/security" class="mt-6 inline-flex text-sm font-semibold text-gold hover:text-white">
              Read security overview →
            </router-link>
          </div>
        </div>
      </section>

      <!-- Pricing -->
      <section id="pricing" class="scroll-mt-24 border-y border-surface-border bg-surface-muted/60 py-20 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-accent">Pricing</p>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-ink sm:text-4xl">
              {{ settings.pricing_title || 'Simple, transparent pricing' }}
            </h2>
            <p class="mt-3 text-base text-ink-muted">{{ settings.pricing_subtitle }}</p>
          </div>

          <div class="mt-12 grid gap-6 lg:grid-cols-3">
            <article
              v-for="plan in plans"
              :key="plan.id"
              class="relative flex flex-col rounded-2xl border p-7 shadow-card transition"
              :class="plan.is_featured
                ? 'border-accent bg-surface-card ring-2 ring-accent/30 scale-[1.02]'
                : 'border-surface-border bg-surface-card'"
            >
              <span
                v-if="plan.badge || plan.is_featured"
                class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-accent px-3 py-1 text-xs font-bold text-white"
              >
                {{ plan.badge || 'Popular' }}
              </span>
              <h3 class="text-xl font-bold text-ink">{{ plan.name }}</h3>
              <p class="mt-2 text-sm text-ink-muted">{{ plan.description }}</p>
              <div class="mt-6 flex items-baseline gap-1">
                <span class="text-4xl font-bold tracking-tight text-ink">{{ plan.price || 'Custom' }}</span>
                <span v-if="plan.price_period" class="text-sm text-ink-muted">{{ plan.price_period }}</span>
              </div>
              <ul class="mt-6 flex-1 space-y-3">
                <li v-for="(item, i) in (plan.features || [])" :key="i" class="flex gap-2 text-sm text-ink-soft">
                  <svg class="mt-0.5 h-4 w-4 shrink-0 text-accent" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  {{ item }}
                </li>
              </ul>
              <a
                :href="plan.cta_link || '/login'"
                class="mt-8 inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold transition"
                :class="plan.is_featured
                  ? 'bg-accent text-white hover:bg-accent-dark'
                  : 'border border-surface-border bg-surface-muted text-ink hover:bg-surface-card'"
              >
                {{ plan.cta_text || 'Get started' }}
              </a>
            </article>
          </div>
        </div>
      </section>

      <!-- Testimonials -->
      <section v-if="testimonials.length" class="py-20 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-accent">Customers</p>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-ink sm:text-4xl">
              {{ settings.testimonials_title || 'What teams say' }}
            </h2>
          </div>
          <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            <blockquote
              v-for="item in testimonials"
              :key="item.id"
              class="flex flex-col rounded-2xl border border-surface-border bg-surface-card p-6 shadow-card"
            >
              <p class="flex-1 text-sm leading-relaxed text-ink-soft">“{{ item.quote }}”</p>
              <footer class="mt-5 flex items-center gap-3 border-t border-surface-border pt-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-accent text-xs font-bold text-white">
                  {{ initials(item.name) }}
                </div>
                <div>
                  <p class="text-sm font-semibold text-ink">{{ item.name }}</p>
                  <p class="text-xs text-ink-muted">
                    {{ [item.role, item.company].filter(Boolean).join(' · ') }}
                  </p>
                </div>
              </footer>
            </blockquote>
          </div>
        </div>
      </section>

      <!-- FAQ -->
      <section id="faq" class="scroll-mt-24 border-y border-surface-border bg-surface-muted/60 py-20 sm:py-24">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-accent">FAQ</p>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-ink sm:text-4xl">
              {{ settings.faq_title || 'Frequently asked questions' }}
            </h2>
            <p class="mt-3 text-base text-ink-muted">{{ settings.faq_subtitle }}</p>
          </div>
          <div class="mt-10 space-y-3">
            <details
              v-for="faq in faqs"
              :key="faq.id"
              class="group rounded-2xl border border-surface-border bg-surface-card shadow-card open:shadow-soft"
            >
              <summary class="cursor-pointer list-none px-5 py-4 font-semibold text-ink marker:content-none flex items-center justify-between gap-3">
                <span>{{ faq.question }}</span>
                <svg class="h-5 w-5 shrink-0 text-ink-muted transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </summary>
              <div class="border-t border-surface-border px-5 py-4 text-sm leading-relaxed text-ink-soft whitespace-pre-line">
                {{ faq.answer }}
              </div>
            </details>
          </div>
        </div>
      </section>

      <!-- Contact -->
      <section id="contact" class="scroll-mt-24 py-20 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="overflow-hidden rounded-3xl border border-surface-border bg-surface-card shadow-soft lg:grid lg:grid-cols-2">
            <div class="bg-brand p-8 text-white sm:p-10">
              <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">
                {{ settings.contact_title || 'Talk to our team' }}
              </h2>
              <p class="mt-3 text-white/70 whitespace-pre-line">{{ settings.contact_body }}</p>
              <ul class="mt-8 space-y-4 text-sm text-white/80">
                <li v-if="settings.contact_email" class="flex gap-3">
                  <span class="text-gold">Email</span>
                  <a :href="`mailto:${settings.contact_email}`" class="hover:text-white">{{ settings.contact_email }}</a>
                </li>
                <li v-if="settings.contact_phone" class="flex gap-3">
                  <span class="text-gold">Phone</span>
                  <span>{{ settings.contact_phone }}</span>
                </li>
                <li v-if="settings.contact_address" class="flex gap-3">
                  <span class="text-gold">Office</span>
                  <span>{{ settings.contact_address }}</span>
                </li>
              </ul>
            </div>
            <div class="flex flex-col justify-center p-8 sm:p-10">
              <h3 class="text-xl font-bold text-ink">{{ settings.cta_title || 'Ready to get started?' }}</h3>
              <p class="mt-2 text-sm text-ink-muted">{{ settings.cta_body }}</p>
              <div class="mt-6 flex flex-wrap gap-3">
                <a
                  :href="ctaButtonHref"
                  class="inline-flex items-center rounded-xl bg-accent px-5 py-3 text-sm font-semibold text-white hover:bg-accent-dark"
                >
                  {{ settings.cta_button_text || 'Sign In' }}
                </a>
                <router-link
                  to="/pages/terms"
                  class="inline-flex items-center rounded-xl border border-surface-border px-5 py-3 text-sm font-semibold text-ink hover:bg-surface-muted"
                >
                  Terms of Service
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </section>
    </LandingShell>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import LandingShell from '@/components/LandingShell.vue';

const authStore = useAuthStore();
const isAuthenticated = computed(() => authStore.isAuthenticated);

const loading = ref(true);
const settings = ref({});
const features = ref([]);
const stats = ref([]);
const testimonials = ref([]);
const plans = ref([]);
const faqs = ref([]);
const steps = ref([]);
const pages = ref([]);

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
  helpdesk: 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z',
  travel: 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
};

function iconPath(key) {
  return ICON_PATHS[key] || ICON_PATHS.payroll;
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
    plans.value = data.plans || [];
    faqs.value = data.faqs || [];
    steps.value = data.steps || [];
    pages.value = data.pages || data.footer_pages || [];
  } catch (err) {
    console.error(err);
    settings.value = {
      brand_name: 'Payroll Digital',
      hero_title: 'Modern HR & Payroll',
      hero_subtitle: 'Something went wrong loading this page. You can still sign in.',
      hero_cta_text: 'Sign In',
      hero_cta_link: '/login',
    };
  } finally {
    loading.value = false;
  }
}

onMounted(loadLanding);
</script>
