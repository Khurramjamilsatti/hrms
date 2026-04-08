<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Leave Management</h1>
      <button v-if="can('leaves.apply')" @click="showApplyModal = true" class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Apply Leave
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Applications</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
          </div>
          <div class="bg-gray-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
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
            <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
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
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" @change="loadLeaves()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
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
      <button @click="loadLeaves()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <!-- Empty -->
    <div v-else-if="leaves.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Leave Applications</h3>
      <p class="text-gray-500">Click "Apply Leave" to submit one.</p>
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employee</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Leave Type</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Duration</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Days</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reason</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Approval Status</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="leave in leaves" :key="leave.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-9 w-9 rounded-full bg-gray-200 flex items-center justify-center">
                    <span class="text-sm font-bold text-gray-600">{{ getInitials(leave.employee) }}</span>
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-semibold text-gray-900">{{ getEmployeeName(leave.employee) }}</div>
                    <div class="text-xs text-gray-500">{{ leave.employee?.employee_code }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="leaveTypeBadge(leave.leave_type?.name)">{{ leave.leave_type?.name || 'N/A' }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                <div>{{ formatDate(leave.start_date) }}</div>
                <div class="text-xs text-gray-500">to {{ formatDate(leave.end_date) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-semibold text-gray-900">{{ leave.total_days }}</span>
                <span class="text-xs text-gray-500 ml-1">{{ Number(leave.total_days) > 1 ? 'days' : 'day' }}</span>
              </td>
              <td class="px-6 py-4 max-w-xs"><p class="text-sm text-gray-700 truncate">{{ leave.reason || '—' }}</p></td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="statusBadge(leave.status)">{{ capitalise(leave.status) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="space-y-1">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="approvalLevelBadge(leave.approval_level)">
                    {{ formatApprovalLevel(leave.approval_level) }}
                  </span>
                  <div v-if="leave.first_approver" class="text-xs text-gray-500">
                    1st: {{ leave.first_approver.name }}
                  </div>
                  <div v-if="leave.final_approver" class="text-xs text-gray-500">
                    Final: {{ leave.final_approver.name }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <!-- Section Head: Can approve first level for employees in their section -->
                <div v-if="canApproveFirstLevel(leave)" class="flex items-center space-x-2">
                  <button @click="approveLeave(leave)" class="px-3 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors">Approve (1st)</button>
                  <button @click="openRejectModal(leave)" class="px-3 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors">Reject</button>
                </div>
                <!-- Admin: Can give final approval -->
                <div v-else-if="canApproveFinal(leave)" class="flex items-center space-x-2">
                  <button @click="approveLeave(leave)" class="px-3 py-1 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">Final Approve</button>
                  <button @click="openRejectModal(leave)" class="px-3 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors">Reject</button>
                </div>
                <span v-else-if="leave.approval_level === 'first_approved'" class="text-xs text-gray-500 italic">Pending admin approval</span>
                <span v-else-if="leave.status !== 'pending'" class="text-xs text-gray-400">Processed</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination -->
      <div v-if="pagination" class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="text-sm text-gray-600">Showing <span class="font-semibold text-gray-900">{{ pagination.total }}</span> records</div>
        <div class="flex items-center space-x-2">
          <button @click="loadLeaves(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :class="pagination.current_page === 1 ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'">Previous</button>
          <span class="text-sm text-gray-600">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
          <button @click="loadLeaves(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :class="pagination.current_page === pagination.last_page ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'">Next</button>
        </div>
      </div>
    </div>

    <!-- Apply Leave Modal -->
    <div v-if="showApplyModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">Apply for Leave</h3>
          <button @click="showApplyModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        <div class="px-6 py-5 space-y-4">
          <!-- Employee Selector (Admin/Manager only) -->
          <div v-if="can('leaves.apply') && isAdminOrManager">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Employee *</label>
            <div class="relative">
              <input 
                v-model="employeeSearch" 
                @input="filterEmployees"
                @focus="showEmployeeDropdown = true"
                type="text" 
                placeholder="Search employee by name or code..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
              />
              <div v-if="showEmployeeDropdown && filteredEmployees.length > 0" 
                   class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                <div 
                  v-for="emp in filteredEmployees" 
                  :key="emp.id"
                  @click="selectEmployee(emp)"
                  class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0"
                >
                  <div class="font-medium text-gray-900">{{ getEmployeeFullName(emp) }}</div>
                  <div class="text-xs text-gray-500">{{ emp.employee_code }} • {{ emp.department?.name || 'N/A' }}</div>
                </div>
              </div>
              <div v-if="form.employee_id && selectedEmployee" class="mt-2 px-3 py-2 bg-gray-50 rounded-lg border border-gray-200 flex justify-between items-center">
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ getEmployeeFullName(selectedEmployee) }}</div>
                  <div class="text-xs text-gray-500">{{ selectedEmployee.employee_code }}</div>
                </div>
                <button @click="clearEmployee" type="button" class="text-red-600 hover:text-red-700">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Leave Type</label>
            <select v-model="form.leave_type_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
              <option value="">Select Leave Type</option>
              <option v-for="lt in leaveTypes" :key="lt.id" :value="lt.id">{{ lt.name }} ({{ lt.days_per_year }} days/yr)</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date</label>
              <input v-model="form.start_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">End Date</label>
              <input v-model="form.end_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Reason</label>
            <textarea v-model="form.reason" rows="3" placeholder="Describe the reason for leave..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"></textarea>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showApplyModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
          <button @click="submitLeave" :disabled="submitting" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors disabled:opacity-50">{{ submitting ? 'Submitting...' : 'Submit Application' }}</button>
        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200"><h3 class="text-lg font-bold text-gray-900">Reject Leave Application</h3></div>
        <div class="px-6 py-5">
          <label class="block text-sm font-semibold text-gray-700 mb-1">Remarks (required)</label>
          <textarea v-model="rejectRemarks" rows="3" placeholder="Reason for rejection..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"></textarea>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showRejectModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="rejectLeave" :disabled="!rejectRemarks" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">Reject</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePermissions } from '@/composables/usePermissions';
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';

const { can } = usePermissions();
const { success, error: showError } = useNotification();

const leaves = ref([]);
const leaveTypes = ref([]);
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
const selectedLeave = ref(null);
const rejectRemarks = ref('');
const submitting = ref(false);
const formError = ref(null);

const user = JSON.parse(localStorage.getItem('user') || '{}');

const filters = ref({ status: '' });
const form = ref({ 
  employee_id: '', 
  leave_type_id: '', 
  start_date: '', 
  end_date: '', 
  reason: '' 
});

const stats = computed(() => {
  const list = leaves.value || [];
  return {
    total: pagination.value?.total || list.length,
    pending: list.filter(l => l.status === 'pending').length,
    approved: list.filter(l => l.status === 'approved').length,
    rejected: list.filter(l => l.status === 'rejected').length
  };
});

const isAdminOrManager = computed(() => {
  return user.role === 'admin' || user.role === 'manager';
});

const loadLeaves = async (page = 1) => {
  loading.value = true;
  error.value = null;
  try {
    const params = { page };
    if (filters.value.status) params.status = filters.value.status;
    const response = await axios.get('/leave-applications', { params });
    leaves.value = response.data.data || [];
    pagination.value = { current_page: response.data.current_page, last_page: response.data.last_page, per_page: response.data.per_page, total: response.data.total };
  } catch (err) {
    error.value = 'Failed to load leave applications';
  } finally {
    loading.value = false;
  }
};

const loadLeaveTypes = async () => {
  try {
    const response = await axios.get('/leave-types');
    leaveTypes.value = Array.isArray(response.data) ? response.data : (response.data.data || []);
  } catch (err) { console.error('Failed to load leave types:', err); }
};

const loadEmployees = async () => {
  if (!isAdminOrManager.value) return;
  try {
    const response = await axios.get('/employees');
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
    const fullName = getEmployeeFullName(emp).toLowerCase();
    const code = (emp.employee_code || '').toLowerCase();
    return fullName.includes(search) || code.includes(search);
  });
};

const selectEmployee = (emp) => {
  selectedEmployee.value = emp;
  form.value.employee_id = emp.id;
  employeeSearch.value = getEmployeeFullName(emp);
  showEmployeeDropdown.value = false;
};

const clearEmployee = () => {
  selectedEmployee.value = null;
  form.value.employee_id = '';
  employeeSearch.value = '';
  filteredEmployees.value = employees.value;
};

const getEmployeeFullName = (emp) => {
  return emp?.user?.name || `${emp?.first_name || ''} ${emp?.last_name || ''}`.trim() || 'N/A';
};

const submitLeave = async () => {
  formError.value = null;
  
  // Validation
  const requiredEmployeeId = isAdminOrManager.value
    ? form.value.employee_id 
    : (user.employee?.id || user.id);
  
  if (isAdminOrManager.value && !form.value.employee_id) {
    formError.value = 'Please select an employee';
    return;
  }
  
  if (!form.value.leave_type_id || !form.value.start_date || !form.value.end_date || !form.value.reason) { 
    formError.value = 'Please fill in all fields'; 
    return; 
  }
  
  submitting.value = true;
  try {
    await axios.post('/leave-applications', { 
      ...form.value, 
      employee_id: requiredEmployeeId 
    });
    success('Leave application submitted successfully');
    showApplyModal.value = false;
    form.value = { 
      employee_id: '', 
      leave_type_id: '', 
      start_date: '', 
      end_date: '', 
      reason: '' 
    };
    employeeSearch.value = '';
    selectedEmployee.value = null;
    loadLeaves();
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to submit';
  } finally { submitting.value = false; }
};

const approveLeave = async (leave) => {
  try { 
    const response = await axios.post(`/leave-applications/${leave.id}/approve`); 
    if (response.data.message) {
      success(response.data.message);
    }
    loadLeaves(pagination.value?.current_page || 1); 
  } catch (err) { 
    showError(err.response?.data?.message || 'Failed to approve leave'); 
  }
};

const openRejectModal = (leave) => { selectedLeave.value = leave; rejectRemarks.value = ''; showRejectModal.value = true; };

const rejectLeave = async () => {
  try { 
    await axios.post(`/leave-applications/${selectedLeave.value.id}/reject`, { approval_remarks: rejectRemarks.value }); 
    success('Leave rejected successfully');
    showRejectModal.value = false; 
    loadLeaves(pagination.value?.current_page || 1); 
  } catch (err) { 
    showError(err.response?.data?.message || 'Failed to reject leave'); 
  }
};

const resetFilters = () => { filters.value = { status: '' }; loadLeaves(); };

// Approval workflow helpers
const canApproveFirstLevel = (leave) => {
  // Section Head can approve first level if:
  // 1. User is section_head
  // 2. Leave is pending (not yet approved)
  // 3. Employee reports to them (manager_id matches)
  // 4. It's not their own leave
  if (!user.role || user.role !== 'section_head') return false;
  if (leave.status !== 'pending' || leave.approval_level !== 'pending') return false;
  if (leave.employee_id === user.employee?.id) return false; // Cannot approve own leave
  
  // Check if the employee reports to this section head
  const employeeManagerId = leave.employee?.manager_id || leave.employee?.user?.id;
  return employeeManagerId && employeeManagerId === user.id && can('leaves.approve');
};

const canApproveFinal = (leave) => {
  // Admin/HR can give final approval if:
  // 1. User is admin/hr_admin/super_admin
  // 2. For section employees: Must have first_approved status
  // 3. For section heads (their own leaves): Can approve directly
  if (!['admin', 'hr_admin', 'super_admin'].includes(user.role)) return false;
  if (leave.status !== 'pending') return false;
  
  // If employee is in a section and it's not the section head themselves, require first approval
  const isInSection = leave.employee?.department_id !== null;
  const isSectionHeadOwnLeave = leave.employee?.user?.role === 'section_head';
  
  if (isInSection && !isSectionHeadOwnLeave) {
    return leave.approval_level === 'first_approved' && can('leaves.approve');
  }
  
  // Section head's own leave or non-section employees can be approved directly by admin
  return can('leaves.approve');
};

const formatApprovalLevel = (level) => {
  const labels = {
    'pending': 'Pending',
    'first_approved': '1st Level Approved',
    'final_approved': 'Fully Approved',
    'rejected': 'Rejected',
    'cancelled': 'Cancelled'
  };
  return labels[level] || level;
};

const approvalLevelBadge = (level) => {
  const badges = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'first_approved': 'bg-blue-100 text-blue-800',
    'final_approved': 'bg-green-100 text-green-800',
    'rejected': 'bg-red-100 text-red-800',
    'cancelled': 'bg-gray-100 text-gray-600'
  };
  return badges[level] || 'bg-gray-100 text-gray-600';
};

const getEmployeeName = (emp) => emp?.user?.name || `${emp?.first_name || ''} ${emp?.last_name || ''}`.trim() || 'N/A';
const getInitials = (emp) => getEmployeeName(emp).split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
const capitalise = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
const statusBadge = (s) => ({ pending: 'bg-yellow-100 text-yellow-800', approved: 'bg-green-100 text-green-800', rejected: 'bg-red-100 text-red-800', cancelled: 'bg-gray-100 text-gray-600' }[s] || 'bg-gray-100 text-gray-600');
const leaveTypeBadge = (name) => ({ 'Annual Leave': 'bg-blue-100 text-blue-800', 'Sick Leave': 'bg-orange-100 text-orange-800', 'Casual Leave': 'bg-purple-100 text-purple-800', 'Maternity Leave': 'bg-pink-100 text-pink-800', 'Unpaid Leave': 'bg-gray-100 text-gray-600' }[name] || 'bg-gray-100 text-gray-700');

onMounted(() => { 
  loadLeaves(); 
  loadLeaveTypes(); 
  if (isAdminOrManager.value) {
    loadEmployees();
  }
  
  // Close dropdown when clicking outside
  if (typeof window !== 'undefined') {
    window.addEventListener('click', (e) => {
      if (!e.target.closest('.relative')) {
        showEmployeeDropdown.value = false;
      }
    });
  }
});
</script>
