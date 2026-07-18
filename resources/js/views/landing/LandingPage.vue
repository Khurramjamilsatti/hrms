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
            <router-link
              to="/contact?intent=demo"
              class="inline-flex items-center rounded-xl bg-accent px-6 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-accent-dark"
            >
              Book a Demo
            </router-link>
            <a
              href="#pricing"
              class="inline-flex items-center rounded-xl border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/10"
            >
              View Pricing
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

      <!-- Trusted by -->
      <section v-if="blocks.logos?.length" class="border-b border-surface-border bg-surface-card py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <p class="text-center text-xs font-bold uppercase tracking-[0.2em] text-ink-muted">
            {{ settings.logos_title || 'Trusted by growing teams' }}
          </p>
          <div class="mt-6 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
            <span
              v-for="logo in blocks.logos"
              :key="logo.id"
              class="rounded-xl border border-surface-border bg-surface-muted/60 px-4 py-2.5 text-sm font-semibold text-ink-soft"
            >
              {{ logo.title }}
            </span>
          </div>
        </div>
      </section>

      <!-- Stats strip (desktop) -->
      <section v-if="stats.length" class="hidden border-b border-surface-border bg-surface-muted/40 py-10 lg:block">
        <div class="mx-auto grid max-w-6xl grid-cols-3 gap-6 px-4 sm:grid-cols-6 sm:px-6 lg:px-8">
          <div v-for="stat in stats" :key="'d-' + stat.id" class="text-center">
            <p class="text-2xl font-bold text-brand">{{ stat.value }}</p>
            <p class="mt-1 text-xs font-medium text-ink-muted">{{ stat.label }}</p>
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

      <!-- Why choose us -->
      <section v-if="blocks.highlights?.length" id="why-us" class="scroll-mt-24 border-b border-surface-border bg-brand py-20 text-white sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-gold">Why us</p>
            <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">
              {{ settings.highlights_title || 'Why teams choose Payroll Digital' }}
            </h2>
            <p class="mt-3 text-base text-white/65">{{ settings.highlights_subtitle }}</p>
          </div>
          <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <article
              v-for="item in blocks.highlights"
              :key="item.id"
              class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur transition hover:bg-white/10"
            >
              <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-accent/20 text-accent-muted">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" :d="iconPath(item.icon)" />
                </svg>
              </div>
              <h3 class="text-base font-bold">{{ item.title }}</h3>
              <p class="mt-2 text-sm leading-relaxed text-white/65">{{ item.description }}</p>
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

      <!-- Industries -->
      <section v-if="blocks.industries?.length" id="industries" class="scroll-mt-24 py-20 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-accent">Industries</p>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-ink sm:text-4xl">
              {{ settings.industries_title || 'Built for every industry' }}
            </h2>
            <p class="mt-3 text-base text-ink-muted">{{ settings.industries_subtitle }}</p>
          </div>
          <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <article
              v-for="item in blocks.industries"
              :key="item.id"
              class="rounded-2xl border border-surface-border border-l-4 border-l-accent bg-surface-card p-6 shadow-card"
            >
              <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-accent-soft text-accent">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" :d="iconPath(item.icon)" />
                </svg>
              </div>
              <h3 class="text-base font-bold text-ink">{{ item.title }}</h3>
              <p class="mt-2 text-sm leading-relaxed text-ink-muted">{{ item.description }}</p>
            </article>
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

      <!-- Integrations -->
      <section v-if="blocks.integrations?.length" id="integrations" class="scroll-mt-24 border-y border-surface-border bg-surface-muted/60 py-20 sm:py-24">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-accent">Integrations</p>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-ink sm:text-4xl">
              {{ settings.integrations_title || 'Connects with your stack' }}
            </h2>
            <p class="mt-3 text-base text-ink-muted">{{ settings.integrations_subtitle }}</p>
          </div>
          <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <article
              v-for="item in blocks.integrations"
              :key="item.id"
              class="flex gap-4 rounded-2xl border border-surface-border bg-surface-card p-5 shadow-card"
            >
              <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand text-gold">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" :d="iconPath(item.icon)" />
                </svg>
              </div>
              <div>
                <h3 class="text-sm font-bold text-ink">{{ item.title }}</h3>
                <p class="mt-1 text-sm leading-relaxed text-ink-muted">{{ item.description }}</p>
              </div>
            </article>
          </div>
        </div>
      </section>

      <!-- Pricing -->
      <section id="pricing" class="scroll-mt-24 relative overflow-hidden py-20 sm:py-24">
        <div class="pointer-events-none absolute inset-0 bg-brand">
          <div class="absolute -left-24 top-0 h-72 w-72 rounded-full bg-accent/20 blur-3xl" />
          <div class="absolute -right-16 bottom-0 h-80 w-80 rounded-full bg-gold/15 blur-3xl" />
          <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px;" />
        </div>

        <div class="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-gold">Pricing</p>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">
              {{ settings.pricing_title || 'Simple, transparent pricing' }}
            </h2>
            <p class="mt-3 text-base text-white/65">{{ settings.pricing_subtitle }}</p>
            <p v-if="pricingLocale.currency" class="mt-2 text-xs font-medium text-white/45">
              Prices shown in {{ pricingLocale.currency }} based on your region.
            </p>
          </div>

          <div class="mt-14 grid gap-5 md:grid-cols-2 xl:grid-cols-4 xl:items-stretch">
            <article
              v-for="plan in plans"
              :key="plan.id"
              class="group relative flex flex-col overflow-hidden rounded-2xl transition duration-300 hover:-translate-y-1"
              :class="plan.is_featured
                ? 'bg-accent shadow-[0_20px_50px_-12px_rgba(255,91,96,0.55)] ring-2 ring-gold/40 xl:-mt-3 xl:mb-[-0.75rem]'
                : 'bg-white/95 shadow-[0_16px_40px_-16px_rgba(0,0,0,0.35)] ring-1 ring-white/20'"
            >
              <div
                class="h-1.5 w-full"
                :class="plan.is_featured ? 'bg-gold' : 'bg-gradient-to-r from-brand via-accent to-gold'"
              />

              <div class="flex flex-1 flex-col p-6 sm:p-7">
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <p
                      class="text-[11px] font-bold uppercase tracking-[0.16em]"
                      :class="plan.is_featured ? 'text-white/80' : 'text-accent'"
                    >
                      {{ plan.name }}
                    </p>
                    <span
                      v-if="plan.badge || plan.is_featured"
                      class="mt-2 inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide"
                      :class="plan.is_featured
                        ? 'bg-gold text-brand'
                        : 'bg-accent-soft text-accent-dark'"
                    >
                      {{ plan.badge || 'Popular' }}
                    </span>
                  </div>
                  <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-sm font-bold"
                    :class="plan.is_featured
                      ? 'bg-white/20 text-white'
                      : 'bg-brand text-gold'"
                  >
                    {{ planInitial(plan.name) }}
                  </span>
                </div>

                <p
                  class="mt-3 text-sm leading-relaxed"
                  :class="plan.is_featured ? 'text-white/85' : 'text-ink-muted'"
                >
                  {{ plan.description }}
                </p>

                <div class="mt-6 border-y py-5"
                  :class="plan.is_featured ? 'border-white/20' : 'border-surface-border'"
                >
                  <div class="flex flex-wrap items-end gap-x-1.5 gap-y-1">
                    <span
                      class="text-3xl font-bold tracking-tight sm:text-[2rem]"
                      :class="plan.is_featured ? 'text-white' : 'text-brand'"
                    >
                      {{ formatPlanPrice(plan) }}
                    </span>
                    <span
                      v-if="plan.price_period && !isCustomPrice(plan)"
                      class="mb-1 text-sm font-medium"
                      :class="plan.is_featured ? 'text-white/70' : 'text-ink-muted'"
                    >
                      {{ plan.price_period }}
                    </span>
                  </div>
                  <p
                    v-if="employeeLimit(plan)"
                    class="mt-2 text-xs font-semibold"
                    :class="plan.is_featured ? 'text-gold' : 'text-brand-light'"
                  >
                    {{ employeeLimit(plan) }}
                  </p>
                </div>

                <ul class="mt-5 flex-1 space-y-2.5">
                  <li
                    v-for="(item, i) in (plan.features || [])"
                    :key="i"
                    class="flex gap-2.5 text-sm leading-snug"
                    :class="plan.is_featured ? 'text-white/90' : 'text-ink-soft'"
                  >
                    <span
                      class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full"
                      :class="plan.is_featured ? 'bg-white/20 text-gold' : 'bg-accent-soft text-accent'"
                    >
                      <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </span>
                    {{ item }}
                  </li>
                </ul>

                <a
                  :href="plan.cta_link || '/contact?intent=demo'"
                  class="mt-7 inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-semibold transition"
                  :class="plan.is_featured
                    ? 'bg-brand text-white shadow-soft hover:bg-brand-soft'
                    : 'bg-accent text-white hover:bg-accent-dark'"
                >
                  {{ plan.cta_text || 'Book a Demo' }}
                </a>
              </div>
            </article>
          </div>
        </div>
      </section>

      <!-- Mobile apps -->
      <section
        v-if="settings.mobile_title || settings.app_store_url || settings.play_store_url"
        id="mobile"
        class="scroll-mt-24 py-20 sm:py-24"
      >
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
          <div class="overflow-hidden rounded-3xl border border-surface-border bg-surface-card shadow-soft lg:grid lg:grid-cols-2">
            <div class="relative bg-brand p-8 text-white sm:p-12">
              <div class="pointer-events-none absolute -right-10 -top-10 h-48 w-48 rounded-full bg-accent/20 blur-3xl" />
              <p class="text-sm font-semibold uppercase tracking-wider text-gold">Mobile</p>
              <h2 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">
                {{ settings.mobile_title || 'HR in your pocket' }}
              </h2>
              <p class="mt-2 text-sm font-medium text-white/70">{{ settings.mobile_subtitle }}</p>
              <p class="mt-4 text-sm leading-relaxed text-white/65 whitespace-pre-line">
                {{ settings.mobile_body }}
              </p>
              <ul class="mt-8 space-y-3 text-sm text-white/80">
                <li class="flex gap-2"><span class="text-gold">✓</span> Attendance check-in & leave requests</li>
                <li class="flex gap-2"><span class="text-gold">✓</span> Payslips & approval notifications</li>
                <li class="flex gap-2"><span class="text-gold">✓</span> Works on iOS, Android & mobile web</li>
              </ul>
              <div v-if="settings.app_store_url || settings.play_store_url" class="mt-8 flex flex-wrap gap-3">
                <a
                  v-if="settings.app_store_url"
                  :href="settings.app_store_url"
                  target="_blank"
                  rel="noopener"
                  class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-semibold transition hover:bg-white/15"
                >
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M16.365 1.43c0 1.14-.417 2.2-1.25 3.02-.836.82-1.94 1.29-3.02 1.2-.13-1.1.41-2.24 1.19-3.02.84-.85 2.06-1.42 3.08-1.2zM20.5 17.02c-.55 1.27-.81 1.83-1.52 2.95-.99 1.57-2.39 3.52-4.12 3.53-1.54.01-1.94-1-4.03-.99-2.09.01-2.53 1.01-4.07.99-1.73-.01-3.06-1.78-4.05-3.34C.36 15.68-.14 10.9 1.9 8.35c1.02-1.29 2.63-2.11 4.17-2.11 1.56 0 2.54 1 3.83 1 1.25 0 2.01-1.01 3.82-1.01 1.36 0 2.8.74 3.83 2.02-3.37 1.84-2.82 6.64.95 8.77z"/></svg>
                  App Store
                </a>
                <a
                  v-if="settings.play_store_url"
                  :href="settings.play_store_url"
                  target="_blank"
                  rel="noopener"
                  class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2.5 text-sm font-semibold transition hover:bg-white/15"
                >
                  <svg class="h-5 w-5" viewBox="0 0 512 512"><path fill="#00d7fe" d="M63 33 322 292l-73 73L48 164a34 34 0 0 1-11-25V49c0-7 4-13 10-16z"/><path fill="#00f076" d="M63 33c4-2 9-2 14 1l280 161-35 35L63 33z"/><path fill="#fee000" d="M357 195l70 40c14 8 14 34 0 42l-70 40-38-81 38-81z"/><path fill="#fe3d44" d="M322 292l35 35L91 481c-9 5-19 4-25-1L322 292z"/></svg>
                  Google Play
                </a>
              </div>
            </div>
            <div class="flex items-center justify-center bg-surface-muted/50 p-8 sm:p-12">
              <div class="relative w-full max-w-[260px]">
                <div class="rounded-[2rem] border-4 border-brand bg-brand p-3 shadow-soft">
                  <div class="overflow-hidden rounded-[1.5rem] bg-surface-card">
                    <div class="flex items-center justify-center bg-brand px-4 py-2">
                      <img
                        :src="'/images/payroll-digital-logo.png'"
                        alt="Payroll Digital"
                        class="h-12 w-12 object-contain"
                      >
                    </div>
                    <div class="space-y-3 p-4">
                      <div class="rounded-lg bg-surface-muted p-3">
                        <p class="text-[10px] font-semibold uppercase text-ink-muted">Today</p>
                        <p class="mt-1 text-sm font-bold text-ink">Checked in · 9:02 AM</p>
                      </div>
                      <div class="rounded-lg bg-surface-muted p-3">
                        <p class="text-[10px] font-semibold uppercase text-ink-muted">Leave</p>
                        <p class="mt-1 text-sm font-bold text-ink">3 days remaining</p>
                      </div>
                      <div class="rounded-lg bg-accent-soft p-3">
                        <p class="text-[10px] font-semibold uppercase text-accent">Payslip</p>
                        <p class="mt-1 text-sm font-bold text-ink">June ready to view</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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
                <router-link
                  to="/contact?intent=demo"
                  class="inline-flex items-center rounded-xl bg-accent px-5 py-3 text-sm font-semibold text-white hover:bg-accent-dark"
                >
                  Book a Demo
                </router-link>
                <router-link
                  to="/contact"
                  class="inline-flex items-center rounded-xl border border-surface-border px-5 py-3 text-sm font-semibold text-ink hover:bg-surface-muted"
                >
                  Contact Us
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
import { onMounted, ref } from 'vue';
import axios from 'axios';
import LandingShell from '@/components/LandingShell.vue';

const loading = ref(true);
const settings = ref({});
const features = ref([]);
const stats = ref([]);
const testimonials = ref([]);
const plans = ref([]);
const pricingLocale = ref({});
const faqs = ref([]);
const steps = ref([]);
const pages = ref([]);
const blocks = ref({ logos: [], highlights: [], industries: [], integrations: [] });

const ICON_PATHS = {
  payroll: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
  attendance: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
  leaves: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
  employees: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
  shifts: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
  recruitment: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
  helpdesk: 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z',
  travel: 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
  speed: 'M13 10V3L4 14h7v7l9-11h-7z',
  shield: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
  workflow: 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z',
  mobile: 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
  report: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
  support: 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z',
  retail: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
  manufacturing: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
  tech: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
  healthcare: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
  logistics: 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0',
  finance: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
  excel: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
  email: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
  calendar: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
  api: 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
  sso: 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
  storage: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
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

function formatPlanPrice(plan) {
  if (plan.localized_price_amount == null || !plan.localized_currency) {
    return plan.price || 'Custom';
  }

  return new Intl.NumberFormat(undefined, {
    style: 'currency',
    currency: plan.localized_currency,
    maximumFractionDigits: 0,
  }).format(plan.localized_price_amount);
}

function isCustomPrice(plan) {
  const price = String(formatPlanPrice(plan) || '').toLowerCase();
  return !price || price.includes('custom');
}

function planInitial(name) {
  return String(name || '?').trim().charAt(0).toUpperCase() || '?';
}

function employeeLimit(plan) {
  const features = plan.features || [];
  const match = features.find((f) => /employee/i.test(String(f)));
  return match || null;
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
    pricingLocale.value = data.pricing_locale || {};
    faqs.value = data.faqs || [];
    steps.value = data.steps || [];
    pages.value = data.pages || data.footer_pages || [];
    blocks.value = data.blocks || { logos: [], highlights: [], industries: [], integrations: [] };
  } catch (err) {
    console.error(err);
    settings.value = {
      brand_name: 'Payroll Digital',
      hero_title: 'Modern HR & Payroll',
      hero_subtitle: 'Something went wrong loading this page. You can still book a demo.',
    };
  } finally {
    loading.value = false;
  }
}

onMounted(loadLanding);
</script>
