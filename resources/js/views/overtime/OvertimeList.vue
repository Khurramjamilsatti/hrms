<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Overtime Requests</h1>
      <button @click="openCreateModal" class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Request Overtime
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Pending</p>
        <h3 class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Approved</p>
        <h3 class="text-2xl font-bold text-green-600">{{ stats.approved }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Rejected</p>
        <h3 class="text-2xl font-bold text-red-600">{{ stats.rejected }}</h3>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" @change="fetchRequests" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
      <p class="font-medium">{{ error }}</p>
      <button @click="fetchRequests" class="mt-2 text-sm underline">Try again</button>
    </div>

    <div v-else-if="requests.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Overtime Requests</h3>
      <p class="text-gray-500">Click "Request Overtime" to submit one.</p>
    </div>

    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Employee</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Hours</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reason</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="req in requests" :key="req.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ getEmployeeName(req.employee) }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ formatDate(req.date) }}</td>
            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ req.hours }}h</td>
            <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">{{ req.reason || '—' }}</td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs rounded-full font-semibold capitalize" :class="statusClass(req.status)">{{ req.status }}</span>
            </td>
            <td class="px-6 py-4 text-sm">
              <div v-if="canApprove && (req.status === 'pending' || req.approval_level === 'first_approved')" class="flex items-center space-x-2">
                <button @click="approveRequest(req)" class="px-3 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md">Approve</button>
                <button @click="openRejectModal(req)" class="px-3 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">Reject</button>
              </div>
              <span v-else class="text-xs text-gray-400">—</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create Modal -->
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">Request Overtime</h3>
          <button @click="showForm = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        <form @submit.prevent="submitRequest">
          <div class="px-6 py-5 space-y-4">
            <div v-if="needsEmployeePicker">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Employee</label>
              <select v-model="form.employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="">Select employee</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                  {{ empLabel(emp) }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
              <input v-model="form.date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Hours</label>
              <input v-model="form.hours" type="number" step="0.5" min="0.5" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Reason</label>
              <textarea v-model="form.reason" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"></textarea>
            </div>
            <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
          </div>
          <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
            <button type="button" @click="showForm = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
            <button type="submit" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50">{{ saving ? 'Submitting...' : 'Submit' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Reject Modal -->
    <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-bold text-gray-900">Reject Overtime</h3>
        </div>
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Remarks</label>
            <textarea v-model="rejectRemarks" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"></textarea>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showRejectModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="rejectRequest" :disabled="actionLoading" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">{{ actionLoading ? 'Rejecting...' : 'Reject' }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import { useDialog } from '@/composables/useDialog';
import { usePermissions } from '@/composables/usePermissions';
import { useEmployeeRecordPicker } from '@/composables/useEmployeeRecordPicker';

const authStore = useAuthStore();
const { alert } = useDialog();
const { can } = usePermissions();
const {
  showEmployeePicker: needsEmployeePicker,
  applyOwnEmployeeToForm,
  validateEmployeeForSubmit,
} = useEmployeeRecordPicker('overtime');
const requests = ref([]);
const employees = ref([]);
const loading = ref(false);
const error = ref(null);
const showForm = ref(false);
const saving = ref(false);
const formError = ref(null);
const showRejectModal = ref(false);
const rejectRemarks = ref('');
const selectedRequest = ref(null);
const actionLoading = ref(false);
const filters = reactive({ status: '' });

const form = reactive({ employee_id: '', date: '', hours: '', reason: '' });

const canApprove = computed(() => can('overtime.approve'));

const stats = computed(() => {
  const list = requests.value || [];
  return {
    total: list.length,
    pending: list.filter(r => r.status === 'pending').length,
    approved: list.filter(r => r.status === 'approved').length,
    rejected: list.filter(r => r.status === 'rejected').length
  };
});

const fetchRequests = async () => {
  loading.value = true;
  error.value = null;
  try {
    const params = {};
    if (filters.status) params.status = filters.status;
    const response = await axios.get('/overtime-requests', { params });
    requests.value = response.data.data || response.data || [];
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load overtime requests';
  } finally {
    loading.value = false;
  }
};

const fetchEmployees = async () => {
  if (!needsEmployeePicker.value) return;
  try {
    const response = await axios.get('/employees/dropdown');
    employees.value = response.data.data || response.data || [];
  } catch (err) {
    try {
      const response = await axios.get('/employees/all');
      employees.value = response.data.data || response.data || [];
    } catch {
      employees.value = [];
    }
  }
};

const openCreateModal = () => {
  formError.value = null;
  Object.assign(form, {
    employee_id: '',
    date: '',
    hours: '',
    reason: '',
  });
  applyOwnEmployeeToForm(form);
  showForm.value = true;
  if (needsEmployeePicker.value && employees.value.length === 0) {
    fetchEmployees();
  }
};

const submitRequest = async () => {
  formError.value = null;
  const employeeCheck = validateEmployeeForSubmit(form);
  if (!employeeCheck.valid) {
    formError.value = employeeCheck.message;
    return;
  }

  saving.value = true;
  try {
    const payload = {
      date: form.date,
      hours: form.hours,
      reason: form.reason,
    };
    if (needsEmployeePicker.value) {
      payload.employee_id = employeeCheck.employeeId;
    }
    await axios.post('/overtime-requests', payload);
    showForm.value = false;
    fetchRequests();
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to submit overtime request';
  } finally {
    saving.value = false;
  }
};

const approveRequest = async (req) => {
  actionLoading.value = true;
  try {
    await axios.post(`/overtime-requests/${req.id}/approve`, { approval_remarks: 'Approved' });
    fetchRequests();
  } catch (err) {
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Failed to approve request',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  } finally {
    actionLoading.value = false;
  }
};

const openRejectModal = (req) => {
  selectedRequest.value = req;
  rejectRemarks.value = '';
  showRejectModal.value = true;
};

const rejectRequest = async () => {
  if (!selectedRequest.value) return;
  if (!rejectRemarks.value.trim()) {
    await alert({
      title: 'Error',
      message: 'Please provide rejection remarks',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
    return;
  }
  actionLoading.value = true;
  try {
    await axios.post(`/overtime-requests/${selectedRequest.value.id}/reject`, {
      approval_remarks: rejectRemarks.value,
    });
    showRejectModal.value = false;
    fetchRequests();
  } catch (err) {
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Failed to reject request',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  } finally {
    actionLoading.value = false;
  }
};

const empLabel = (emp) => {
  const name = emp.full_name || emp.name || `${emp.first_name || ''} ${emp.last_name || ''}`.trim() || emp.user?.name || 'Employee';
  const code = emp.employee_code ? ` (${emp.employee_code})` : '';
  return `${name}${code}`;
};

const getEmployeeName = (emp) => {
  if (!emp) return '—';
  return emp.full_name || emp.user?.name || emp.name || `${emp.first_name || ''} ${emp.last_name || ''}`.trim() || '—';
};
const formatDate = (date) => date ? new Date(date).toLocaleDateString('en-PK') : '—';
const statusClass = (status) => ({
  pending: 'bg-yellow-100 text-yellow-800',
  approved: 'bg-green-100 text-green-800',
  rejected: 'bg-red-100 text-red-800'
}[status] || 'bg-gray-100 text-gray-800');

onMounted(() => {
  fetchRequests();
  if (needsEmployeePicker.value) {
    fetchEmployees();
  }
});
</script>
