<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Short Leaves & Exemptions</h1>
      <button v-if="can('short_leaves.apply')" @click="openApplyModal" class="inline-flex items-center px-5 py-2.5 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors shadow">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        New Request
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Requests</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
          </div>
          <div class="bg-gray-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Pending</p>
            <h3 class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</h3>
          </div>
          <div class="bg-yellow-50 rounded-lg p-3">
            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Approved</p>
            <h3 class="text-2xl font-bold text-green-600">{{ stats.approved }}</h3>
          </div>
          <div class="bg-green-50 rounded-lg p-3">
            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Rejected</p>
            <h3 class="text-2xl font-bold text-red-600">{{ stats.rejected }}</h3>
          </div>
          <div class="bg-red-50 rounded-lg p-3">
            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
          <select v-model="filters.category" @change="loadRequests()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
            <option value="">All Types</option>
            <option value="short_leave">Short Leave</option>
            <option value="exemption">Exemption</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" @change="loadRequests()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="resetFilters" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Reset Filters</button>
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
      <button @click="loadRequests()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <!-- Empty -->
    <div v-else-if="requests.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Requests Found</h3>
      <p class="text-gray-500">Click "New Request" to submit a short leave or exemption.</p>
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employee</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Duration</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reason</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="request in requests"
              :key="request.id"
              :id="`short-leave-${request.id}`"
              class="hover:bg-gray-50 transition-colors"
              :class="{ 'bg-accent-soft/50 ring-2 ring-inset ring-accent': highlightedId === request.id }"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-9 w-9 rounded-full bg-gray-200 flex items-center justify-center">
                    <span class="text-sm font-bold text-gray-600">{{ getInitials(request.employee) }}</span>
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-semibold text-gray-900">{{ getEmployeeName(request.employee) }}</div>
                    <div class="text-xs text-gray-500">{{ request.employee?.employee_code }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="request.category === 'exemption' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'">
                  {{ request.category === 'exemption' ? 'Exemption' : 'Short Leave' }}
                </span>
                <div v-if="request.exemption_type" class="text-xs text-gray-500 mt-1">{{ formatExemptionType(request.exemption_type) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ formatDate(request.date) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                <template v-if="request.from_time && request.to_time">{{ formatTime(request.from_time) }} – {{ formatTime(request.to_time) }}</template>
                <span v-else class="text-gray-400">—</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ formatDuration(request.duration_minutes) }}</td>
              <td class="px-6 py-4 max-w-xs"><p class="text-sm text-gray-700 truncate" :title="request.reason">{{ request.reason || '—' }}</p></td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="statusBadge(request.status)">{{ capitalise(request.status) }}</span>
                <div v-if="request.approver" class="text-xs text-gray-500 mt-1">by {{ request.approver.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <div class="flex items-center space-x-2">
                  <template v-if="canDecide(request)">
                    <button @click="approveRequest(request)" class="px-3 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors">Approve</button>
                    <button @click="openRejectModal(request)" class="px-3 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors">Reject</button>
                  </template>
                  <button
                    v-if="canCancel(request)"
                    @click="cancelRequest(request)"
                    class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                  >
                    Cancel
                  </button>
                  <span v-if="!canDecide(request) && !canCancel(request) && request.status !== 'pending'" class="text-xs text-gray-400">Processed</span>
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
          <button @click="loadRequests(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :class="pagination.current_page === 1 ? 'bg-gray-300 text-gray-500' : 'bg-accent text-white hover:bg-accent-dark'">Previous</button>
          <span class="text-sm text-gray-600">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
          <button @click="loadRequests(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :class="pagination.current_page === pagination.last_page ? 'bg-gray-300 text-gray-500' : 'bg-accent text-white hover:bg-accent-dark'">Next</button>
        </div>
      </div>
    </div>

    <!-- Apply Modal -->
    <div v-if="showApplyModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">New {{ form.category === 'exemption' ? 'Exemption' : 'Short Leave' }} Request</h3>
          <button @click="showApplyModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
          <!-- Category toggle -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Request Type *</label>
            <div class="grid grid-cols-2 gap-2">
              <button
                type="button"
                @click="form.category = 'short_leave'"
                class="px-4 py-2 text-sm font-medium rounded-lg border transition-colors"
                :class="form.category === 'short_leave' ? 'bg-accent text-white border-accent' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
              >Short Leave</button>
              <button
                type="button"
                @click="form.category = 'exemption'"
                class="px-4 py-2 text-sm font-medium rounded-lg border transition-colors"
                :class="form.category === 'exemption' ? 'bg-accent text-white border-accent' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
              >Exemption</button>
            </div>
          </div>

          <!-- Employee picker (HR/managers on behalf of others) -->
          <div v-if="showEmployeePickerForRequest">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Employee *</label>
            <div class="relative">
              <input
                v-model="employeeSearch"
                @input="filterEmployees"
                @focus="showEmployeeDropdown = true"
                type="text"
                placeholder="Search employee by name or code..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"
              />
              <div v-if="showEmployeeDropdown && filteredEmployees.length > 0" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                <div
                  v-for="emp in filteredEmployees"
                  :key="emp.id"
                  @click="selectEmployee(emp)"
                  class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0"
                >
                  <div class="font-medium text-gray-900">{{ getEmployeeName(emp) }}</div>
                  <div class="text-xs text-gray-500">{{ emp.employee_code }} • {{ emp.department?.name || 'N/A' }}</div>
                </div>
              </div>
              <div v-if="form.employee_id && selectedEmployee" class="mt-2 px-3 py-2 bg-gray-50 rounded-lg border border-gray-200 flex justify-between items-center">
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ getEmployeeName(selectedEmployee) }}</div>
                  <div class="text-xs text-gray-500">{{ selectedEmployee.employee_code }}</div>
                </div>
                <button @click="clearEmployee" type="button" class="text-red-600 hover:text-red-700">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Exemption type -->
          <div v-if="form.category === 'exemption'">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Exemption Type *</label>
            <select v-model="form.exemption_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
              <option value="">Select Exemption Type</option>
              <option value="late_arrival">Late Arrival</option>
              <option value="early_departure">Early Departure</option>
              <option value="missed_punch">Missed Punch</option>
              <option value="official_duty">Official Duty</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Date *</label>
            <input v-model="form.date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">From Time {{ form.category === 'short_leave' ? '*' : '' }}</label>
              <input v-model="form.from_time" type="time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">To Time {{ form.category === 'short_leave' ? '*' : '' }}</label>
              <input v-model="form.to_time" type="time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
          </div>
          <p v-if="form.category === 'short_leave'" class="text-xs text-gray-500 -mt-2">Short leaves cannot exceed 4 hours.</p>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Reason *</label>
            <textarea v-model="form.reason" rows="3" :placeholder="form.category === 'exemption' ? 'Explain why this exemption is needed...' : 'Describe the reason for the short leave...'" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
          </div>

          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showApplyModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
          <button @click="submitRequest" :disabled="submitting" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark transition-colors disabled:opacity-50">{{ submitting ? 'Submitting...' : 'Submit Request' }}</button>
        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200"><h3 class="text-lg font-bold text-gray-900">Reject Request</h3></div>
        <div class="px-6 py-5">
          <label class="block text-sm font-semibold text-gray-700 mb-1">Remarks (required)</label>
          <textarea v-model="rejectRemarks" rows="3" placeholder="Reason for rejection..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showRejectModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="rejectRequest" :disabled="!rejectRemarks" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">Reject</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import { usePermissions } from '@/composables/usePermissions';
import { useNotification } from '@/composables/useNotification';
import { useDialog } from '@/composables/useDialog';
import { useAuthStore } from '@/stores/auth';
import { useEmployeeRecordPicker } from '@/composables/useEmployeeRecordPicker';
import axios from 'axios';

const route = useRoute();
const { can } = usePermissions();
const { success, error: showError } = useNotification();
const { confirm } = useDialog();
const authStore = useAuthStore();
const { showEmployeePicker } = useEmployeeRecordPicker('short_leaves');

const requests = ref([]);
const employees = ref([]);
const filteredEmployees = ref([]);
const employeeSearch = ref('');
const showEmployeeDropdown = ref(false);
const selectedEmployee = ref(null);
const loading = ref(false);
const error = ref(null);
const pagination = ref(null);
const showApplyModal = ref(false);
const showRejectModal = ref(false);
const selectedRequest = ref(null);
const rejectRemarks = ref('');
const submitting = ref(false);
const formError = ref(null);
const highlightedId = ref(null);

const user = computed(() => authStore.user || JSON.parse(localStorage.getItem('user') || '{}'));
const currentEmployeeId = computed(() => user.value?.employee?.id || null);

const filters = ref({ status: '', category: '' });
const form = ref({
  employee_id: '',
  category: 'short_leave',
  exemption_type: '',
  date: '',
  from_time: '',
  to_time: '',
  reason: '',
});

const stats = computed(() => {
  const list = requests.value || [];
  return {
    total: pagination.value?.total || list.length,
    pending: list.filter(r => r.status === 'pending').length,
    approved: list.filter(r => r.status === 'approved').length,
    rejected: list.filter(r => r.status === 'rejected').length,
  };
});

const showEmployeePickerForRequest = computed(() => can('short_leaves.apply') && showEmployeePicker.value);

const loadRequests = async (page = 1) => {
  loading.value = true;
  error.value = null;
  try {
    const params = { page };
    if (filters.value.status) params.status = filters.value.status;
    if (filters.value.category) params.category = filters.value.category;
    const response = await axios.get('/short-leaves', { params });
    requests.value = response.data.data || [];
    pagination.value = { current_page: response.data.current_page, last_page: response.data.last_page, per_page: response.data.per_page, total: response.data.total };
    await focusHighlighted();
  } catch (err) {
    error.value = 'Failed to load requests';
  } finally {
    loading.value = false;
  }
};

const focusHighlighted = async () => {
  const rawId = route.query.id;
  if (!rawId) return;
  highlightedId.value = Number(rawId) || rawId;
  await nextTick();
  const el = document.getElementById(`short-leave-${highlightedId.value}`);
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
};

const loadEmployees = async () => {
  if (!showEmployeePicker.value) return;
  try {
    let response;
    try {
      response = await axios.get('/employees/dropdown');
    } catch {
      response = await axios.get('/employees');
    }
    employees.value = Array.isArray(response.data) ? response.data : (response.data.data || []);
    filteredEmployees.value = employees.value;
  } catch (err) {
    console.error('Failed to load employees:', err);
  }
};

const filterEmployees = () => {
  const search = employeeSearch.value.toLowerCase();
  if (!search) {
    filteredEmployees.value = employees.value;
    return;
  }
  filteredEmployees.value = employees.value.filter(emp => {
    const fullName = getEmployeeName(emp).toLowerCase();
    const code = (emp.employee_code || '').toLowerCase();
    return fullName.includes(search) || code.includes(search);
  });
};

const selectEmployee = (emp) => {
  selectedEmployee.value = emp;
  form.value.employee_id = emp.id;
  employeeSearch.value = '';
  showEmployeeDropdown.value = false;
};

const clearEmployee = () => {
  selectedEmployee.value = null;
  form.value.employee_id = '';
};

const openApplyModal = () => {
  form.value = { employee_id: '', category: 'short_leave', exemption_type: '', date: '', from_time: '', to_time: '', reason: '' };
  selectedEmployee.value = null;
  employeeSearch.value = '';
  formError.value = null;
  showApplyModal.value = true;
};

const submitRequest = async () => {
  formError.value = null;

  if (showEmployeePickerForRequest.value && !form.value.employee_id) {
    formError.value = 'Please select an employee';
    return;
  }
  if (!form.value.date) {
    formError.value = 'Please select a date';
    return;
  }
  if (form.value.category === 'exemption' && !form.value.exemption_type) {
    formError.value = 'Please select an exemption type';
    return;
  }
  if (form.value.category === 'short_leave' && (!form.value.from_time || !form.value.to_time)) {
    formError.value = 'Please provide from and to times';
    return;
  }
  if (!form.value.reason?.trim()) {
    formError.value = 'Please provide a reason';
    return;
  }

  submitting.value = true;
  try {
    const payload = { ...form.value };
    if (!payload.employee_id) delete payload.employee_id;
    if (payload.category !== 'exemption') payload.exemption_type = null;
    if (!payload.from_time) payload.from_time = null;
    if (!payload.to_time) payload.to_time = null;

    await axios.post('/short-leaves', payload);
    success('Request submitted successfully');
    showApplyModal.value = false;
    loadRequests();
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to submit request';
  } finally {
    submitting.value = false;
  }
};

const canDecide = (request) => {
  if (request.status !== 'pending') return false;
  if (!can('short_leaves.approve')) return false;
  // Cannot decide on own request
  if (currentEmployeeId.value && request.employee_id === currentEmployeeId.value) return false;
  return true;
};

const canCancel = (request) => {
  if (!['pending', 'approved'].includes(request.status)) return false;
  if (can('short_leaves.manage')) return true;
  return currentEmployeeId.value && request.employee_id === currentEmployeeId.value;
};

const approveRequest = async (request) => {
  const confirmed = await confirm({
    title: 'Approve Request',
    message: `Approve this ${request.category === 'exemption' ? 'exemption' : 'short leave'} for ${getEmployeeName(request.employee)}?`,
    confirmText: 'Approve',
  });
  if (!confirmed) return;

  try {
    await axios.post(`/short-leaves/${request.id}/approve`);
    success('Request approved');
    loadRequests(pagination.value?.current_page || 1);
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to approve request');
  }
};

const openRejectModal = (request) => {
  selectedRequest.value = request;
  rejectRemarks.value = '';
  showRejectModal.value = true;
};

const rejectRequest = async () => {
  if (!selectedRequest.value || !rejectRemarks.value) return;
  try {
    await axios.post(`/short-leaves/${selectedRequest.value.id}/reject`, { approval_remarks: rejectRemarks.value });
    success('Request rejected');
    showRejectModal.value = false;
    loadRequests(pagination.value?.current_page || 1);
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to reject request');
  }
};

const cancelRequest = async (request) => {
  const confirmed = await confirm({
    title: 'Cancel Request',
    message: 'Are you sure you want to cancel this request?',
    confirmText: 'Yes, Cancel',
    variant: 'danger',
  });
  if (!confirmed) return;

  try {
    await axios.post(`/short-leaves/${request.id}/cancel`);
    success('Request cancelled');
    loadRequests(pagination.value?.current_page || 1);
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to cancel request');
  }
};

const resetFilters = () => {
  filters.value = { status: '', category: '' };
  loadRequests();
};

const getEmployeeName = (employee) => {
  if (!employee) return 'Unknown';
  const first = employee.first_name || employee.user?.name || '';
  const last = employee.last_name || '';
  return `${first} ${last}`.trim() || 'Unknown';
};

const getInitials = (employee) => {
  const name = getEmployeeName(employee);
  return name.split(' ').filter(Boolean).map(n => n[0]).join('').slice(0, 2).toUpperCase() || '?';
};

const formatDate = (value) => {
  if (!value) return '—';
  return new Date(value).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const formatTime = (value) => {
  if (!value) return '';
  const [hours, minutes] = String(value).split(':');
  const date = new Date();
  date.setHours(Number(hours), Number(minutes));
  return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
};

const formatDuration = (minutes) => {
  if (!minutes) return '—';
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  if (hours && mins) return `${hours}h ${mins}m`;
  if (hours) return `${hours}h`;
  return `${mins}m`;
};

const formatExemptionType = (type) => {
  const labels = {
    late_arrival: 'Late Arrival',
    early_departure: 'Early Departure',
    missed_punch: 'Missed Punch',
    official_duty: 'Official Duty',
    other: 'Other',
  };
  return labels[type] || type;
};

const statusBadge = (status) => {
  const badges = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-600',
  };
  return badges[status] || 'bg-gray-100 text-gray-600';
};

const capitalise = (value) => (value ? value.charAt(0).toUpperCase() + value.slice(1) : '');

onMounted(() => {
  if (route.query.status) filters.value.status = String(route.query.status);
  if (route.query.category) filters.value.category = String(route.query.category);
  loadRequests();
  loadEmployees();
});
</script>
