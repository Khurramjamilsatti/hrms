<template>
  <div class="p-6">
    <!-- Header -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
        <p class="mt-1 text-sm text-gray-500">
          Stay on top of approvals, payroll, leave, and system updates
        </p>
      </div>
      <div class="flex flex-wrap gap-2">
        <button
          type="button"
          :disabled="!unreadCount || acting"
          class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
          @click="markAllAsRead"
        >
          Mark all as read
        </button>
        <button
          type="button"
          :disabled="!notifications.length || acting"
          class="inline-flex items-center rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-700 hover:bg-red-50 disabled:opacity-50"
          @click="clearAll"
        >
          Clear all
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
      <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Unread</p>
        <p class="mt-2 text-3xl font-bold text-accent">{{ unreadCount }}</p>
      </div>
      <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ pagination.total || notifications.length }}</p>
      </div>
      <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">High priority</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ highPriorityCount }}</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="mb-5 flex flex-col gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
      <div class="flex flex-wrap gap-2">
        <button
          v-for="tab in filterTabs"
          :key="tab.id"
          type="button"
          class="rounded-full px-4 py-1.5 text-sm font-semibold transition"
          :class="filter === tab.id
            ? 'bg-brand text-white'
            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
          @click="setFilter(tab.id)"
        >
          {{ tab.label }}
          <span
            v-if="tab.id === 'unread' && unreadCount"
            class="ml-1 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-accent px-1.5 text-[10px] text-white"
          >
            {{ unreadCount }}
          </span>
        </button>
      </div>
      <select
        v-model="typeFilter"
        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-accent"
        @change="loadNotifications()"
      >
        <option value="">All types</option>
        <option v-for="type in typeOptions" :key="type" :value="type">{{ formatType(type) }}</option>
      </select>
    </div>

    <div v-if="loading" class="flex justify-center py-20">
      <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-gray-900" />
    </div>

    <div v-else-if="error" class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
      {{ error }}
      <button type="button" class="ml-2 underline" @click="loadNotifications()">Try again</button>
    </div>

    <!-- Empty -->
    <div
      v-else-if="!notifications.length"
      class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center shadow-sm"
    >
      <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-accent-soft text-accent">
        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-900">You're all caught up</h3>
      <p class="mx-auto mt-2 max-w-sm text-sm text-gray-500">
        No {{ filter === 'unread' ? 'unread ' : filter === 'read' ? 'read ' : '' }}notifications right now. New updates will show up here.
      </p>
    </div>

    <!-- Feed -->
    <div v-else class="space-y-8">
      <section v-for="group in groupedNotifications" :key="group.label">
        <h2 class="mb-3 text-xs font-bold uppercase tracking-[0.16em] text-gray-400">
          {{ group.label }}
        </h2>
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
          <article
            v-for="(item, index) in group.items"
            :key="item.id"
            class="group relative flex gap-4 px-4 py-4 transition hover:bg-gray-50 sm:px-5"
            :class="[
              index !== group.items.length - 1 ? 'border-b border-gray-100' : '',
              !item.is_read ? 'bg-accent-soft/30' : '',
            ]"
          >
            <div
              class="mt-0.5 flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
              :class="iconWrapClass(item)"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" :d="iconPath(item.type)" />
              </svg>
            </div>

            <button
              type="button"
              class="min-w-0 flex-1 text-left"
              @click="openNotification(item)"
            >
              <div class="flex flex-wrap items-start justify-between gap-2">
                <div class="min-w-0">
                  <div class="flex flex-wrap items-center gap-2">
                    <h3
                      class="text-sm font-semibold text-gray-900"
                      :class="{ 'font-bold': !item.is_read }"
                    >
                      {{ item.title }}
                    </h3>
                    <span
                      v-if="!item.is_read"
                      class="inline-flex rounded-full bg-accent px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white"
                    >
                      New
                    </span>
                    <span
                      v-if="item.priority === 'high'"
                      class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-red-700"
                    >
                      High
                    </span>
                  </div>
                  <p class="mt-1 text-sm leading-relaxed text-gray-600">{{ item.message }}</p>
                  <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-400">
                    <span class="rounded-md bg-gray-100 px-2 py-0.5 font-medium text-gray-600">
                      {{ formatType(item.type) }}
                    </span>
                    <span>{{ relativeTime(item.created_at) }}</span>
                    <span v-if="hasNotificationTarget(item)" class="font-semibold text-accent">Open →</span>
                  </div>
                </div>
                <span
                  v-if="!item.is_read"
                  class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-accent"
                  title="Unread"
                />
              </div>
            </button>

            <div class="flex shrink-0 flex-col gap-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100">
              <button
                v-if="!item.is_read"
                type="button"
                class="rounded-lg px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-100"
                title="Mark as read"
                @click.stop="markAsRead(item)"
              >
                Read
              </button>
              <button
                type="button"
                class="rounded-lg px-2 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50"
                title="Delete"
                @click.stop="deleteNotification(item)"
              >
                Delete
              </button>
            </div>
          </article>
        </div>
      </section>

      <Pagination
        v-if="pagination.last_page > 1"
        :current-page="pagination.current_page"
        :total-pages="pagination.last_page"
        :total="pagination.total"
        :from="pagination.from"
        :to="pagination.to"
        @page-change="loadNotifications"
      />
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Pagination from '@/components/Pagination.vue';
import { useDialog } from '@/composables/useDialog';
import { hasNotificationTarget, resolveNotificationTarget } from '@/utils/notificationTarget';

const router = useRouter();
const { confirm, alert } = useDialog();

const loading = ref(true);
const acting = ref(false);
const error = ref('');
const notifications = ref([]);
const unreadCount = ref(0);
const filter = ref('all');
const typeFilter = ref('');
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

const filterTabs = [
  { id: 'all', label: 'All' },
  { id: 'unread', label: 'Unread' },
  { id: 'read', label: 'Read' },
];

const ICON_PATHS = {
  leave: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
  payroll: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
  attendance: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
  loan: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
  recruitment: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
  announcement: 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
  default: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
};

const highPriorityCount = computed(() =>
  notifications.value.filter((n) => n.priority === 'high').length
);

const typeOptions = computed(() => {
  const types = new Set(notifications.value.map((n) => n.type).filter(Boolean));
  return Array.from(types).sort();
});

const groupedNotifications = computed(() => {
  const groups = { Today: [], Yesterday: [], Earlier: [] };
  const today = startOfDay(new Date());
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);

  notifications.value.forEach((item) => {
    const created = startOfDay(new Date(item.created_at));
    if (created.getTime() === today.getTime()) groups.Today.push(item);
    else if (created.getTime() === yesterday.getTime()) groups.Yesterday.push(item);
    else groups.Earlier.push(item);
  });

  return Object.entries(groups)
    .filter(([, items]) => items.length)
    .map(([label, items]) => ({ label, items }));
});

function startOfDay(date) {
  const d = new Date(date);
  d.setHours(0, 0, 0, 0);
  return d;
}

function setFilter(id) {
  filter.value = id;
  loadNotifications();
}

function formatType(type) {
  return String(type || 'General')
    .replace(/([a-z])([A-Z])/g, '$1 $2')
    .replace(/[_-]+/g, ' ')
    .replace(/\b\w/g, (c) => c.toUpperCase());
}

function iconKey(type = '') {
  const t = String(type).toLowerCase();
  if (t.includes('leave')) return 'leave';
  if (t.includes('payroll') || t.includes('salary')) return 'payroll';
  if (t.includes('attendance') || t.includes('check')) return 'attendance';
  if (t.includes('loan') || t.includes('advance')) return 'loan';
  if (t.includes('recruit') || t.includes('job') || t.includes('interview')) return 'recruitment';
  if (t.includes('announce')) return 'announcement';
  return 'default';
}

function iconPath(type) {
  return ICON_PATHS[iconKey(type)] || ICON_PATHS.default;
}

function iconWrapClass(item) {
  if (item.priority === 'high') return 'bg-red-100 text-red-600';
  if (!item.is_read) return 'bg-accent-soft text-accent';
  return 'bg-gray-100 text-gray-600';
}

function relativeTime(dateString) {
  const date = new Date(dateString);
  const diff = Date.now() - date.getTime();
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(diff / 3600000);
  const days = Math.floor(diff / 86400000);

  if (minutes < 1) return 'Just now';
  if (minutes < 60) return `${minutes}m ago`;
  if (hours < 24) return `${hours}h ago`;
  if (days < 7) return `${days}d ago`;
  return date.toLocaleDateString('en-PK', { month: 'short', day: 'numeric', year: 'numeric' });
}

async function fetchUnreadCount() {
  try {
    const { data } = await axios.get('/notifications/unread-count');
    unreadCount.value = data.unread_count ?? data.count ?? 0;
  } catch (_) {
    unreadCount.value = notifications.value.filter((n) => !n.is_read).length;
  }
}

async function loadNotifications(page = 1) {
  loading.value = true;
  error.value = '';
  try {
    const params = { page };
    if (filter.value === 'unread') params.is_read = false;
    if (filter.value === 'read') params.is_read = true;
    if (typeFilter.value) params.type = typeFilter.value;

    const { data } = await axios.get('/notifications', { params });
    notifications.value = data.data || data;

    if (data.current_page) {
      pagination.value = {
        current_page: data.current_page,
        last_page: data.last_page,
        total: data.total,
        from: data.from || 0,
        to: data.to || 0,
      };
    }

    await fetchUnreadCount();
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load notifications';
  } finally {
    loading.value = false;
  }
}

async function markAsRead(item) {
  if (item.is_read) return;
  acting.value = true;
  try {
    await axios.post(`/notifications/${item.id}/mark-read`);
    item.is_read = true;
    item.read_at = new Date().toISOString();
    unreadCount.value = Math.max(0, unreadCount.value - 1);
  } catch (err) {
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Could not mark as read',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  } finally {
    acting.value = false;
  }
}

async function openNotification(item) {
  await markAsRead(item);
  const target = resolveNotificationTarget(item);
  if (!target) return;

  if (/^https?:\/\//i.test(target)) {
    window.location.href = target;
    return;
  }

  router.push(target);
}

async function markAllAsRead() {
  acting.value = true;
  try {
    await axios.post('/notifications/mark-all-read');
    notifications.value.forEach((n) => {
      n.is_read = true;
    });
    unreadCount.value = 0;
  } catch (err) {
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Could not mark all as read',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  } finally {
    acting.value = false;
  }
}

async function deleteNotification(item) {
  if (!(await confirm({
    title: 'Delete notification?',
    message: 'This notification will be removed permanently.',
    confirmText: 'Delete',
    cancelText: 'Cancel',
    variant: 'danger',
  }))) return;

  acting.value = true;
  try {
    await axios.delete(`/notifications/${item.id}`);
    notifications.value = notifications.value.filter((n) => n.id !== item.id);
    if (!item.is_read) unreadCount.value = Math.max(0, unreadCount.value - 1);
    pagination.value.total = Math.max(0, (pagination.value.total || 1) - 1);
  } catch (err) {
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Could not delete notification',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  } finally {
    acting.value = false;
  }
}

async function clearAll() {
  if (!(await confirm({
    title: 'Clear all notifications?',
    message: 'Every notification will be permanently deleted.',
    confirmText: 'Clear all',
    cancelText: 'Cancel',
    variant: 'danger',
  }))) return;

  acting.value = true;
  try {
    await axios.delete('/notifications/clear-all');
    notifications.value = [];
    unreadCount.value = 0;
    pagination.value = { current_page: 1, last_page: 1, total: 0, from: 0, to: 0 };
  } catch (err) {
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Could not clear notifications',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  } finally {
    acting.value = false;
  }
}

onMounted(() => loadNotifications());
</script>
