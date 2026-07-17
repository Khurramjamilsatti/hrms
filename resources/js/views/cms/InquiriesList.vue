<template>
  <div class="p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Leads & Inquiries</h1>
        <p class="mt-1 text-sm text-gray-500">
          Demo requests and contact messages from the public website.
          <span v-if="unreadCount" class="ml-2 inline-flex rounded-full bg-accent px-2 py-0.5 text-xs font-bold text-white">
            {{ unreadCount }} new
          </span>
        </p>
      </div>
      <button
        type="button"
        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        @click="load"
      >
        Refresh
      </button>
    </div>

    <div class="mb-4 flex flex-wrap gap-3">
      <select v-model="filters.status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm" @change="load">
        <option value="">All statuses</option>
        <option value="new">New</option>
        <option value="read">Read</option>
        <option value="archived">Archived</option>
      </select>
      <select v-model="filters.type" class="rounded-lg border border-gray-300 px-3 py-2 text-sm" @change="load">
        <option value="">All types</option>
        <option value="demo">Demo</option>
        <option value="contact">Contact</option>
      </select>
      <input
        v-model="filters.search"
        type="search"
        placeholder="Search name, email, company…"
        class="min-w-[220px] flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm"
        @keyup.enter="load"
      />
    </div>

    <div v-if="error" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ error }}</div>

    <div v-if="loading" class="flex justify-center py-16">
      <div class="h-10 w-10 animate-spin rounded-full border-b-2 border-brand" />
    </div>

    <div v-else class="overflow-hidden rounded-xl border border-surface-border bg-white shadow-card">
      <div v-if="!items.length" class="p-12 text-center text-sm text-gray-500">No inquiries yet.</div>
      <table v-else class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
          <tr>
            <th class="px-4 py-3">Type</th>
            <th class="px-4 py-3">From</th>
            <th class="px-4 py-3">Message</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Received</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr
            v-for="row in items"
            :key="row.id"
            class="hover:bg-gray-50"
            :class="{ 'bg-accent-soft/40': row.status === 'new' }"
          >
            <td class="px-4 py-3">
              <span
                class="rounded-full px-2 py-0.5 text-xs font-bold uppercase"
                :class="row.type === 'demo' ? 'bg-gold-soft text-yellow-900' : 'bg-gray-100 text-gray-700'"
              >
                {{ row.type }}
              </span>
            </td>
            <td class="px-4 py-3">
              <div class="font-semibold text-gray-900">{{ row.name }}</div>
              <div class="text-xs text-gray-500">{{ row.email }}</div>
              <div v-if="row.company" class="text-xs text-gray-400">{{ row.company }}</div>
            </td>
            <td class="max-w-xs px-4 py-3 text-gray-600">
              <div v-if="row.subject" class="text-xs font-medium text-gray-500">{{ row.subject }}</div>
              <div class="line-clamp-2">{{ row.message }}</div>
            </td>
            <td class="px-4 py-3 capitalize text-gray-700">{{ row.status }}</td>
            <td class="whitespace-nowrap px-4 py-3 text-xs text-gray-500">{{ formatDate(row.created_at) }}</td>
            <td class="px-4 py-3 text-right">
              <button type="button" class="text-sm font-semibold text-accent hover:text-accent-dark" @click="open(row)">
                View
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Detail modal -->
    <div v-if="selected" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-xl bg-white shadow-xl">
        <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4">
          <div>
            <h3 class="text-lg font-bold text-gray-900">{{ selected.name }}</h3>
            <p class="text-sm text-gray-500">{{ selected.email }} · {{ selected.type }}</p>
          </div>
          <button type="button" class="text-gray-400 hover:text-gray-700" @click="selected = null">✕</button>
        </div>
        <div class="space-y-3 px-6 py-5 text-sm">
          <p v-if="selected.phone"><span class="font-semibold">Phone:</span> {{ selected.phone }}</p>
          <p v-if="selected.company"><span class="font-semibold">Company:</span> {{ selected.company }}</p>
          <p v-if="selected.subject"><span class="font-semibold">Subject:</span> {{ selected.subject }}</p>
          <p class="whitespace-pre-line rounded-lg bg-gray-50 p-4 text-gray-700">{{ selected.message }}</p>
          <p class="text-xs text-gray-400">IP {{ selected.ip_address || '—' }} · {{ formatDate(selected.created_at) }}</p>
        </div>
        <div class="flex flex-wrap justify-end gap-2 border-t border-gray-200 bg-gray-50 px-6 py-4">
          <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium" @click="setStatus('archived')">
            Archive
          </button>
          <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium" @click="setStatus('read')">
            Mark read
          </button>
          <button type="button" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700" @click="remove">
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import axios from 'axios';

const loading = ref(true);
const error = ref('');
const items = ref([]);
const unreadCount = ref(0);
const selected = ref(null);
const filters = reactive({ status: '', type: '', search: '' });

function formatDate(value) {
  if (!value) return '—';
  return new Date(value).toLocaleString();
}

async function load() {
  loading.value = true;
  error.value = '';
  try {
    const { data } = await axios.get('/cms/inquiries', {
      params: {
        status: filters.status || undefined,
        type: filters.type || undefined,
        search: filters.search || undefined,
      },
    });
    const page = data.inquiries;
    items.value = page?.data || page || [];
    unreadCount.value = data.unread_count || 0;
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load inquiries.';
  } finally {
    loading.value = false;
  }
}

async function open(row) {
  try {
    const { data } = await axios.get(`/cms/inquiries/${row.id}`);
    selected.value = data.inquiry;
    const idx = items.value.findIndex((i) => i.id === row.id);
    if (idx !== -1) items.value[idx] = data.inquiry;
    unreadCount.value = Math.max(0, unreadCount.value - (row.status === 'new' ? 1 : 0));
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to open inquiry.';
  }
}

async function setStatus(status) {
  if (!selected.value) return;
  const { data } = await axios.put(`/cms/inquiries/${selected.value.id}`, { status });
  selected.value = data.inquiry;
  await load();
}

async function remove() {
  if (!selected.value || !confirm('Delete this inquiry?')) return;
  await axios.delete(`/cms/inquiries/${selected.value.id}`);
  selected.value = null;
  await load();
}

onMounted(load);
</script>
