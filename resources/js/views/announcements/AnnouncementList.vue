<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Announcements</h1>
      <button
        v-if="can('announcements.create')"
        @click="openCreateModal"
        class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        New Announcement
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Active</p>
        <h3 class="text-2xl font-bold text-green-600">{{ stats.active }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Urgent / High</p>
        <h3 class="text-2xl font-bold text-red-600">{{ stats.highPriority }}</h3>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
      <p class="font-medium">{{ error }}</p>
      <button @click="loadAnnouncements()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <div v-else-if="announcements.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Announcements</h3>
      <p class="text-gray-500">Click "New Announcement" to post one.</p>
    </div>

    <!-- List -->
    <div v-else class="space-y-4">
      <div
        v-for="announcement in announcements"
        :key="announcement.id"
        class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
      >
        <div class="p-5">
          <div class="flex justify-between items-start gap-4">
            <div class="flex-1 min-w-0">
              <div class="flex flex-wrap items-center gap-2 mb-2">
                <h3 class="text-lg font-bold text-gray-900">{{ announcement.title }}</h3>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold" :class="priorityClass(announcement.priority)">
                  {{ announcement.priority }}
                </span>
                <span
                  class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold"
                  :class="isActive(announcement) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'"
                >
                  {{ isActive(announcement) ? 'Active' : 'Inactive' }}
                </span>
              </div>
              <p class="text-sm text-gray-700 whitespace-pre-wrap mb-3 line-clamp-4">{{ announcement.content }}</p>
              <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500">
                <span>Posted: {{ formatDate(announcement.created_at) }}</span>
                <span v-if="announcement.expiry_date || announcement.end_date">
                  Expires: {{ formatDate(announcement.expiry_date || announcement.end_date) }}
                </span>
              </div>
            </div>
            <div v-if="can('announcements.update') || can('announcements.delete')" class="flex space-x-1 flex-shrink-0">
              <button
                v-if="can('announcements.update')"
                @click="openEditModal(announcement)"
                class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors"
                title="Edit"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
              </button>
              <button
                v-if="can('announcements.delete')"
                @click="openDeleteModal(announcement)"
                class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors"
                title="Delete"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingAnnouncement ? 'Edit Announcement' : 'New Announcement' }}</h3>
          <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title *</label>
            <input v-model="form.title" type="text" placeholder="Announcement title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Content *</label>
            <textarea v-model="form.content" rows="5" placeholder="Write your announcement..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Priority *</label>
              <select v-model="form.priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Expiry Date</label>
              <input v-model="form.expiry_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
          </div>
          <div class="flex items-center space-x-3">
            <label class="relative inline-flex items-center cursor-pointer">
              <input v-model="form.is_active" type="checkbox" class="sr-only peer" />
              <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-gray-900 peer-focus:ring-2 peer-focus:ring-gray-300 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
            </label>
            <span class="text-sm font-medium text-gray-700">Active</span>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveAnnouncement" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50">
            {{ saving ? 'Saving...' : (editingAnnouncement ? 'Update' : 'Create') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
        <div class="px-6 py-5 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
          </div>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Announcement</h3>
          <p class="text-sm text-gray-600">Delete <span class="font-semibold">{{ deletingAnnouncement?.title }}</span>? This cannot be undone.</p>
          <div v-if="formError" class="mt-3 text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="deleteAnnouncement" :disabled="deleting" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePermissions } from '@/composables/usePermissions';
import axios from 'axios';

const { can } = usePermissions();

const announcements = ref([]);
const loading = ref(false);
const error = ref(null);
const showModal = ref(false);
const showDeleteModal = ref(false);
const editingAnnouncement = ref(null);
const deletingAnnouncement = ref(null);
const saving = ref(false);
const deleting = ref(false);
const formError = ref(null);

const form = ref({
  title: '',
  content: '',
  priority: 'medium',
  is_active: true,
  expiry_date: '',
});

const extractList = (payload) => {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  return [];
};

const isActive = (a) => {
  if (a.is_active != null) return !!a.is_active;
  if (a.is_published != null) return !!a.is_published;
  return true;
};

const stats = computed(() => {
  const list = announcements.value || [];
  return {
    total: list.length,
    active: list.filter(isActive).length,
    highPriority: list.filter(a => a.priority === 'high' || a.priority === 'urgent').length,
  };
});

const formatDate = (date) => {
  if (!date) return '—';
  return new Date(date).toLocaleDateString('en-PK', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const priorityClass = (priority) => ({
  low: 'bg-gray-100 text-gray-700',
  medium: 'bg-blue-100 text-blue-800',
  high: 'bg-amber-100 text-amber-800',
  urgent: 'bg-red-100 text-red-700',
}[priority] || 'bg-gray-100 text-gray-700');

const loadAnnouncements = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/announcements');
    announcements.value = extractList(response.data);
  } catch (err) {
    error.value = 'Failed to load announcements';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  editingAnnouncement.value = null;
  formError.value = null;
  form.value = {
    title: '',
    content: '',
    priority: 'medium',
    is_active: true,
    expiry_date: '',
  };
  showModal.value = true;
};

const openEditModal = (announcement) => {
  editingAnnouncement.value = announcement;
  formError.value = null;
  const priority = ['low', 'medium', 'high', 'urgent'].includes(announcement.priority)
    ? announcement.priority
    : 'medium';
  form.value = {
    title: announcement.title || '',
    content: announcement.content || '',
    priority,
    is_active: isActive(announcement),
    expiry_date: (announcement.expiry_date || announcement.end_date)
      ? String(announcement.expiry_date || announcement.end_date).substring(0, 10)
      : '',
  };
  showModal.value = true;
};

const saveAnnouncement = async () => {
  formError.value = null;
  if (!form.value.title.trim()) { formError.value = 'Title is required'; return; }
  if (!form.value.content.trim()) { formError.value = 'Content is required'; return; }
  saving.value = true;
  try {
    const payload = {
      title: form.value.title,
      content: form.value.content,
      priority: form.value.priority,
      is_active: form.value.is_active,
      expiry_date: form.value.expiry_date || null,
    };
    if (editingAnnouncement.value) {
      await axios.put(`/announcements/${editingAnnouncement.value.id}`, payload);
    } else {
      await axios.post('/announcements', payload);
    }
    showModal.value = false;
    await loadAnnouncements();
  } catch (err) {
    formError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {}).flat().join(' ') || 'Failed to save announcement';
  } finally {
    saving.value = false;
  }
};

const openDeleteModal = (announcement) => {
  deletingAnnouncement.value = announcement;
  formError.value = null;
  showDeleteModal.value = true;
};

const deleteAnnouncement = async () => {
  deleting.value = true;
  formError.value = null;
  try {
    await axios.delete(`/announcements/${deletingAnnouncement.value.id}`);
    showDeleteModal.value = false;
    await loadAnnouncements();
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to delete announcement';
  } finally {
    deleting.value = false;
  }
};

onMounted(() => { loadAnnouncements(); });
</script>
