<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Timesheets</h1>
        <p class="text-sm text-gray-500 mt-1">Log daily work hours against projects and tasks</p>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <button
          v-if="canManageOthers"
          @click="$router.push('/timesheets/projects')"
          class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 font-medium rounded-lg transition-colors"
        >
          Manage Projects
        </button>
        <button
          v-if="can('timesheets.create')"
          @click="openAddModal"
          class="inline-flex items-center px-5 py-2.5 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors shadow"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          Add Timesheet Entry
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Entries</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Hours</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.totalHours }}h</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Billable Hours</p>
        <h3 class="text-2xl font-bold text-green-600">{{ stats.billableHours }}h</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Pending Approval</p>
        <h3 class="text-2xl font-bold text-amber-600">{{ stats.pending }}</h3>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="flex flex-wrap items-end gap-4">
        <div v-if="showEmployeeColumn" class="flex-1 min-w-[200px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Search Employee</label>
          <input v-model="filters.search" type="text" placeholder="Search by name or code..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"
            @input="debouncedSearch" />
        </div>
        <div class="min-w-[180px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Project</label>
          <select v-model="filters.project_id" @change="loadTimesheets()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
            <option value="">All Projects</option>
            <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
          </select>
        </div>
        <div class="min-w-[140px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" @change="loadTimesheets()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="submitted">Submitted</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>
        <button @click="resetFilters" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">Reset</button>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
      <p class="font-medium">{{ error }}</p>
      <button @click="loadTimesheets()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <div v-else-if="timesheets.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Timesheet Records</h3>
      <p class="text-gray-500">Click "Add Timesheet Entry" to log work hours.</p>
    </div>

    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th v-if="showEmployeeColumn" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Employee</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Project / Task</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Time</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Hours</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Billable</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="ts in timesheets" :key="ts.id" class="hover:bg-gray-50">
              <td v-if="showEmployeeColumn" class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-semibold text-gray-900">{{ getEmployeeName(ts.employee) }}</div>
                <div class="text-xs text-gray-500">{{ ts.employee?.employee_code }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ formatDate(ts.date) }}</td>
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">{{ ts.project?.name || '—' }}</div>
                <div class="text-xs text-gray-500">{{ ts.task?.title || 'No task' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">
                {{ formatTime(ts.start_time) }} – {{ formatTime(ts.end_time) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900">{{ formatHours(ts.hours_worked) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full" :class="ts.billable ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'">
                  {{ ts.billable ? 'Yes' : 'No' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full" :class="statusBadge(ts.status)">{{ capitalise(ts.status) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="flex items-center justify-center gap-1">
                  <button v-if="ts.status === 'draft'" @click="submitEntry(ts)" class="px-2.5 py-1 text-xs font-medium text-white bg-accent hover:bg-accent-dark rounded-md">Submit</button>
                  <button v-if="canApprove && ts.status === 'submitted'" @click="approveEntry(ts)" class="px-2.5 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md">Approve</button>
                  <button v-if="canApprove && ts.status === 'submitted'" @click="openRejectModal(ts)" class="px-2.5 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">Reject</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="pagination" class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="text-sm text-gray-600">Showing <span class="font-semibold text-gray-900">{{ pagination.total }}</span> records</div>
        <div class="flex items-center space-x-2">
          <button @click="loadTimesheets(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-4 py-2 text-sm font-medium rounded-lg disabled:opacity-50" :class="pagination.current_page === 1 ? 'bg-gray-300 text-gray-500' : 'bg-accent text-white hover:bg-accent-dark'">Previous</button>
          <span class="text-sm text-gray-600">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
          <button @click="loadTimesheets(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-4 py-2 text-sm font-medium rounded-lg disabled:opacity-50" :class="pagination.current_page === pagination.last_page ? 'bg-gray-300 text-gray-500' : 'bg-accent text-white hover:bg-accent-dark'">Next</button>
        </div>
      </div>
    </div>

    <!-- Professional Add Entry Modal -->
    <div v-if="showFormModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl overflow-hidden max-h-[92vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
          <div>
            <h3 class="text-lg font-bold text-gray-900">{{ editingId ? 'Edit Timesheet Entry' : 'New Timesheet Entry' }}</h3>
            <p class="text-xs text-gray-500 mt-0.5">Record productive hours against a project</p>
          </div>
          <button @click="showFormModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>

        <div class="px-6 py-5 space-y-5 overflow-y-auto">
          <!-- Employee (admin / users without profile) -->
          <div v-if="showEmployeePicker" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Employee <span class="text-red-500">*</span></label>
            <select v-model="form.employee_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent bg-white">
              <option value="">Select employee</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ empLabel(emp) }}</option>
            </select>
            <p class="text-xs text-gray-500 mt-1.5">Select who this timesheet entry belongs to.</p>
          </div>

          <!-- Work details -->
          <div>
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Work Details</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Work Date <span class="text-red-500">*</span></label>
                <input v-model="form.date" type="date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Project <span class="text-red-500">*</span></label>
                <select v-model="form.project_id" @change="onProjectChange" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                  <option value="">Select project</option>
                  <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Task</label>
                <select v-model="form.task_id" :disabled="!form.project_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent disabled:bg-gray-100">
                  <option value="">{{ form.project_id ? 'Select task (optional)' : 'Select a project first' }}</option>
                  <option v-for="t in tasks" :key="t.id" :value="t.id">{{ t.title }}</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Time tracking -->
          <div>
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Time Tracking</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Start Time <span class="text-red-500">*</span></label>
                <input v-model="form.start_time" type="time" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">End Time <span class="text-red-500">*</span></label>
                <input v-model="form.end_time" type="time" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Duration</label>
                <div class="w-full px-4 py-2.5 border border-gray-200 rounded-lg bg-gray-50 text-sm font-bold text-gray-900">
                  {{ computedDurationLabel }}
                </div>
              </div>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
              <button v-for="preset in timePresets" :key="preset.label" type="button" @click="applyPreset(preset)"
                class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                {{ preset.label }}
              </button>
            </div>
          </div>

          <!-- Notes -->
          <div>
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Notes</h4>
            <textarea v-model="form.description" rows="3" placeholder="Describe the work completed..."
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
            <label class="mt-3 inline-flex items-center gap-2 cursor-pointer">
              <input v-model="form.billable" type="checkbox" class="h-4 w-4 text-gray-900 border-gray-300 rounded focus:ring-accent" />
              <span class="text-sm font-medium text-gray-700">Mark as billable hours</span>
            </label>
          </div>

          <div v-if="formError" class="text-sm text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 flex flex-wrap justify-end gap-3 bg-gray-50">
          <button @click="showFormModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveEntry('draft')" :disabled="saving" class="px-5 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save as Draft' }}
          </button>
          <button @click="saveEntry('submitted')" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save & Submit' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-bold text-gray-900">Reject Timesheet</h3>
        </div>
        <div class="px-6 py-5">
          <label class="block text-sm font-semibold text-gray-700 mb-1">Rejection Reason <span class="text-red-500">*</span></label>
          <textarea v-model="rejectReason" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3 bg-gray-50">
          <button @click="showRejectModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="rejectEntry" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Reject</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import { useNotification } from '@/composables/useNotification';
import { useEmployeeRecordPicker } from '@/composables/useEmployeeRecordPicker';
import { usePermissions } from '@/composables/usePermissions';

const authStore = useAuthStore();
const { success, error: showError } = useNotification();
const { can } = usePermissions();
const {
  canCreateForOthers: canManageOthers,
  showEmployeePicker,
  applyOwnEmployeeToForm,
  validateEmployeeForSubmit,
} = useEmployeeRecordPicker('timesheets');

const timesheets = ref([]);
const projects = ref([]);
const tasks = ref([]);
const employees = ref([]);
const loading = ref(false);
const error = ref(null);
const pagination = ref(null);
const showFormModal = ref(false);
const showRejectModal = ref(false);
const editingId = ref(null);
const saving = ref(false);
const formError = ref(null);
const rejectReason = ref('');
const rejectingEntry = ref(null);

const user = computed(() => authStore.user || JSON.parse(localStorage.getItem('user') || '{}'));
const role = computed(() => user.value?.role || '');
const isEmployee = computed(() => role.value === 'employee');
const canApprove = computed(() => can('timesheets.approve'));
const showEmployeeColumn = computed(() => canManageOthers.value || canApprove.value);

let searchTimer = null;
const filters = ref({ search: '', project_id: '', status: '' });

const emptyForm = () => {
  const base = {
    employee_id: '',
    date: new Date().toISOString().split('T')[0],
    project_id: '',
    task_id: '',
    start_time: '09:00',
    end_time: '17:00',
    description: '',
    billable: true,
  };
  if (!showEmployeePicker.value && authStore.user?.employee?.id) {
    base.employee_id = authStore.user.employee.id;
  }
  return base;
};

const form = ref(emptyForm());

const timePresets = [
  { label: 'Full day (9–5)', start: '09:00', end: '17:00' },
  { label: 'Morning (9–1)', start: '09:00', end: '13:00' },
  { label: 'Afternoon (2–6)', start: '14:00', end: '18:00' },
  { label: 'Half day (9–1)', start: '09:00', end: '13:00' },
];

const computedDurationMinutes = computed(() => {
  if (!form.value.start_time || !form.value.end_time) return 0;
  const [sh, sm] = form.value.start_time.split(':').map(Number);
  const [eh, em] = form.value.end_time.split(':').map(Number);
  const mins = (eh * 60 + em) - (sh * 60 + sm);
  return mins > 0 ? mins : 0;
});

const computedDurationLabel = computed(() => {
  const mins = computedDurationMinutes.value;
  if (!mins) return 'Invalid range';
  const h = Math.floor(mins / 60);
  const m = mins % 60;
  return m ? `${h}h ${m}m` : `${h}h`;
});

const stats = computed(() => {
  const list = timesheets.value || [];
  return {
    total: pagination.value?.total || list.length,
    totalHours: list.reduce((sum, t) => sum + Math.round((t.hours_worked || 0) / 60 * 10) / 10, 0).toFixed(1),
    billableHours: list.filter(t => t.billable).reduce((sum, t) => sum + Math.round((t.hours_worked || 0) / 60 * 10) / 10, 0).toFixed(1),
    pending: list.filter(t => t.status === 'submitted').length,
  };
});

const loadTimesheets = async (page = 1) => {
  loading.value = true;
  error.value = null;
  try {
    const params = { page };
    if (filters.value.search) params.search = filters.value.search;
    if (filters.value.project_id) params.project_id = filters.value.project_id;
    if (filters.value.status) params.status = filters.value.status;
    const res = await axios.get('/timesheets', { params });
    timesheets.value = res.data.data || [];
    pagination.value = {
      current_page: res.data.current_page,
      last_page: res.data.last_page,
      per_page: res.data.per_page,
      total: res.data.total,
    };
  } catch {
    error.value = 'Failed to load timesheets';
  } finally {
    loading.value = false;
  }
};

const loadProjects = async () => {
  try {
    const res = await axios.get('/timesheets/projects', { params: { per_page: 200 } });
    projects.value = res.data.data || res.data || [];
  } catch (err) {
    console.error('Failed to load projects:', err);
  }
};

const loadEmployees = async () => {
  try {
    let res;
    try {
      res = await axios.get('/employees/dropdown');
    } catch {
      res = await axios.get('/employees/all');
    }
    employees.value = res.data.data || res.data || [];
  } catch (err) {
    console.error('Failed to load employees:', err);
    employees.value = [];
  }
};

const onProjectChange = async () => {
  form.value.task_id = '';
  tasks.value = [];
  if (!form.value.project_id) return;
  try {
    const res = await axios.get(`/timesheets/projects/${form.value.project_id}/tasks`);
    tasks.value = res.data.data || res.data || [];
  } catch (err) {
    console.error('Failed to load tasks:', err);
  }
};

const applyPreset = (preset) => {
  form.value.start_time = preset.start;
  form.value.end_time = preset.end;
};

const debouncedSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => loadTimesheets(), 400);
};

const resetFilters = () => {
  filters.value = { search: '', project_id: '', status: '' };
  loadTimesheets();
};

const openAddModal = async () => {
  editingId.value = null;
  formError.value = null;
  form.value = emptyForm();
  tasks.value = [];
  showFormModal.value = true;
  if (showEmployeePicker.value && employees.value.length === 0) {
    await loadEmployees();
  }
};

const saveEntry = async (status = 'draft') => {
  formError.value = null;

  const employeeCheck = validateEmployeeForSubmit(form);
  if (!employeeCheck.valid) {
    formError.value = employeeCheck.message;
    return;
  }
  if (!form.value.date || !form.value.project_id || !form.value.start_time || !form.value.end_time) {
    formError.value = 'Please fill in all required fields';
    return;
  }
  if (computedDurationMinutes.value <= 0) {
    formError.value = 'End time must be after start time';
    return;
  }

  saving.value = true;
  try {
    const payload = {
      date: form.value.date,
      project_id: form.value.project_id,
      start_time: form.value.start_time,
      end_time: form.value.end_time,
      description: form.value.description || null,
      billable: !!form.value.billable,
      status,
    };

    if (showEmployeePicker.value) {
      payload.employee_id = employeeCheck.employeeId;
    }
    if (form.value.task_id) payload.task_id = form.value.task_id;

    if (editingId.value) {
      await axios.put(`/timesheets/${editingId.value}`, payload);
      if (status === 'submitted') {
        await axios.post(`/timesheets/${editingId.value}/submit`);
      }
    } else {
      await axios.post('/timesheets', payload);
    }

    showFormModal.value = false;
    loadTimesheets(pagination.value?.current_page || 1);
    success(status === 'submitted' ? 'Timesheet saved and submitted' : 'Timesheet draft saved');
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to save entry';
  } finally {
    saving.value = false;
  }
};

const submitEntry = async (ts) => {
  try {
    await axios.post(`/timesheets/${ts.id}/submit`);
    loadTimesheets(pagination.value?.current_page || 1);
    success('Timesheet submitted successfully');
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to submit');
  }
};

const approveEntry = async (ts) => {
  try {
    await axios.post(`/timesheets/${ts.id}/approve`);
    loadTimesheets(pagination.value?.current_page || 1);
    success('Timesheet approved successfully');
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to approve');
  }
};

const openRejectModal = (ts) => {
  rejectingEntry.value = ts;
  rejectReason.value = '';
  showRejectModal.value = true;
};

const rejectEntry = async () => {
  if (!rejectReason.value.trim()) return;
  try {
    await axios.post(`/timesheets/${rejectingEntry.value.id}/reject`, { rejection_reason: rejectReason.value });
    showRejectModal.value = false;
    loadTimesheets(pagination.value?.current_page || 1);
    success('Timesheet rejected');
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to reject');
  }
};

const empLabel = (emp) => {
  const name = emp.full_name || emp.user?.name || `${emp.first_name || ''} ${emp.last_name || ''}`.trim() || 'Employee';
  return emp.employee_code ? `${name} (${emp.employee_code})` : name;
};

const formatDate = (d) => {
  if (!d) return '—';
  try { return new Date(d).toLocaleDateString('en-PK', { year: 'numeric', month: 'short', day: 'numeric' }); }
  catch { return d; }
};

const formatTime = (t) => {
  if (!t) return '';
  const parts = String(t).slice(0, 5).split(':');
  const hr = parseInt(parts[0], 10);
  const m = parts[1] || '00';
  const suffix = hr >= 12 ? 'PM' : 'AM';
  const display = hr % 12 || 12;
  return `${display}:${m} ${suffix}`;
};

const formatHours = (minutes) => {
  if (!minutes) return '0h';
  const h = Math.floor(minutes / 60);
  const m = minutes % 60;
  return m > 0 ? `${h}h ${m}m` : `${h}h`;
};

const getEmployeeName = (emp) => emp?.user?.name || emp?.full_name || `${emp?.first_name || ''} ${emp?.last_name || ''}`.trim() || 'N/A';
const capitalise = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
const statusBadge = (s) => ({
  draft: 'bg-gray-100 text-gray-700',
  submitted: 'bg-amber-100 text-amber-800',
  approved: 'bg-green-100 text-green-800',
  rejected: 'bg-red-100 text-red-800',
}[s] || 'bg-gray-100 text-gray-600');

onMounted(async () => {
  loadTimesheets();
  loadProjects();
  if (showEmployeePicker.value) {
    loadEmployees();
  }
});
</script>
