<template>
  <div class="p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Landing Page CMS</h1>
        <p class="mt-1 text-sm text-gray-500">Manage marketing content for the public landing page.</p>
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

        <fieldset :disabled="!canEdit" class="grid gap-5 md:grid-cols-2">
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
          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">About title</label>
            <input v-model="settingsForm.about_title" type="text" class="field" />
          </div>
          <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-gray-700">About body</label>
            <textarea v-model="settingsForm.about_body" rows="4" class="field" />
          </div>
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
          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Contact email</label>
            <input v-model="settingsForm.contact_email" type="email" class="field" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Contact phone</label>
            <input v-model="settingsForm.contact_phone" type="text" class="field" />
          </div>
          <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-gray-700">Contact address</label>
            <input v-model="settingsForm.contact_address" type="text" class="field" />
          </div>
          <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-semibold text-gray-700">Footer text</label>
            <textarea v-model="settingsForm.footer_text" rows="2" class="field" />
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
import { usePermissions } from '@/composables/usePermissions';

const { can, permissions, isSuperAdmin } = usePermissions();

const canEdit = computed(() => {
  if (isSuperAdmin.value) return true;
  if (typeof can === 'function' && can('cms.update')) return true;
  const list = permissions.value || [];
  const cmsConfigured = list.some((p) => String(p.slug || '').startsWith('cms.'));
  // If cms.* permissions are not in RBAC yet, assume editable on this admin screen
  return !cmsConfigured;
});

const tabs = [
  { id: 'settings', label: 'Settings' },
  { id: 'features', label: 'Features' },
  { id: 'stats', label: 'Stats' },
  { id: 'testimonials', label: 'Testimonials' },
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
const testimonials = ref([]);

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
  testimonials_title: '',
  cta_title: '',
  cta_body: '',
  cta_button_text: '',
  cta_button_link: '',
  contact_email: '',
  contact_phone: '',
  contact_address: '',
  footer_text: '',
  is_published: true,
});

const settingsForm = ref(emptySettings());

const showModal = ref(false);
const editingId = ref(null);
const featureForm = ref({ icon: '', title: '', description: '', sort_order: 0, is_active: true });
const statForm = ref({ label: '', value: '', icon: '', sort_order: 0, is_active: true });
const testimonialForm = ref({
  name: '',
  role: '',
  company: '',
  quote: '',
  avatar_url: '',
  sort_order: 0,
  is_active: true,
});

const showDeleteModal = ref(false);
const deleteType = ref('');
const deleteTarget = ref(null);

const addButtonLabel = computed(() => {
  if (activeTab.value === 'features') return 'Add Feature';
  if (activeTab.value === 'stats') return 'Add Stat';
  if (activeTab.value === 'testimonials') return 'Add Testimonial';
  return 'Add';
});

const modalTitle = computed(() => {
  const action = editingId.value ? 'Edit' : 'Add';
  if (activeTab.value === 'features') return `${action} Feature`;
  if (activeTab.value === 'stats') return `${action} Stat`;
  if (activeTab.value === 'testimonials') return `${action} Testimonial`;
  return action;
});

const deleteLabel = computed(() => {
  const t = deleteTarget.value;
  if (!t) return '';
  return t.title || t.label || t.name || `#${t.id}`;
});

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
    testimonials.value = data.testimonials || [];
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
    } else if (deleteType.value === 'testimonial') {
      const { data } = await axios.delete(`/cms/landing/testimonials/${id}`);
      testimonials.value = testimonials.value.filter((t) => t.id !== id);
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
