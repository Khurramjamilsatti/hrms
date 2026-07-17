<template>
  <div class="p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Landing Content</h1>
        <p class="mt-1 text-sm text-gray-500">Edit the public website — separate from the HRMS application.</p>
      </div>
      <button
        v-if="canEdit && activeTab !== 'settings'"
        type="button"
        @click="openCreateModal"
        class="inline-flex items-center rounded-lg bg-accent px-5 py-2.5 font-medium text-white shadow transition-colors hover:bg-accent-dark"
      >
        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        {{ addButtonLabel }}
      </button>
    </div>

    <!-- Flash messages -->
    <div
      v-if="successMessage"
      class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800"
    >
      {{ successMessage }}
    </div>
    <div
      v-if="errorMessage"
      class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"
    >
      {{ errorMessage }}
    </div>

    <!-- Tabs -->
    <div class="mb-6 rounded-xl border border-surface-border bg-white shadow-card">
      <div class="border-b border-surface-border px-4">
        <nav class="-mb-px flex gap-1 overflow-x-auto">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            type="button"
            @click="activeTab = tab.id"
            class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors"
            :class="activeTab === tab.id
              ? 'border-brand text-brand'
              : 'border-transparent text-gray-500 hover:text-gray-700'"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex items-center justify-center py-20">
      <div class="h-10 w-10 animate-spin rounded-full border-b-2 border-brand" />
    </div>

    <!-- Settings -->
    <div v-else-if="activeTab === 'settings'" class="rounded-xl border border-surface-border bg-white p-6 shadow-card">
      <form class="space-y-6" @submit.prevent="saveSettings">
        <fieldset :disabled="!canEdit" class="space-y-6">
          <!-- Brand & Hero -->
          <div class="rounded-xl border border-surface-border p-5">
            <h3 class="mb-4 text-xs font-bold uppercase tracking-wide text-gray-500">Brand &amp; Hero</h3>
            <div class="grid gap-5 md:grid-cols-2">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Brand name</label>
                <input v-model="settingsForm.brand_name" type="text" class="field" required />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Brand tagline</label>
                <input v-model="settingsForm.brand_tagline" type="text" class="field" />
              </div>
              <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold text-gray-700">Hero title</label>
                <input v-model="settingsForm.hero_title" type="text" class="field" required />
              </div>
              <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold text-gray-700">Hero subtitle</label>
                <textarea v-model="settingsForm.hero_subtitle" rows="3" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Primary CTA text</label>
                <input v-model="settingsForm.hero_cta_text" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Primary CTA link</label>
                <input v-model="settingsForm.hero_cta_link" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Secondary CTA text</label>
                <input v-model="settingsForm.hero_secondary_cta_text" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Secondary CTA link</label>
                <input v-model="settingsForm.hero_secondary_cta_link" type="text" class="field" />
              </div>
            </div>
          </div>

          <!-- About & Security -->
          <div class="rounded-xl border border-surface-border p-5">
            <h3 class="mb-4 text-xs font-bold uppercase tracking-wide text-gray-500">About &amp; Security</h3>
            <div class="grid gap-5 md:grid-cols-2">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">About title</label>
                <input v-model="settingsForm.about_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Security title</label>
                <input v-model="settingsForm.security_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">About body</label>
                <textarea v-model="settingsForm.about_body" rows="4" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Security body</label>
                <textarea v-model="settingsForm.security_body" rows="4" class="field" />
              </div>
            </div>
          </div>

          <!-- Section Titles -->
          <div class="rounded-xl border border-surface-border p-5">
            <h3 class="mb-4 text-xs font-bold uppercase tracking-wide text-gray-500">Section Titles</h3>
            <div class="grid gap-5 md:grid-cols-2">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Features title</label>
                <input v-model="settingsForm.features_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Features subtitle</label>
                <input v-model="settingsForm.features_subtitle" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Stats title</label>
                <input v-model="settingsForm.stats_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Testimonials title</label>
                <input v-model="settingsForm.testimonials_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">How it works title</label>
                <input v-model="settingsForm.how_it_works_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">How it works subtitle</label>
                <input v-model="settingsForm.how_it_works_subtitle" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Pricing title</label>
                <input v-model="settingsForm.pricing_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Pricing subtitle</label>
                <input v-model="settingsForm.pricing_subtitle" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">FAQ title</label>
                <input v-model="settingsForm.faq_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">FAQ subtitle</label>
                <input v-model="settingsForm.faq_subtitle" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Trusted-by title</label>
                <input v-model="settingsForm.logos_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Highlights title</label>
                <input v-model="settingsForm.highlights_title" type="text" class="field" />
              </div>
              <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold text-gray-700">Highlights subtitle</label>
                <input v-model="settingsForm.highlights_subtitle" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Industries title</label>
                <input v-model="settingsForm.industries_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Industries subtitle</label>
                <input v-model="settingsForm.industries_subtitle" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Integrations title</label>
                <input v-model="settingsForm.integrations_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Integrations subtitle</label>
                <input v-model="settingsForm.integrations_subtitle" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Mobile section title</label>
                <input v-model="settingsForm.mobile_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Mobile section subtitle</label>
                <input v-model="settingsForm.mobile_subtitle" type="text" class="field" />
              </div>
              <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold text-gray-700">Mobile section body</label>
                <textarea v-model="settingsForm.mobile_body" rows="3" class="field" />
              </div>
            </div>
          </div>

          <!-- Contact & Social -->
          <div class="rounded-xl border border-surface-border p-5">
            <h3 class="mb-4 text-xs font-bold uppercase tracking-wide text-gray-500">Contact &amp; Social</h3>
            <div class="grid gap-5 md:grid-cols-2">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Contact title</label>
                <input v-model="settingsForm.contact_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Contact email</label>
                <input v-model="settingsForm.contact_email" type="email" class="field" />
              </div>
              <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold text-gray-700">Contact body</label>
                <textarea v-model="settingsForm.contact_body" rows="3" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Contact phone</label>
                <input v-model="settingsForm.contact_phone" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Contact address</label>
                <input v-model="settingsForm.contact_address" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">LinkedIn URL</label>
                <input v-model="settingsForm.social_linkedin" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Twitter / X URL</label>
                <input v-model="settingsForm.social_twitter" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Facebook URL</label>
                <input v-model="settingsForm.social_facebook" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">App Store URL (iOS)</label>
                <input v-model="settingsForm.app_store_url" type="text" class="field" placeholder="https://apps.apple.com/app/…" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Google Play URL (Android)</label>
                <input v-model="settingsForm.play_store_url" type="text" class="field" placeholder="https://play.google.com/store/apps/details?id=…" />
              </div>
              <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold text-gray-700">Footer text</label>
                <textarea v-model="settingsForm.footer_text" rows="2" class="field" />
              </div>
            </div>
          </div>

          <!-- CTA -->
          <div class="rounded-xl border border-surface-border p-5">
            <h3 class="mb-4 text-xs font-bold uppercase tracking-wide text-gray-500">CTA</h3>
            <div class="grid gap-5 md:grid-cols-2">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">CTA title</label>
                <input v-model="settingsForm.cta_title" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">CTA body</label>
                <input v-model="settingsForm.cta_body" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">CTA button text</label>
                <input v-model="settingsForm.cta_button_text" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">CTA button link</label>
                <input v-model="settingsForm.cta_button_link" type="text" class="field" />
              </div>
            </div>
          </div>

          <!-- Publish toggle -->
          <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl bg-surface px-4 py-3">
            <div>
              <p class="text-sm font-semibold text-gray-900">Published</p>
              <p class="text-xs text-gray-500">When off, visitors see a coming-soon message.</p>
            </div>
            <label class="relative inline-flex cursor-pointer items-center">
              <input v-model="settingsForm.is_published" type="checkbox" class="peer sr-only" :disabled="!canEdit" />
              <div class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all peer-checked:bg-accent peer-checked:after:translate-x-full peer-focus:ring-2 peer-focus:ring-accent/30" />
            </label>
          </div>
        </fieldset>

        <div v-if="canEdit" class="flex justify-end border-t border-surface-border pt-4">
          <button
            type="submit"
            :disabled="saving"
            class="rounded-lg bg-accent px-5 py-2.5 text-sm font-medium text-white hover:bg-accent-dark disabled:opacity-50"
          >
            {{ saving ? 'Saving…' : 'Save settings' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Features -->
    <div v-else-if="activeTab === 'features'">
      <div v-if="features.length === 0" class="rounded-xl border border-surface-border bg-white p-12 text-center shadow-card">
        <h3 class="text-lg font-semibold text-gray-900">No features yet</h3>
        <p class="mt-1 text-gray-500">Add feature cards for the landing page.</p>
      </div>
      <div v-else class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="item in features"
          :key="item.id"
          class="overflow-hidden rounded-xl border border-surface-border bg-white shadow-card"
        >
          <div class="p-5">
            <div class="mb-3 flex items-start justify-between gap-2">
              <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">{{ item.icon || 'icon' }}</p>
                <h3 class="text-lg font-bold text-gray-900">{{ item.title }}</h3>
              </div>
              <span
                class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700'"
              >
                {{ item.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <p class="line-clamp-3 min-h-[3.75rem] text-sm text-gray-600">{{ item.description || 'No description.' }}</p>
            <p class="mt-3 text-xs text-gray-400">Sort: {{ item.sort_order ?? 0 }}</p>
          </div>
          <div v-if="canEdit" class="flex justify-end gap-2 border-t border-surface-border bg-gray-50 px-5 py-3">
            <button type="button" class="icon-btn" title="Edit" @click="openEditFeature(item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
            </button>
            <button type="button" class="icon-btn-danger" title="Delete" @click="confirmDelete('feature', item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div v-else-if="activeTab === 'stats'">
      <div v-if="stats.length === 0" class="rounded-xl border border-surface-border bg-white p-12 text-center shadow-card">
        <h3 class="text-lg font-semibold text-gray-900">No stats yet</h3>
        <p class="mt-1 text-gray-500">Add headline metrics for the landing page.</p>
      </div>
      <div v-else class="grid gap-5 md:grid-cols-2 lg:grid-cols-4">
        <div
          v-for="item in stats"
          :key="item.id"
          class="overflow-hidden rounded-xl border border-surface-border bg-white shadow-card"
        >
          <div class="p-5">
            <div class="mb-2 flex items-start justify-between">
              <p class="text-2xl font-bold text-brand">{{ item.value }}</p>
              <span
                class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700'"
              >
                {{ item.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <p class="font-medium text-gray-900">{{ item.label }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ item.icon || '—' }} · Sort {{ item.sort_order ?? 0 }}</p>
          </div>
          <div v-if="canEdit" class="flex justify-end gap-2 border-t border-surface-border bg-gray-50 px-5 py-3">
            <button type="button" class="icon-btn" title="Edit" @click="openEditStat(item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
            </button>
            <button type="button" class="icon-btn-danger" title="Delete" @click="confirmDelete('stat', item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Blocks -->
    <div v-else-if="activeTab === 'blocks'">
      <div class="mb-4 flex flex-wrap gap-2">
        <button
          v-for="opt in blockTypeOptions"
          :key="opt.id"
          type="button"
          class="rounded-lg px-3 py-1.5 text-sm font-semibold transition"
          :class="blockTypeFilter === opt.id ? 'bg-accent text-white' : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50'"
          @click="blockTypeFilter = opt.id"
        >
          {{ opt.label }}
        </button>
      </div>
      <div v-if="filteredBlocks.length === 0" class="rounded-xl border border-surface-border bg-white p-12 text-center shadow-card">
        <h3 class="text-lg font-semibold text-gray-900">No blocks yet</h3>
        <p class="mt-1 text-gray-500">Add {{ blockTypeFilter }} items for the landing page.</p>
      </div>
      <div v-else class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="item in filteredBlocks"
          :key="item.id"
          class="overflow-hidden rounded-xl border border-surface-border bg-white shadow-card"
        >
          <div class="p-5">
            <div class="mb-2 flex items-start justify-between gap-2">
              <h3 class="font-bold text-gray-900">{{ item.title }}</h3>
              <span
                class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700'"
              >
                {{ item.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <p v-if="item.description" class="line-clamp-3 text-sm text-gray-600">{{ item.description }}</p>
            <p class="mt-2 text-xs text-gray-400">{{ item.type }} · {{ item.icon || '—' }} · Sort {{ item.sort_order ?? 0 }}</p>
          </div>
          <div v-if="canEdit" class="flex justify-end gap-2 border-t border-surface-border bg-gray-50 px-5 py-3">
            <button type="button" class="icon-btn" title="Edit" @click="openEditBlock(item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
            </button>
            <button type="button" class="icon-btn-danger" title="Delete" @click="confirmDelete('block', item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Testimonials -->
    <div v-else-if="activeTab === 'testimonials'">
      <div v-if="testimonials.length === 0" class="rounded-xl border border-surface-border bg-white p-12 text-center shadow-card">
        <h3 class="text-lg font-semibold text-gray-900">No testimonials yet</h3>
        <p class="mt-1 text-gray-500">Add customer quotes for the landing page.</p>
      </div>
      <div v-else class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="item in testimonials"
          :key="item.id"
          class="overflow-hidden rounded-xl border border-surface-border bg-white shadow-card"
        >
          <div class="p-5">
            <div class="mb-3 flex items-start justify-between gap-2">
              <div>
                <h3 class="font-bold text-gray-900">{{ item.name }}</h3>
                <p class="text-xs text-gray-500">{{ [item.role, item.company].filter(Boolean).join(' · ') || '—' }}</p>
              </div>
              <span
                class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700'"
              >
                {{ item.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <p class="line-clamp-4 text-sm text-gray-600">“{{ item.quote }}”</p>
          </div>
          <div v-if="canEdit" class="flex justify-end gap-2 border-t border-surface-border bg-gray-50 px-5 py-3">
            <button type="button" class="icon-btn" title="Edit" @click="openEditTestimonial(item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
            </button>
            <button type="button" class="icon-btn-danger" title="Delete" @click="confirmDelete('testimonial', item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Plans -->
    <div v-else-if="activeTab === 'plans'">
      <div v-if="plans.length === 0" class="rounded-xl border border-surface-border bg-white p-12 text-center shadow-card">
        <h3 class="text-lg font-semibold text-gray-900">No plans yet</h3>
        <p class="mt-1 text-gray-500">Add pricing plans for the landing page.</p>
      </div>
      <div v-else class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="item in plans"
          :key="item.id"
          class="overflow-hidden rounded-xl border shadow-card"
          :class="item.is_featured ? 'border-accent' : 'border-surface-border bg-white'"
        >
          <div class="p-5">
            <div class="mb-3 flex items-start justify-between gap-2">
              <div>
                <div class="flex items-center gap-2">
                  <h3 class="text-lg font-bold text-gray-900">{{ item.name }}</h3>
                  <span v-if="item.badge" class="inline-flex rounded-full bg-accent/10 px-2 py-0.5 text-xs font-semibold text-accent-dark">{{ item.badge }}</span>
                </div>
                <p class="text-xs text-gray-500">/{{ item.slug }}</p>
              </div>
              <span
                class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700'"
              >
                {{ item.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">
              {{ item.price || '—' }}
              <span v-if="item.price_period" class="text-sm font-medium text-gray-500">/{{ item.price_period }}</span>
            </p>
            <p v-if="item.is_featured" class="mt-1 text-xs font-semibold uppercase tracking-wide text-accent-dark">Featured plan</p>
            <p class="mt-2 line-clamp-2 text-sm text-gray-600">{{ item.description || 'No description.' }}</p>
            <ul v-if="(item.features || []).length" class="mt-3 space-y-1">
              <li v-for="(line, idx) in (item.features || []).slice(0, 4)" :key="idx" class="flex items-start gap-1.5 text-xs text-gray-600">
                <svg class="mt-0.5 h-3.5 w-3.5 flex-shrink-0 text-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                <span class="line-clamp-1">{{ line }}</span>
              </li>
            </ul>
            <p class="mt-3 text-xs text-gray-400">Sort: {{ item.sort_order ?? 0 }}</p>
          </div>
          <div v-if="canEdit" class="flex justify-end gap-2 border-t border-surface-border bg-gray-50 px-5 py-3">
            <button type="button" class="icon-btn" title="Edit" @click="openEditPlan(item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
            </button>
            <button type="button" class="icon-btn-danger" title="Delete" @click="confirmDelete('plan', item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- FAQs -->
    <div v-else-if="activeTab === 'faqs'">
      <div v-if="faqs.length === 0" class="rounded-xl border border-surface-border bg-white p-12 text-center shadow-card">
        <h3 class="text-lg font-semibold text-gray-900">No FAQs yet</h3>
        <p class="mt-1 text-gray-500">Add frequently asked questions for the landing page.</p>
      </div>
      <div v-else class="divide-y divide-surface-border rounded-xl border border-surface-border bg-white shadow-card">
        <div v-for="item in faqs" :key="item.id" class="flex items-start justify-between gap-4 p-5">
          <div class="min-w-0 flex-1">
            <div class="mb-1 flex items-center gap-2">
              <h3 class="font-bold text-gray-900">{{ item.question }}</h3>
              <span v-if="item.category" class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">{{ item.category }}</span>
              <span
                class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700'"
              >
                {{ item.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <p class="line-clamp-2 text-sm text-gray-600">{{ item.answer }}</p>
            <p class="mt-1 text-xs text-gray-400">Sort: {{ item.sort_order ?? 0 }}</p>
          </div>
          <div v-if="canEdit" class="flex flex-shrink-0 gap-2">
            <button type="button" class="icon-btn" title="Edit" @click="openEditFaq(item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
            </button>
            <button type="button" class="icon-btn-danger" title="Delete" @click="confirmDelete('faq', item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Steps -->
    <div v-else-if="activeTab === 'steps'">
      <div v-if="steps.length === 0" class="rounded-xl border border-surface-border bg-white p-12 text-center shadow-card">
        <h3 class="text-lg font-semibold text-gray-900">No steps yet</h3>
        <p class="mt-1 text-gray-500">Add "how it works" steps for the landing page.</p>
      </div>
      <div v-else class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="item in steps"
          :key="item.id"
          class="overflow-hidden rounded-xl border border-surface-border bg-white shadow-card"
        >
          <div class="p-5">
            <div class="mb-3 flex items-start justify-between gap-2">
              <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">{{ item.icon || 'icon' }}</p>
                <h3 class="text-lg font-bold text-gray-900">{{ item.title }}</h3>
              </div>
              <span
                class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700'"
              >
                {{ item.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <p class="line-clamp-3 min-h-[3.75rem] text-sm text-gray-600">{{ item.description || 'No description.' }}</p>
            <p class="mt-3 text-xs text-gray-400">Sort: {{ item.sort_order ?? 0 }}</p>
          </div>
          <div v-if="canEdit" class="flex justify-end gap-2 border-t border-surface-border bg-gray-50 px-5 py-3">
            <button type="button" class="icon-btn" title="Edit" @click="openEditStep(item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
            </button>
            <button type="button" class="icon-btn-danger" title="Delete" @click="confirmDelete('step', item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Pages -->
    <div v-else-if="activeTab === 'pages'">
      <div v-if="pages.length === 0" class="rounded-xl border border-surface-border bg-white p-12 text-center shadow-card">
        <h3 class="text-lg font-semibold text-gray-900">No pages yet</h3>
        <p class="mt-1 text-gray-500">Add standalone content pages (e.g. Privacy Policy, Terms).</p>
      </div>
      <div v-else class="divide-y divide-surface-border rounded-xl border border-surface-border bg-white shadow-card">
        <div v-for="item in pages" :key="item.id" class="flex items-start justify-between gap-4 p-5">
          <div class="min-w-0 flex-1">
            <div class="mb-1 flex flex-wrap items-center gap-2">
              <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-mono font-medium text-gray-600">/{{ item.slug }}</span>
              <h3 class="font-bold text-gray-900">{{ item.title }}</h3>
              <span
                class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                :class="item.is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
              >
                {{ item.is_published ? 'Published' : 'Draft' }}
              </span>
              <span v-if="item.show_in_footer" class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-800">
                In footer
              </span>
            </div>
            <p v-if="item.nav_label" class="text-xs text-gray-400">Nav label: {{ item.nav_label }}</p>
            <p class="line-clamp-2 text-sm text-gray-600">{{ item.excerpt || 'No excerpt.' }}</p>
            <p class="mt-1 text-xs text-gray-400">Sort: {{ item.sort_order ?? 0 }}</p>
          </div>
          <div v-if="canEdit" class="flex flex-shrink-0 gap-2">
            <button type="button" class="icon-btn" title="Edit" @click="openEditPage(item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
            </button>
            <button type="button" class="icon-btn-danger" title="Delete" @click="confirmDelete('page', item)">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create / Edit modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-surface-border px-6 py-4">
          <h3 class="text-lg font-bold text-gray-900">{{ modalTitle }}</h3>
          <button type="button" class="text-gray-400 hover:text-gray-600" @click="closeModal">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>

        <form class="space-y-4 px-6 py-5" @submit.prevent="saveModal">
          <template v-if="activeTab === 'features'">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Icon key</label>
              <input v-model="featureForm.icon" type="text" placeholder="payroll, attendance…" class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Title</label>
              <input v-model="featureForm.title" type="text" required class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Description</label>
              <textarea v-model="featureForm.description" rows="3" class="field" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Sort order</label>
                <input v-model.number="featureForm.sort_order" type="number" min="0" class="field" />
              </div>
              <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                  <input v-model="featureForm.is_active" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                  Active
                </label>
              </div>
            </div>
          </template>

          <template v-else-if="activeTab === 'stats'">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Label</label>
              <input v-model="statForm.label" type="text" required class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Value</label>
              <input v-model="statForm.value" type="text" required class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Icon key</label>
              <input v-model="statForm.icon" type="text" class="field" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Sort order</label>
                <input v-model.number="statForm.sort_order" type="number" min="0" class="field" />
              </div>
              <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                  <input v-model="statForm.is_active" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                  Active
                </label>
              </div>
            </div>
          </template>

          <template v-else-if="activeTab === 'blocks'">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Block type</label>
              <select v-model="blockForm.type" class="field" required>
                <option value="logo">Trusted logo</option>
                <option value="highlight">Why choose us</option>
                <option value="industry">Industry</option>
                <option value="integration">Integration</option>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Title</label>
              <input v-model="blockForm.title" type="text" required class="field" />
            </div>
            <div v-if="blockForm.type !== 'logo'">
              <label class="mb-1 block text-sm font-semibold text-gray-700">Icon key</label>
              <input v-model="blockForm.icon" type="text" placeholder="speed, retail, api…" class="field" />
            </div>
            <div v-if="blockForm.type !== 'logo'">
              <label class="mb-1 block text-sm font-semibold text-gray-700">Description</label>
              <textarea v-model="blockForm.description" rows="3" class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">URL (optional)</label>
              <input v-model="blockForm.url" type="text" class="field" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Sort order</label>
                <input v-model.number="blockForm.sort_order" type="number" min="0" class="field" />
              </div>
              <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                  <input v-model="blockForm.is_active" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                  Active
                </label>
              </div>
            </div>
          </template>

          <template v-else-if="activeTab === 'testimonials'">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Name</label>
              <input v-model="testimonialForm.name" type="text" required class="field" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Role</label>
                <input v-model="testimonialForm.role" type="text" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Company</label>
                <input v-model="testimonialForm.company" type="text" class="field" />
              </div>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Quote</label>
              <textarea v-model="testimonialForm.quote" rows="4" required class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Avatar URL</label>
              <input v-model="testimonialForm.avatar_url" type="text" class="field" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Sort order</label>
                <input v-model.number="testimonialForm.sort_order" type="number" min="0" class="field" />
              </div>
              <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                  <input v-model="testimonialForm.is_active" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                  Active
                </label>
              </div>
            </div>
          </template>

          <template v-else-if="activeTab === 'plans'">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Name</label>
                <input v-model="planForm.name" type="text" required class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Slug</label>
                <input v-model="planForm.slug" type="text" placeholder="auto-generated" class="field" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Price</label>
                <input v-model="planForm.price" type="text" placeholder="$49" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Price period</label>
                <input v-model="planForm.price_period" type="text" placeholder="month" class="field" />
              </div>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Badge</label>
              <input v-model="planForm.badge" type="text" placeholder="Most popular" class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Description</label>
              <textarea v-model="planForm.description" rows="2" class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Features (one per line)</label>
              <textarea v-model="planForm.features" rows="5" placeholder="Unlimited employees&#10;Payroll automation&#10;Priority support" class="field" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">CTA text</label>
                <input v-model="planForm.cta_text" type="text" placeholder="Get started" class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">CTA link</label>
                <input v-model="planForm.cta_link" type="text" class="field" />
              </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Sort order</label>
                <input v-model.number="planForm.sort_order" type="number" min="0" class="field" />
              </div>
              <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                  <input v-model="planForm.is_featured" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                  Featured
                </label>
              </div>
              <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                  <input v-model="planForm.is_active" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                  Active
                </label>
              </div>
            </div>
          </template>

          <template v-else-if="activeTab === 'faqs'">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Question</label>
              <input v-model="faqForm.question" type="text" required class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Answer</label>
              <textarea v-model="faqForm.answer" rows="4" required class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Category</label>
              <input v-model="faqForm.category" type="text" placeholder="Billing, Security…" class="field" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Sort order</label>
                <input v-model.number="faqForm.sort_order" type="number" min="0" class="field" />
              </div>
              <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                  <input v-model="faqForm.is_active" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                  Active
                </label>
              </div>
            </div>
          </template>

          <template v-else-if="activeTab === 'steps'">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Title</label>
              <input v-model="stepForm.title" type="text" required class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Description</label>
              <textarea v-model="stepForm.description" rows="3" class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Icon key</label>
              <input v-model="stepForm.icon" type="text" class="field" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Sort order</label>
                <input v-model.number="stepForm.sort_order" type="number" min="0" class="field" />
              </div>
              <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                  <input v-model="stepForm.is_active" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                  Active
                </label>
              </div>
            </div>
          </template>

          <template v-else-if="activeTab === 'pages'">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Title</label>
                <input v-model="pageForm.title" type="text" required class="field" />
              </div>
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Slug</label>
                <input v-model="pageForm.slug" type="text" placeholder="auto-generated" class="field" />
              </div>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Nav label</label>
              <input v-model="pageForm.nav_label" type="text" placeholder="Shown in footer navigation" class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Excerpt</label>
              <textarea v-model="pageForm.excerpt" rows="2" class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Content</label>
              <textarea v-model="pageForm.content" rows="10" required class="field font-mono text-sm" />
            </div>
            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="mb-1 block text-sm font-semibold text-gray-700">Sort order</label>
                <input v-model.number="pageForm.sort_order" type="number" min="0" class="field" />
              </div>
              <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                  <input v-model="pageForm.show_in_footer" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                  Show in footer
                </label>
              </div>
              <div class="flex items-end pb-2">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                  <input v-model="pageForm.is_published" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                  Published
                </label>
              </div>
            </div>
          </template>

          <div v-if="formError" class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600">{{ formError }}</div>

          <div class="flex justify-end gap-3 border-t border-surface-border pt-4">
            <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50" @click="closeModal">
              Cancel
            </button>
            <button type="submit" :disabled="saving" class="rounded-lg bg-accent px-5 py-2 text-sm font-medium text-white hover:bg-accent-dark disabled:opacity-50">
              {{ saving ? 'Saving…' : (editingId ? 'Update' : 'Create') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete confirm -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="w-full max-w-sm overflow-hidden rounded-xl bg-white shadow-xl">
        <div class="px-6 py-5 text-center">
          <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
          </div>
          <h3 class="mb-2 text-lg font-bold text-gray-900">Delete {{ deleteType }}?</h3>
          <p class="text-sm text-gray-600">
            Are you sure you want to delete
            <span class="font-semibold">{{ deleteLabel }}</span>?
            This cannot be undone.
          </p>
        </div>
        <div class="flex justify-end gap-3 border-t border-surface-border bg-gray-50 px-6 py-4">
          <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50" @click="showDeleteModal = false">
            Cancel
          </button>
          <button type="button" :disabled="deleting" class="rounded-lg bg-red-600 px-5 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50" @click="performDelete">
            {{ deleting ? 'Deleting…' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';

// CMS area has its own auth; any authenticated CMS user can edit content.
const canEdit = computed(() => true);

const tabs = [
  { id: 'settings', label: 'Settings' },
  { id: 'features', label: 'Features' },
  { id: 'stats', label: 'Stats' },
  { id: 'blocks', label: 'Blocks' },
  { id: 'testimonials', label: 'Testimonials' },
  { id: 'plans', label: 'Plans' },
  { id: 'faqs', label: 'FAQs' },
  { id: 'steps', label: 'Steps' },
  { id: 'pages', label: 'Pages' },
];

const activeTab = ref('settings');
const loading = ref(true);
const saving = ref(false);
const deleting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const formError = ref('');

const features = ref([]);
const stats = ref([]);
const blocks = ref([]);
const blockTypeFilter = ref('highlight');
const blockTypeOptions = [
  { id: 'logo', label: 'Trusted logos' },
  { id: 'highlight', label: 'Highlights' },
  { id: 'industry', label: 'Industries' },
  { id: 'integration', label: 'Integrations' },
];
const testimonials = ref([]);
const plans = ref([]);
const faqs = ref([]);
const steps = ref([]);
const pages = ref([]);

const emptySettings = () => ({
  brand_name: '',
  brand_tagline: '',
  hero_title: '',
  hero_subtitle: '',
  hero_cta_text: '',
  hero_cta_link: '',
  hero_secondary_cta_text: '',
  hero_secondary_cta_link: '',
  about_title: '',
  about_body: '',
  features_title: '',
  features_subtitle: '',
  stats_title: '',
  logos_title: '',
  highlights_title: '',
  highlights_subtitle: '',
  industries_title: '',
  industries_subtitle: '',
  integrations_title: '',
  integrations_subtitle: '',
  mobile_title: '',
  mobile_subtitle: '',
  mobile_body: '',
  testimonials_title: '',
  pricing_title: '',
  pricing_subtitle: '',
  faq_title: '',
  faq_subtitle: '',
  how_it_works_title: '',
  how_it_works_subtitle: '',
  security_title: '',
  security_body: '',
  contact_title: '',
  contact_body: '',
  cta_title: '',
  cta_body: '',
  cta_button_text: '',
  cta_button_link: '',
  contact_email: '',
  contact_phone: '',
  contact_address: '',
  social_linkedin: '',
  social_twitter: '',
  social_facebook: '',
  app_store_url: '',
  play_store_url: '',
  footer_text: '',
  is_published: true,
});

const settingsForm = ref(emptySettings());

const showModal = ref(false);
const editingId = ref(null);
const featureForm = ref({ icon: '', title: '', description: '', sort_order: 0, is_active: true });
const statForm = ref({ label: '', value: '', icon: '', sort_order: 0, is_active: true });
const blockForm = ref({ type: 'highlight', icon: '', title: '', description: '', url: '', sort_order: 0, is_active: true });
const testimonialForm = ref({
  name: '',
  role: '',
  company: '',
  quote: '',
  avatar_url: '',
  sort_order: 0,
  is_active: true,
});
const planForm = ref({
  name: '',
  slug: '',
  price: '',
  price_period: '',
  badge: '',
  description: '',
  features: '',
  cta_text: '',
  cta_link: '',
  is_featured: false,
  is_active: true,
  sort_order: 0,
});
const faqForm = ref({ question: '', answer: '', category: '', is_active: true, sort_order: 0 });
const stepForm = ref({ title: '', description: '', icon: '', is_active: true, sort_order: 0 });
const pageForm = ref({
  slug: '',
  title: '',
  nav_label: '',
  excerpt: '',
  content: '',
  show_in_footer: false,
  is_published: true,
  sort_order: 0,
});

const showDeleteModal = ref(false);
const deleteType = ref('');
const deleteTarget = ref(null);

const addButtonLabel = computed(() => {
  const labels = {
    features: 'Add Feature',
    stats: 'Add Stat',
    blocks: 'Add Block',
    testimonials: 'Add Testimonial',
    plans: 'Add Plan',
    faqs: 'Add FAQ',
    steps: 'Add Step',
    pages: 'Add Page',
  };
  return labels[activeTab.value] || 'Add';
});

const modalTitle = computed(() => {
  const action = editingId.value ? 'Edit' : 'Add';
  const nouns = {
    features: 'Feature',
    stats: 'Stat',
    blocks: 'Block',
    testimonials: 'Testimonial',
    plans: 'Plan',
    faqs: 'FAQ',
    steps: 'Step',
    pages: 'Page',
  };
  const noun = nouns[activeTab.value];
  return noun ? `${action} ${noun}` : action;
});

const deleteLabel = computed(() => {
  const t = deleteTarget.value;
  if (!t) return '';
  return t.title || t.question || t.label || t.name || `#${t.id}`;
});

const filteredBlocks = computed(() =>
  blocks.value.filter((b) => b.type === blockTypeFilter.value)
);

function slugify(value) {
  return String(value || '')
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/(^-+|-+$)/g, '');
}

let flashTimer = null;
function flash(success, error = '') {
  successMessage.value = success || '';
  errorMessage.value = error || '';
  if (flashTimer) clearTimeout(flashTimer);
  flashTimer = setTimeout(() => {
    successMessage.value = '';
    errorMessage.value = '';
  }, 4000);
}

function assignSettings(data) {
  const base = emptySettings();
  Object.keys(base).forEach((key) => {
    if (data && data[key] !== undefined && data[key] !== null) {
      base[key] = data[key];
    }
  });
  base.is_published = !!base.is_published;
  settingsForm.value = base;
}

async function loadCms() {
  loading.value = true;
  errorMessage.value = '';
  try {
    const { data } = await axios.get('/cms/landing');
    assignSettings(data.settings || {});
    features.value = data.features || [];
    stats.value = data.stats || [];
    blocks.value = data.blocks || [];
    testimonials.value = data.testimonials || [];
    plans.value = data.plans || [];
    faqs.value = data.faqs || [];
    steps.value = data.steps || [];
    pages.value = data.pages || [];
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Failed to load landing CMS data.';
  } finally {
    loading.value = false;
  }
}

async function saveSettings() {
  if (!canEdit.value) return;
  saving.value = true;
  formError.value = '';
  try {
    const { data } = await axios.put('/cms/landing/settings', settingsForm.value);
    assignSettings(data.settings || settingsForm.value);
    flash(data.message || 'Landing page settings updated.');
  } catch (err) {
    const msg = err.response?.data?.message || 'Failed to save settings.';
    flash('', msg);
  } finally {
    saving.value = false;
  }
}

function openCreateModal() {
  editingId.value = null;
  formError.value = '';
  if (activeTab.value === 'features') {
    featureForm.value = { icon: '', title: '', description: '', sort_order: features.value.length + 1, is_active: true };
  } else if (activeTab.value === 'stats') {
    statForm.value = { label: '', value: '', icon: '', sort_order: stats.value.length + 1, is_active: true };
  } else if (activeTab.value === 'blocks') {
    blockForm.value = {
      type: blockTypeFilter.value,
      icon: '',
      title: '',
      description: '',
      url: '',
      sort_order: filteredBlocks.value.length + 1,
      is_active: true,
    };
  } else if (activeTab.value === 'testimonials') {
    testimonialForm.value = {
      name: '',
      role: '',
      company: '',
      quote: '',
      avatar_url: '',
      sort_order: testimonials.value.length + 1,
      is_active: true,
    };
  } else if (activeTab.value === 'plans') {
    planForm.value = {
      name: '',
      slug: '',
      price: '',
      price_period: '',
      badge: '',
      description: '',
      features: '',
      cta_text: '',
      cta_link: '',
      is_featured: false,
      is_active: true,
      sort_order: plans.value.length + 1,
    };
  } else if (activeTab.value === 'faqs') {
    faqForm.value = { question: '', answer: '', category: '', is_active: true, sort_order: faqs.value.length + 1 };
  } else if (activeTab.value === 'steps') {
    stepForm.value = { title: '', description: '', icon: '', is_active: true, sort_order: steps.value.length + 1 };
  } else if (activeTab.value === 'pages') {
    pageForm.value = {
      slug: '',
      title: '',
      nav_label: '',
      excerpt: '',
      content: '',
      show_in_footer: false,
      is_published: true,
      sort_order: pages.value.length + 1,
    };
  }
  showModal.value = true;
}

function openEditFeature(item) {
  editingId.value = item.id;
  featureForm.value = {
    icon: item.icon || '',
    title: item.title || '',
    description: item.description || '',
    sort_order: item.sort_order ?? 0,
    is_active: !!item.is_active,
  };
  formError.value = '';
  showModal.value = true;
}

function openEditStat(item) {
  editingId.value = item.id;
  statForm.value = {
    label: item.label || '',
    value: item.value || '',
    icon: item.icon || '',
    sort_order: item.sort_order ?? 0,
    is_active: !!item.is_active,
  };
  formError.value = '';
  showModal.value = true;
}

function openEditBlock(item) {
  editingId.value = item.id;
  blockForm.value = {
    type: item.type || 'highlight',
    icon: item.icon || '',
    title: item.title || '',
    description: item.description || '',
    url: item.url || '',
    is_active: !!item.is_active,
    sort_order: item.sort_order ?? 0,
  };
  formError.value = '';
  showModal.value = true;
}

function openEditTestimonial(item) {
  editingId.value = item.id;
  testimonialForm.value = {
    name: item.name || '',
    role: item.role || '',
    company: item.company || '',
    quote: item.quote || '',
    avatar_url: item.avatar_url || '',
    sort_order: item.sort_order ?? 0,
    is_active: !!item.is_active,
  };
  formError.value = '';
  showModal.value = true;
}

function openEditPlan(item) {
  editingId.value = item.id;
  const featureLines = Array.isArray(item.features) ? item.features : [];
  planForm.value = {
    name: item.name || '',
    slug: item.slug || '',
    price: item.price || '',
    price_period: item.price_period || '',
    badge: item.badge || '',
    description: item.description || '',
    features: featureLines.join('\n'),
    cta_text: item.cta_text || '',
    cta_link: item.cta_link || '',
    is_featured: !!item.is_featured,
    is_active: !!item.is_active,
    sort_order: item.sort_order ?? 0,
  };
  formError.value = '';
  showModal.value = true;
}

function openEditFaq(item) {
  editingId.value = item.id;
  faqForm.value = {
    question: item.question || '',
    answer: item.answer || '',
    category: item.category || '',
    is_active: !!item.is_active,
    sort_order: item.sort_order ?? 0,
  };
  formError.value = '';
  showModal.value = true;
}

function openEditStep(item) {
  editingId.value = item.id;
  stepForm.value = {
    title: item.title || '',
    description: item.description || '',
    icon: item.icon || '',
    is_active: !!item.is_active,
    sort_order: item.sort_order ?? 0,
  };
  formError.value = '';
  showModal.value = true;
}

function openEditPage(item) {
  editingId.value = item.id;
  pageForm.value = {
    slug: item.slug || '',
    title: item.title || '',
    nav_label: item.nav_label || '',
    excerpt: item.excerpt || '',
    content: item.content || '',
    show_in_footer: !!item.show_in_footer,
    is_published: !!item.is_published,
    sort_order: item.sort_order ?? 0,
  };
  formError.value = '';
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editingId.value = null;
  formError.value = '';
}

async function saveModal() {
  if (!canEdit.value) return;
  saving.value = true;
  formError.value = '';
  try {
    if (activeTab.value === 'features') {
      if (editingId.value) {
        const { data } = await axios.put(`/cms/landing/features/${editingId.value}`, featureForm.value);
        const idx = features.value.findIndex((f) => f.id === editingId.value);
        if (idx !== -1) features.value[idx] = data.feature;
        flash(data.message || 'Feature updated.');
      } else {
        const { data } = await axios.post('/cms/landing/features', featureForm.value);
        features.value.push(data.feature);
        flash(data.message || 'Feature created.');
      }
    } else if (activeTab.value === 'stats') {
      if (editingId.value) {
        const { data } = await axios.put(`/cms/landing/stats/${editingId.value}`, statForm.value);
        const idx = stats.value.findIndex((s) => s.id === editingId.value);
        if (idx !== -1) stats.value[idx] = data.stat;
        flash(data.message || 'Stat updated.');
      } else {
        const { data } = await axios.post('/cms/landing/stats', statForm.value);
        stats.value.push(data.stat);
        flash(data.message || 'Stat created.');
      }
    } else if (activeTab.value === 'blocks') {
      if (editingId.value) {
        const { data } = await axios.put(`/cms/landing/blocks/${editingId.value}`, blockForm.value);
        const idx = blocks.value.findIndex((b) => b.id === editingId.value);
        if (idx !== -1) blocks.value[idx] = data.block;
        flash(data.message || 'Block updated.');
      } else {
        const { data } = await axios.post('/cms/landing/blocks', blockForm.value);
        blocks.value.push(data.block);
        flash(data.message || 'Block created.');
      }
    } else if (activeTab.value === 'testimonials') {
      if (editingId.value) {
        const { data } = await axios.put(`/cms/landing/testimonials/${editingId.value}`, testimonialForm.value);
        const idx = testimonials.value.findIndex((t) => t.id === editingId.value);
        if (idx !== -1) testimonials.value[idx] = data.testimonial;
        flash(data.message || 'Testimonial updated.');
      } else {
        const { data } = await axios.post('/cms/landing/testimonials', testimonialForm.value);
        testimonials.value.push(data.testimonial);
        flash(data.message || 'Testimonial created.');
      }
    } else if (activeTab.value === 'plans') {
      if (!editingId.value && !planForm.value.slug.trim()) {
        planForm.value.slug = slugify(planForm.value.name);
      }
      const payload = { ...planForm.value, slug: planForm.value.slug || slugify(planForm.value.name) };
      if (editingId.value) {
        const { data } = await axios.put(`/cms/landing/plans/${editingId.value}`, payload);
        const idx = plans.value.findIndex((p) => p.id === editingId.value);
        if (idx !== -1) plans.value[idx] = data.plan;
        flash(data.message || 'Plan updated.');
      } else {
        const { data } = await axios.post('/cms/landing/plans', payload);
        plans.value.push(data.plan);
        flash(data.message || 'Plan created.');
      }
    } else if (activeTab.value === 'faqs') {
      if (editingId.value) {
        const { data } = await axios.put(`/cms/landing/faqs/${editingId.value}`, faqForm.value);
        const idx = faqs.value.findIndex((f) => f.id === editingId.value);
        if (idx !== -1) faqs.value[idx] = data.faq;
        flash(data.message || 'FAQ updated.');
      } else {
        const { data } = await axios.post('/cms/landing/faqs', faqForm.value);
        faqs.value.push(data.faq);
        flash(data.message || 'FAQ created.');
      }
    } else if (activeTab.value === 'steps') {
      if (editingId.value) {
        const { data } = await axios.put(`/cms/landing/steps/${editingId.value}`, stepForm.value);
        const idx = steps.value.findIndex((s) => s.id === editingId.value);
        if (idx !== -1) steps.value[idx] = data.step;
        flash(data.message || 'Step updated.');
      } else {
        const { data } = await axios.post('/cms/landing/steps', stepForm.value);
        steps.value.push(data.step);
        flash(data.message || 'Step created.');
      }
    } else if (activeTab.value === 'pages') {
      if (!editingId.value && !pageForm.value.slug.trim()) {
        pageForm.value.slug = slugify(pageForm.value.title);
      }
      const payload = { ...pageForm.value, slug: pageForm.value.slug || slugify(pageForm.value.title) };
      if (editingId.value) {
        const { data } = await axios.put(`/cms/landing/pages/${editingId.value}`, payload);
        const idx = pages.value.findIndex((p) => p.id === editingId.value);
        if (idx !== -1) pages.value[idx] = data.page;
        flash(data.message || 'Page updated.');
      } else {
        const { data } = await axios.post('/cms/landing/pages', payload);
        pages.value.push(data.page);
        flash(data.message || 'Page created.');
      }
    }
    closeModal();
  } catch (err) {
    formError.value = err.response?.data?.message || 'Save failed. Check the form and try again.';
  } finally {
    saving.value = false;
  }
}

function confirmDelete(type, item) {
  deleteType.value = type;
  deleteTarget.value = item;
  showDeleteModal.value = true;
}

async function performDelete() {
  if (!canEdit.value || !deleteTarget.value) return;
  deleting.value = true;
  try {
    const id = deleteTarget.value.id;
    let message = 'Deleted.';
    if (deleteType.value === 'feature') {
      const { data } = await axios.delete(`/cms/landing/features/${id}`);
      features.value = features.value.filter((f) => f.id !== id);
      message = data.message || message;
    } else if (deleteType.value === 'stat') {
      const { data } = await axios.delete(`/cms/landing/stats/${id}`);
      stats.value = stats.value.filter((s) => s.id !== id);
      message = data.message || message;
    } else if (deleteType.value === 'block') {
      const { data } = await axios.delete(`/cms/landing/blocks/${id}`);
      blocks.value = blocks.value.filter((b) => b.id !== id);
      message = data.message || message;
    } else if (deleteType.value === 'testimonial') {
      const { data } = await axios.delete(`/cms/landing/testimonials/${id}`);
      testimonials.value = testimonials.value.filter((t) => t.id !== id);
      message = data.message || message;
    } else if (deleteType.value === 'plan') {
      const { data } = await axios.delete(`/cms/landing/plans/${id}`);
      plans.value = plans.value.filter((p) => p.id !== id);
      message = data.message || message;
    } else if (deleteType.value === 'faq') {
      const { data } = await axios.delete(`/cms/landing/faqs/${id}`);
      faqs.value = faqs.value.filter((f) => f.id !== id);
      message = data.message || message;
    } else if (deleteType.value === 'step') {
      const { data } = await axios.delete(`/cms/landing/steps/${id}`);
      steps.value = steps.value.filter((s) => s.id !== id);
      message = data.message || message;
    } else if (deleteType.value === 'page') {
      const { data } = await axios.delete(`/cms/landing/pages/${id}`);
      pages.value = pages.value.filter((p) => p.id !== id);
      message = data.message || message;
    }
    flash(message);
    showDeleteModal.value = false;
    deleteTarget.value = null;
  } catch (err) {
    flash('', err.response?.data?.message || 'Delete failed.');
  } finally {
    deleting.value = false;
  }
}

onMounted(loadCms);
</script>

<style scoped>
.field {
  @apply w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-accent disabled:bg-gray-50 disabled:text-gray-500;
}
.icon-btn {
  @apply rounded-md p-1.5 text-gray-500 transition-colors hover:bg-gray-200 hover:text-gray-900;
}
.icon-btn-danger {
  @apply rounded-md p-1.5 text-gray-500 transition-colors hover:bg-red-50 hover:text-red-600;
}
</style>
