<template>
  <div class="p-6 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Shift Rosters</h1>
        <p class="text-sm text-gray-500 mt-1">Plan and publish department work schedules</p>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <button
          type="button"
          @click="$router.push('/shifts/my')"
          class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
        >
          My Schedule
        </button>
        <button
          v-if="canManageShifts"
          type="button"
          @click="$router.push('/shifts')"
          class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
        >
          Manage Shifts
        </button>
        <button
          v-if="can('shifts.create') || can('shifts.manage')"
          type="button"
          @click="openCreate"
          class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Create Roster
        </button>
      </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
      <div v-for="stat in statCards" :key="stat.label" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ stat.label }}</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ stat.value }}</p>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
      <div class="flex flex-wrap gap-3">
        <select
          v-model="filters.status"
          @change="fetchRosters"
          class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
        >
          <option value="">All statuses</option>
          <option value="draft">Draft</option>
          <option value="published">Published</option>
        </select>
        <select
          v-model="filters.department_id"
          @change="fetchRosters"
          class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 min-w-[200px]"
        >
          <option value="">All departments</option>
          <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <div v-else-if="!rosters.length" class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
      <div class="mx-auto w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-4">
        <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      </div>
      <h3 class="text-lg font-semibold text-gray-900">No rosters yet</h3>
      <p class="text-sm text-gray-500 mt-1 mb-5">Create a roster to start assigning employee shifts.</p>
      <button
        v-if="can('shifts.create') || can('shifts.manage')"
        type="button"
        @click="openCreate"
        class="px-5 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800"
      >
        Create Roster
      </button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <div
        v-for="roster in rosters"
        :key="roster.id"
        class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col"
      >
        <div class="flex items-start justify-between gap-3 mb-4">
          <div>
            <h3 class="text-lg font-bold text-gray-900">{{ roster.name }}</h3>
            <p class="text-sm text-gray-500 mt-0.5">{{ roster.department?.name || 'All departments' }}</p>
          </div>
          <span
            class="shrink-0 px-2.5 py-1 text-xs font-semibold rounded-full"
            :class="isPublished(roster) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200'"
          >
            {{ isPublished(roster) ? 'Published' : 'Draft' }}
          </span>
        </div>

        <div class="space-y-2 text-sm text-gray-700 flex-1">
          <div class="flex justify-between gap-3">
            <span class="text-gray-500">Period</span>
            <span class="font-medium text-right">{{ formatDate(roster.start_date) }} – {{ formatDate(roster.end_date) }}</span>
          </div>
          <div class="flex justify-between gap-3">
            <span class="text-gray-500">Created by</span>
            <span class="font-medium">{{ roster.creator?.name || '—' }}</span>
          </div>
          <div v-if="roster.published_at" class="flex justify-between gap-3">
            <span class="text-gray-500">Published</span>
            <span class="font-medium">{{ formatDate(roster.published_at) }}</span>
          </div>
        </div>

        <div class="flex flex-wrap gap-2 mt-5 pt-4 border-t border-gray-100">
          <button
            v-if="can('shifts.update')"
            type="button"
            @click="editRoster(roster)"
            class="px-3 py-2 text-sm font-medium text-gray-800 bg-gray-100 rounded-lg hover:bg-gray-200"
          >
            Edit
          </button>
          <button
            v-if="!isPublished(roster) && can('shifts.manage')"
            type="button"
            @click="publishRoster(roster)"
            class="px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800"
          >
            Publish
          </button>
        </div>
      </div>
    </div>

    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h3 class="text-lg font-bold text-gray-900">{{ editingRoster ? 'Edit Roster' : 'Create Roster' }}</h3>
          <button type="button" @click="closeForm" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <form @submit.prevent="saveRoster" class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Roster name *</label>
            <input v-model="form.name" type="text" required placeholder="e.g. July Ops Roster" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Department</label>
            <select v-model="form.department_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
              <option value="">All departments</option>
              <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
            </select>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Start date *</label>
              <input v-model="form.start_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">End date *</label>
              <input v-model="form.end_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
          </div>
          <p v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</p>
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="closeForm" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
            <button type="submit" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50">
              {{ saving ? 'Saving...' : 'Save Roster' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import { useDialog } from '@/composables/useDialog';
import { useNotification } from '@/composables/useNotification';
import { usePermissions } from '@/composables/usePermissions';
import { useRouter } from 'vue-router';

const router = useRouter();
const { confirm } = useDialog();
const { success, error: showError } = useNotification();
const { can, canAny } = usePermissions();
const canManageShifts = computed(() =>
  canAny(['shifts.assign', 'shifts.manage', 'shifts.create', 'shifts.update', 'shifts.delete'])
);

const rosters = ref([]);
const departments = ref([]);
const loading = ref(false);
const showForm = ref(false);
const saving = ref(false);
const formError = ref(null);
const editingRoster = ref(null);

const filters = reactive({
  status: '',
  department_id: '',
});

const form = reactive({
  name: '',
  department_id: '',
  start_date: '',
  end_date: '',
});

const isPublished = (roster) =>
  roster.status === 'published' || roster.is_published === true;

const statCards = computed(() => {
  const published = rosters.value.filter(isPublished).length;
  return [
    { label: 'Total', value: rosters.value.length },
    { label: 'Published', value: published },
    { label: 'Draft', value: rosters.value.length - published },
    { label: 'Departments', value: new Set(rosters.value.map((r) => r.department_id).filter(Boolean)).size },
  ];
});

const fetchRosters = async () => {
  loading.value = true;
  try {
    const params = {};
    if (filters.status) params.status = filters.status;
    if (filters.department_id) params.department_id = filters.department_id;
    const response = await axios.get('/shift-scheduling/rosters', { params });
    rosters.value = response.data.data || response.data || [];
  } catch (err) {
    console.error(err);
    showError('Failed to load rosters');
  } finally {
    loading.value = false;
  }
};

const fetchDepartments = async () => {
  try {
    const response = await axios.get('/departments', { params: { per_page: 200 } });
    departments.value = response.data.data || response.data || [];
  } catch (err) {
    console.error(err);
  }
};

const openCreate = () => {
  editingRoster.value = null;
  formError.value = null;
  Object.assign(form, { name: '', department_id: '', start_date: '', end_date: '' });
  showForm.value = true;
};

const editRoster = (roster) => {
  editingRoster.value = roster;
  formError.value = null;
  Object.assign(form, {
    name: roster.name,
    department_id: roster.department_id || '',
    start_date: String(roster.start_date || '').slice(0, 10),
    end_date: String(roster.end_date || '').slice(0, 10),
  });
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
  editingRoster.value = null;
  formError.value = null;
};

const saveRoster = async () => {
  saving.value = true;
  formError.value = null;
  try {
    const payload = {
      name: form.name,
      department_id: form.department_id || null,
      start_date: form.start_date,
      end_date: form.end_date,
    };
    if (editingRoster.value) {
      await axios.put(`/shift-scheduling/rosters/${editingRoster.value.id}`, payload);
      success('Roster updated');
    } else {
      await axios.post('/shift-scheduling/rosters', payload);
      success('Roster created');
    }
    closeForm();
    await fetchRosters();
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to save roster';
  } finally {
    saving.value = false;
  }
};

const publishRoster = async (roster) => {
  if (!(await confirm({
    title: 'Publish roster?',
    message: `Publish "${roster.name}"? Employees will see assigned shifts.`,
    confirmText: 'Publish',
    cancelText: 'Cancel',
    variant: 'primary',
  }))) return;

  try {
    await axios.post(`/shift-scheduling/rosters/${roster.id}/publish`);
    success('Roster published');
    await fetchRosters();
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to publish roster');
  }
};

const formatDate = (date) =>
  date ? new Date(date).toLocaleDateString('en-PK', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

onMounted(async () => {
  if (!canManageShifts.value) {
    router.replace('/shifts/my');
    return;
  }
  await Promise.all([fetchRosters(), fetchDepartments()]);
});
</script>
