<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Timesheets</h1>
      <button @click="openAddModal" class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Add Entry
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Entries</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
          </div>
          <div class="bg-gray-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Hours</p>
            <h3 class="text-2xl font-bold text-blue-600">{{ stats.totalHours }}h</h3>
          </div>
          <div class="bg-blue-50 rounded-lg p-3">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Billable Hours</p>
            <h3 class="text-2xl font-bold text-green-600">{{ stats.billableHours }}h</h3>
          </div>
          <div class="bg-green-50 rounded-lg p-3">
            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Pending Approval</p>
            <h3 class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</h3>
          </div>
          <div class="bg-yellow-50 rounded-lg p-3">
            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="flex flex-wrap items-end gap-4">
        <div v-if="!isEmployee" class="flex-1 min-w-[200px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Search Employee</label>
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input v-model="filters.search" type="text" placeholder="Search by name or code..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
              @input="debouncedSearch" />
          </div>
        </div>
        <div class="min-w-[160px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Project</label>
          <select v-model="filters.project_id" @change="loadTimesheets()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            <option value="">All Projects</option>
            <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
          </select>
        </div>
        <div class="min-w-[130px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" @change="loadTimesheets()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="submitted">Submitted</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>
        <div>
          <button @click="resetFilters" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Reset</button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
      <p class="font-medium">{{ error }}</p>
      <button @click="loadTimesheets()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <!-- Empty -->
    <div v-else-if="timesheets.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Timesheet Records</h3>
      <p class="text-gray-500">Click "Add Entry" to log your work hours.</p>
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th v-if="isAdminOrManager" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employee</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Project</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Task</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Hours</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Billable</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="ts in timesheets" :key="ts.id" class="hover:bg-gray-50 transition-colors">
              <td v-if="isAdminOrManager" class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                    <span class="text-xs font-bold text-gray-600">{{ getInitials(ts.employee) }}</span>
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-semibold text-gray-900">{{ getEmployeeName(ts.employee) }}</div>
                    <div class="text-xs text-gray-500">{{ ts.employee?.employee_code }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ formatDate(ts.date) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ts.project?.name || '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ ts.task?.title || '—' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">
                {{ formatTime(ts.start_time) }} – {{ formatTime(ts.end_time) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="text-sm font-bold text-gray-900">{{ formatHours(ts.hours_worked) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span v-if="ts.billable" class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Yes</span>
                <span v-else class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">No</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full" :class="statusBadge(ts.status)">{{ capitalise(ts.status) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="flex items-center justify-center space-x-1">
                  <button v-if="ts.status === 'draft'" @click="submitEntry(ts)" class="px-2.5 py-1 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors" title="Submit">Submit</button>
                  <button v-if="isAdminOrManager && ts.status === 'submitted'" @click="approveEntry(ts)" class="px-2.5 py-1 text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 rounded-md transition-colors" title="Approve">Approve</button>
                  <button v-if="isAdminOrManager && ts.status === 'submitted'" @click="openRejectModal(ts)" class="px-2.5 py-1 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors" title="Reject">Reject</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination" class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="text-sm text-gray-600">Showing <span class="font-semibold text-gray-900">{{ pagination.total }}</span> records</div>
        <div class="flex items-center space-x-2">
          <button @click="loadTimesheets(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :class="pagination.current_page === 1 ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'">Previous</button>
          <span class="text-sm text-gray-600">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
          <button @click="loadTimesheets(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :class="pagination.current_page === pagination.last_page ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'">Next</button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showFormModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingId ? 'Edit' : 'Add' }} Timesheet Entry</h3>
          <button @click="showFormModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
            <input v-model="form.date" type="date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Project <span class="text-red-500">*</span></label>
            <select v-model="form.project_id" @change="loadTasks" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
              <option value="">Select Project</option>
              <option v-for="p in projects" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Task</label>
            <select v-model="form.task_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
              <option value="">Select Task (optional)</option>
              <option v-for="t in tasks" :key="t.id" :value="t.id">{{ t.title }}</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Start Time <span class="text-red-500">*</span></label>
              <input v-model="form.start_time" type="time" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">End Time <span class="text-red-500">*</span></label>
              <input v-model="form.end_time" type="time" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea v-model="form.description" rows="3" placeholder="What did you work on?" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"></textarea>
          </div>
          <div class="flex items-center">
            <input v-model="form.billable" type="checkbox" id="billable" class="h-4 w-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900" />
            <label for="billable" class="ml-2 text-sm font-medium text-gray-700">Billable</label>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showFormModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveEntry" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50">
            {{ saving ? 'Saving...' : (editingId ? 'Update' : 'Save') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-bold text-gray-900">Reject Timesheet</h3>
        </div>
        <div class="px-6 py-5">
          <label class="block text-sm font-semibold text-gray-700 mb-1">Rejection Reason <span class="text-red-500">*</span></label>
          <textarea v-model="rejectReason" rows="3" required placeholder="Provide a reason for rejection..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"></textarea>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
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
import { useNotification } from '@/composables/useNotification';

const { success, error: showError } = useNotification();

const timesheets = ref([]);
const projects = ref([]);
const tasks = ref([]);
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

const user = JSON.parse(localStorage.getItem('user') || '{}');
const isAdminOrManager = computed(() => user.role === 'admin' || user.role === 'manager');
const isEmployee = computed(() => user.role === 'employee');

let searchTimer = null;
const filters = ref({ search: '', project_id: '', status: '' });

const form = ref({
  date: new Date().toISOString().split('T')[0],
  project_id: '',
  task_id: '',
  start_time: '09:00',
  end_time: '17:00',
  description: '',
  billable: true,
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
    pagination.value = { current_page: res.data.current_page, last_page: res.data.last_page, per_page: res.data.per_page, total: res.data.total };
  } catch (err) {
    error.value = 'Failed to load timesheets';
  } finally {
    loading.value = false;
  }
};

const loadProjects = async () => {
  try {
    const res = await axios.get('/timesheets/projects');
    projects.value = res.data.data || res.data;
  } catch (err) {
    console.error('Failed to load projects:', err);
  }
};

const loadTasks = async () => {
  tasks.value = [];
  if (!form.value.project_id) return;
  try {
    const res = await axios.get(`/timesheets/projects/${form.value.project_id}/tasks`);
    tasks.value = res.data.data || res.data;
  } catch (err) {
    console.error('Failed to load tasks:', err);
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => loadTimesheets(), 400);
};

const resetFilters = () => {
  filters.value = { search: '', project_id: '', status: '' };
  loadTimesheets();
};

const openAddModal = () => {
  editingId.value = null;
  formError.value = null;
  form.value = {
    date: new Date().toISOString().split('T')[0],
    project_id: '',
    task_id: '',
    start_time: '09:00',
    end_time: '17:00',
    description: '',
    billable: true,
  };
  tasks.value = [];
  showFormModal.value = true;
};

const saveEntry = async () => {
  formError.value = null;
  if (!form.value.date || !form.value.project_id || !form.value.start_time || !form.value.end_time) {
    formError.value = 'Please fill in all required fields';
    return;
  }
  saving.value = true;
  try {
    // Get current employee id
    const meRes = await axios.get('/me');
    const employeeId = meRes.data.employee?.id;
    if (!employeeId) { formError.value = 'Employee profile not found'; saving.value = false; return; }

    const payload = { ...form.value, employee_id: employeeId };
    if (!payload.task_id) delete payload.task_id;

    if (editingId.value) {
      await axios.put(`/timesheets/${editingId.value}`, payload);
    } else {
      await axios.post('/timesheets', payload);
    }
    showFormModal.value = false;
    loadTimesheets(pagination.value?.current_page || 1);
    success(editingId.value ? 'Timesheet updated successfully' : 'Timesheet entry created successfully');
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

const formatDate = (d) => {
  if (!d) return '—';
  try { return new Date(d).toLocaleDateString('en-PK', { year: 'numeric', month: 'short', day: 'numeric' }); } catch { return d; }
};

const formatTime = (t) => {
  if (!t) return '';
  const [h, m] = t.split(':');
  const hr = parseInt(h);
  return `${hr > 12 ? hr - 12 : hr}:${m} ${hr >= 12 ? 'PM' : 'AM'}`;
};

const formatHours = (minutes) => {
  if (!minutes) return '0h';
  const h = Math.floor(minutes / 60);
  const m = minutes % 60;
  return m > 0 ? `${h}h ${m}m` : `${h}h`;
};

const getEmployeeName = (emp) => {
  if (!emp) return 'N/A';
  return emp.user?.name || `${emp.first_name || ''} ${emp.last_name || ''}`.trim() || 'N/A';
};

const getInitials = (emp) => getEmployeeName(emp).split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

const capitalise = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';

const statusBadge = (s) => ({
  draft: 'bg-gray-100 text-gray-700',
  submitted: 'bg-yellow-100 text-yellow-800',
  approved: 'bg-green-100 text-green-800',
  rejected: 'bg-red-100 text-red-800',
}[s] || 'bg-gray-100 text-gray-600');

onMounted(() => {
  loadTimesheets();
  loadProjects();
});
</script>
