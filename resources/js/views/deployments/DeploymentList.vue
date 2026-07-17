<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Deployments</h1>
        <p class="text-sm text-gray-500 mt-1">Manage employee deployments and assignments</p>
      </div>
      <button
        v-if="canCreate"
        type="button"
        @click="showCreateModal = true"
        class="inline-flex items-center px-5 py-2.5 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors shadow"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Create Deployment
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Deployments</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Active</p>
        <h3 class="text-2xl font-bold text-emerald-600">{{ stats.active }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Pending Approval</p>
        <h3 class="text-2xl font-bold text-amber-600">{{ stats.pending }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">From Long Leave</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.fromLongLeave }}</h3>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="flex flex-col lg:flex-row lg:items-center gap-3">
        <select
          v-model="filters.status"
          class="px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent bg-white text-gray-700 font-medium"
          @change="fetchDeployments()"
        >
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="active">Active</option>
          <option value="completed">Completed</option>
          <option value="extended">Extended</option>
        </select>

        <select
          v-model="filters.deployment_type"
          class="px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent bg-white text-gray-700 font-medium"
          @change="fetchDeployments()"
        >
          <option value="">All Types</option>
          <option value="domestic">Domestic</option>
          <option value="international">International</option>
          <option value="project">Project</option>
          <option value="temporary">Temporary</option>
          <option value="permanent">Permanent</option>
        </select>

        <label class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 text-sm font-medium text-gray-700">
          <input
            v-model="filters.departure_from_long_leave"
            type="checkbox"
            class="rounded border-gray-300 text-accent focus:ring-accent"
            @change="fetchDeployments()"
          />
          From Long Leave
        </label>
      </div>
    </div>

    <div v-if="loading" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <div class="inline-block h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-gray-900 mb-3" />
      <p class="text-sm text-gray-600">Loading deployments…</p>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-5">
      {{ error }}
      <button type="button" class="ml-2 underline text-sm" @click="fetchDeployments()">Try again</button>
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div v-if="deployments.length === 0" class="p-12 text-center text-gray-500">
        No deployments found
      </div>
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Deployment #</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Employee</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Location</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Duration</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="deployment in deployments" :key="deployment.id" class="hover:bg-gray-50">
              <td class="px-5 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ deployment.deployment_number }}</div>
                <span
                  v-if="deployment.departure_from_long_leave"
                  class="mt-1 inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-700"
                >
                  Long Leave
                </span>
              </td>
              <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ employeeName(deployment) }}
              </td>
              <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">
                {{ deployment.deployment_type }}
              </td>
              <td class="px-5 py-4 text-sm text-gray-900">
                <div>{{ deployment.location }}</div>
                <div class="text-xs text-gray-500">{{ deployment.city }}, {{ deployment.country }}</div>
              </td>
              <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-900">
                <div>{{ formatDate(deployment.start_date) }}</div>
                <div class="text-xs text-gray-500">to {{ formatDate(deployment.end_date) }}</div>
                <span v-if="deployment.extension_count > 0" class="text-xs font-medium text-amber-700">
                  +{{ deployment.extension_count }} ext
                </span>
              </td>
              <td class="px-5 py-4 whitespace-nowrap">
                <span
                  class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold capitalize"
                  :class="getStatusClass(deployment.status)"
                >
                  {{ deployment.status }}
                </span>
              </td>
              <td class="px-5 py-4 whitespace-nowrap text-right text-sm">
                <div class="inline-flex flex-wrap justify-end gap-2">
                  <button
                    type="button"
                    class="font-medium text-gray-700 hover:text-gray-900"
                    @click="viewDetails(deployment.id)"
                  >
                    View
                  </button>
                  <button
                    v-if="deployment.status === 'pending' && canManage"
                    type="button"
                    class="font-medium text-emerald-700 hover:text-emerald-900"
                    @click="approveDeployment(deployment)"
                  >
                    Approve
                  </button>
                  <button
                    v-if="deployment.status === 'approved' && canManage"
                    type="button"
                    class="font-medium text-accent hover:text-accent-dark"
                    @click="activateDeployment(deployment)"
                  >
                    Activate
                  </button>
                  <button
                    v-if="deployment.status === 'active' && canManage"
                    type="button"
                    class="font-medium text-amber-700 hover:text-amber-900"
                    @click="showExtendModal(deployment)"
                  >
                    Extend
                  </button>
                  <button
                    v-if="deployment.status === 'active' && canManage"
                    type="button"
                    class="font-medium text-gray-700 hover:text-gray-900"
                    @click="completeDeployment(deployment)"
                  >
                    Complete
                  </button>
                  <button
                    type="button"
                    class="font-medium text-gray-500 hover:text-gray-800"
                    @click="viewHistory(deployment)"
                  >
                    History
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Pagination
      v-if="!loading && deployments.length > 0"
      :current-page="pagination.current_page"
      :total-pages="pagination.last_page"
      :total="pagination.total"
      :from="pagination.from"
      :to="pagination.to"
      @page-change="handlePageChange"
    />

    <!-- Create Deployment Modal -->
    <div
      v-if="showCreateModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="showCreateModal = false"
    >
      <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
          <h3 class="text-lg font-bold text-gray-900">Create Deployment</h3>
          <button type="button" class="text-gray-400 hover:text-gray-600" @click="showCreateModal = false">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <form class="space-y-4 px-6 py-5" @submit.prevent="submitDeployment">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Employee *</label>
              <select v-model="deploymentForm.employee_id" required class="field">
                <option value="">Select Employee</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                  {{ emp.first_name }} {{ emp.last_name }}
                </option>
              </select>
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Deployment Type *</label>
              <select v-model="deploymentForm.deployment_type" required class="field">
                <option value="domestic">Domestic</option>
                <option value="international">International</option>
                <option value="project">Project</option>
                <option value="temporary">Temporary</option>
                <option value="permanent">Permanent</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Country *</label>
              <input v-model="deploymentForm.country" required class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">City *</label>
              <input v-model="deploymentForm.city" required class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Location *</label>
              <input v-model="deploymentForm.location" required class="field" />
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Start Date *</label>
              <input v-model="deploymentForm.start_date" type="date" required class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">End Date *</label>
              <input v-model="deploymentForm.end_date" type="date" required class="field" />
            </div>
          </div>

          <label class="flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700">
            <input v-model="deploymentForm.departure_from_long_leave" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
            Departure from Long Leave
          </label>

          <div v-if="deploymentForm.departure_from_long_leave" class="grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Long Leave Start Date</label>
              <input v-model="deploymentForm.long_leave_start_date" type="date" class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Long Leave End Date</label>
              <input v-model="deploymentForm.long_leave_end_date" type="date" class="field" />
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Project Name</label>
            <input v-model="deploymentForm.project_name" class="field" />
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Allowance Amount</label>
              <input v-model="deploymentForm.allowance_amount" type="number" class="field" />
            </div>
            <div>
              <label class="mb-1 block text-sm font-semibold text-gray-700">Allowance Currency</label>
              <input v-model="deploymentForm.allowance_currency" placeholder="PKR, USD, AED" class="field" />
            </div>
          </div>

          <div class="flex justify-end gap-3 border-t border-gray-200 pt-4">
            <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50" @click="showCreateModal = false">
              Cancel
            </button>
            <button type="submit" :disabled="saving" class="rounded-lg bg-accent px-5 py-2 text-sm font-medium text-white hover:bg-accent-dark disabled:opacity-50">
              {{ saving ? 'Creating…' : 'Create Deployment' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Extend Modal -->
    <div
      v-if="showExtensionModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="showExtensionModal = false"
    >
      <div class="w-full max-w-md rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
          <h3 class="text-lg font-bold text-gray-900">Request Extension</h3>
          <button type="button" class="text-gray-400 hover:text-gray-600" @click="showExtensionModal = false">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <form class="space-y-4 px-6 py-5" @submit.prevent="submitExtension">
          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">New End Date *</label>
            <input v-model="extensionForm.new_end_date" type="date" required class="field" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Reason *</label>
            <textarea v-model="extensionForm.reason" required rows="3" class="field" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Remarks</label>
            <textarea v-model="extensionForm.remarks" rows="2" class="field" />
          </div>
          <div class="flex justify-end gap-3 border-t border-gray-200 pt-4">
            <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50" @click="showExtensionModal = false">
              Cancel
            </button>
            <button type="submit" :disabled="saving" class="rounded-lg bg-accent px-5 py-2 text-sm font-medium text-white hover:bg-accent-dark disabled:opacity-50">
              {{ saving ? 'Submitting…' : 'Request Extension' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Pagination from '@/components/Pagination.vue';
import { useDialog } from '@/composables/useDialog';
import { usePermissions } from '@/composables/usePermissions';

const router = useRouter();
const { confirm, alert } = useDialog();
const { can, canAny, canAccessModule } = usePermissions();

const canCreate = computed(() =>
  can('deployments.create') || canAccessModule('deployments')
);
const canManage = computed(() =>
  canAny(['deployments.manage', 'deployments.approve', 'deployments.update']) || canAccessModule('deployments')
);

const loading = ref(false);
const saving = ref(false);
const error = ref('');
const deployments = ref([]);
const pagination = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0 });
const employees = ref([]);
const stats = ref({ total: 0, active: 0, pending: 0, fromLongLeave: 0 });
const filters = ref({ status: '', deployment_type: '', departure_from_long_leave: false });
const showCreateModal = ref(false);
const showExtensionModal = ref(false);
const selectedDeployment = ref(null);

const emptyForm = () => ({
  employee_id: '',
  deployment_type: 'domestic',
  location: '',
  country: 'Pakistan',
  city: '',
  start_date: '',
  end_date: '',
  departure_from_long_leave: false,
  long_leave_start_date: '',
  long_leave_end_date: '',
  project_name: '',
  allowance_amount: '',
  allowance_currency: 'PKR',
});

const deploymentForm = ref(emptyForm());
const extensionForm = ref({ new_end_date: '', reason: '', remarks: '' });

const employeeName = (deployment) => {
  const emp = deployment.employee;
  if (!emp) return '—';
  return emp.full_name || `${emp.first_name || ''} ${emp.last_name || ''}`.trim() || emp.user?.name || '—';
};

const fetchDeployments = async (page = 1) => {
  loading.value = true;
  error.value = '';
  try {
    const params = new URLSearchParams();
    params.append('page', page);
    if (filters.value.status) params.append('status', filters.value.status);
    if (filters.value.deployment_type) params.append('deployment_type', filters.value.deployment_type);
    if (filters.value.departure_from_long_leave) params.append('departure_from_long_leave', 'true');

    const response = await axios.get(`/deployments?${params}`);
    deployments.value = response.data.data || response.data;

    if (response.data.current_page) {
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        total: response.data.total,
        from: response.data.from || 0,
        to: response.data.to || 0,
      };
    }

    stats.value.total = pagination.value.total || deployments.value.length;
    stats.value.active = deployments.value.filter((d) => d.status === 'active').length;
    stats.value.pending = deployments.value.filter((d) => d.status === 'pending').length;
    stats.value.fromLongLeave = deployments.value.filter((d) => d.departure_from_long_leave).length;
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to fetch deployments';
    console.error('Failed to fetch deployments:', err);
  } finally {
    loading.value = false;
  }
};

const handlePageChange = (page) => {
  fetchDeployments(page);
};

const viewDetails = (deploymentId) => {
  router.push(`/deployments/${deploymentId}`);
};

const fetchEmployees = async () => {
  try {
    const response = await axios.get('/employees/dropdown');
    employees.value = response.data.data || response.data;
  } catch (err) {
    console.error('Failed to fetch employees:', err);
  }
};

const submitDeployment = async () => {
  saving.value = true;
  try {
    await axios.post('/deployments', deploymentForm.value);
    showCreateModal.value = false;
    deploymentForm.value = emptyForm();
    await fetchDeployments();
    await alert({
      title: 'Success',
      message: 'Deployment created successfully!',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'success',
    });
  } catch (err) {
    console.error('Failed to create deployment:', err);
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Failed to create deployment',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  } finally {
    saving.value = false;
  }
};

const approveDeployment = async (deployment) => {
  if (!(await confirm({
    title: 'Approve deployment?',
    message: `Approve deployment ${deployment.deployment_number}?`,
    confirmText: 'Approve',
    cancelText: 'Cancel',
    variant: 'primary',
  }))) return;
  try {
    await axios.post(`/deployments/${deployment.id}/approve`, { remarks: 'Approved' });
    await fetchDeployments();
    await alert({
      title: 'Success',
      message: 'Deployment approved successfully!',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'success',
    });
  } catch (err) {
    console.error('Failed to approve deployment:', err);
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Failed to approve deployment',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  }
};

const activateDeployment = async (deployment) => {
  if (!(await confirm({
    title: 'Activate deployment?',
    message: `Activate deployment ${deployment.deployment_number}?`,
    confirmText: 'Activate',
    cancelText: 'Cancel',
    variant: 'primary',
  }))) return;
  try {
    await axios.post(`/deployments/${deployment.id}/activate`, { remarks: 'Employee deployed' });
    await fetchDeployments();
    await alert({
      title: 'Success',
      message: 'Deployment activated successfully!',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'success',
    });
  } catch (err) {
    console.error('Failed to activate deployment:', err);
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Failed to activate deployment',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  }
};

const completeDeployment = async (deployment) => {
  if (!(await confirm({
    title: 'Complete deployment?',
    message: `Complete deployment ${deployment.deployment_number}?`,
    confirmText: 'Complete',
    cancelText: 'Cancel',
    variant: 'primary',
  }))) return;
  try {
    await axios.post(`/deployments/${deployment.id}/complete`, {
      actual_end_date: new Date().toISOString().split('T')[0],
      remarks: 'Deployment completed',
    });
    await fetchDeployments();
    await alert({
      title: 'Success',
      message: 'Deployment completed successfully!',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'success',
    });
  } catch (err) {
    console.error('Failed to complete deployment:', err);
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Failed to complete deployment',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  }
};

const showExtendModal = (deployment) => {
  selectedDeployment.value = deployment;
  extensionForm.value = { new_end_date: '', reason: '', remarks: '' };
  showExtensionModal.value = true;
};

const submitExtension = async () => {
  saving.value = true;
  try {
    await axios.post(`/deployments/${selectedDeployment.value.id}/extend`, extensionForm.value);
    showExtensionModal.value = false;
    extensionForm.value = { new_end_date: '', reason: '', remarks: '' };
    await fetchDeployments();
    await alert({
      title: 'Success',
      message: 'Extension request submitted successfully!',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'success',
    });
  } catch (err) {
    console.error('Failed to submit extension:', err);
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Failed to submit extension',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  } finally {
    saving.value = false;
  }
};

const viewHistory = async (deployment) => {
  try {
    const response = await axios.get(`/deployments/employees/${deployment.employee_id}/history`);
    const history = response.data.data || response.data;
    await alert({
      title: 'Deployment history',
      message: `Employee has ${history.length} deployment(s) in history`,
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'primary',
    });
  } catch (err) {
    console.error('Failed to fetch deployment history:', err);
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Failed to fetch deployment history',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '—';
  return new Date(dateString).toLocaleDateString('en-PK', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    pending: 'bg-amber-100 text-amber-800',
    approved: 'bg-blue-100 text-blue-800',
    active: 'bg-emerald-100 text-emerald-800',
    completed: 'bg-gray-100 text-gray-800',
    extended: 'bg-amber-100 text-amber-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  fetchDeployments();
  fetchEmployees();
});
</script>

<style scoped>
.field {
  @apply w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-accent;
}
</style>
