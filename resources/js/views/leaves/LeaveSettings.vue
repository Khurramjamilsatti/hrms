<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Leave Settings</h1>
      <div class="flex items-center gap-2">
        <button
          v-if="can('leaves.manage') && activeTab === 'types'"
          @click="openTypeModal()"
          class="inline-flex items-center px-5 py-2.5 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors shadow"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          Add Leave Type
        </button>
        <button
          v-if="can('leaves.manage') && activeTab === 'balances'"
          @click="openBalanceModal()"
          class="inline-flex items-center px-5 py-2.5 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors shadow"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          Allocate Balance
        </button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow border border-gray-200 mb-6">
      <div class="border-b border-gray-200 px-4">
        <nav class="flex gap-1 -mb-px">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="switchTab(tab.id)"
            class="px-4 py-3 text-sm font-medium border-b-2 transition-colors"
            :class="activeTab === tab.id ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700'"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
      <p class="font-medium">{{ error }}</p>
      <button @click="reload()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <!-- Leave Types Tab -->
    <template v-else-if="activeTab === 'types'">
      <div v-if="leaveTypes.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14H7v-2h5v2zm5-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">No Leave Types</h3>
        <p class="text-gray-500">Click "Add Leave Type" to create one.</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="lt in leaveTypes" :key="lt.id" class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
          <div class="p-5">
            <div class="flex items-start justify-between mb-3">
              <div>
                <h3 class="text-lg font-bold text-gray-900">{{ lt.name }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ lt.days_per_year }} days / year</p>
              </div>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" :class="lt.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700'">
                {{ lt.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
            <p class="text-sm text-gray-600 mb-4 line-clamp-2 min-h-[2.5rem]">{{ lt.description || 'No description provided.' }}</p>
            <div class="flex flex-wrap gap-2 text-xs">
              <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">{{ lt.is_paid ? 'Paid' : 'Unpaid' }}</span>
              <span v-if="lt.is_carry_forward" class="px-2 py-0.5 rounded bg-blue-50 text-blue-700">Carry forward (max {{ lt.max_carry_forward_days ?? 0 }})</span>
              <span v-if="lt.requires_document" class="px-2 py-0.5 rounded bg-amber-50 text-amber-700">Requires document</span>
            </div>
          </div>
          <div v-if="can('leaves.manage')" class="px-5 py-3 bg-gray-50 border-t border-gray-200 flex justify-end space-x-2">
            <button @click="openTypeModal(lt)" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded-md transition-colors" title="Edit">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
            </button>
            <button @click="openDeleteModal(lt)" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Delete">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </template>

    <!-- Leave Balances Tab -->
    <template v-else-if="activeTab === 'balances'">
      <div class="bg-white rounded-lg shadow border border-gray-200 mb-4 p-4 flex flex-wrap gap-3 items-end">
        <div>
          <label class="block text-xs font-semibold text-gray-500 mb-1">Year</label>
          <input v-model.number="balanceFilters.year" type="number" min="2000" max="2100" @change="loadBalances" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent w-28" />
        </div>
        <div class="flex-1 min-w-[180px]">
          <label class="block text-xs font-semibold text-gray-500 mb-1">Leave Type</label>
          <select v-model="balanceFilters.leave_type_id" @change="loadBalances" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
            <option value="">All types</option>
            <option v-for="lt in leaveTypes" :key="lt.id" :value="lt.id">{{ lt.name }}</option>
          </select>
        </div>
      </div>

      <div v-if="balances.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
        <h3 class="text-lg font-semibold text-gray-900 mb-1">No Leave Balances</h3>
        <p class="text-gray-500">Allocate balances to employees for the selected year.</p>
      </div>

      <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employee</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Leave Type</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Year</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Used</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Remaining</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="b in balances" :key="b.id" class="hover:bg-gray-50">
                <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ employeeName(b) }}</td>
                <td class="px-5 py-4 text-sm text-gray-600">{{ b.leave_type?.name || b.leaveType?.name || '—' }}</td>
                <td class="px-5 py-4 text-sm text-gray-600">{{ b.year }}</td>
                <td class="px-5 py-4 text-sm text-gray-900 text-right">{{ b.total_days }}</td>
                <td class="px-5 py-4 text-sm text-amber-600 text-right">{{ b.used_days }}</td>
                <td class="px-5 py-4 text-sm text-green-600 font-medium text-right">{{ b.remaining_days }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="balanceMeta.last_page > 1" class="px-5 py-3 border-t border-gray-200 flex items-center justify-between bg-gray-50">
          <p class="text-sm text-gray-500">Page {{ balanceMeta.current_page }} of {{ balanceMeta.last_page }}</p>
          <div class="flex gap-2">
            <button :disabled="balanceMeta.current_page <= 1" @click="loadBalances(balanceMeta.current_page - 1)" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg disabled:opacity-40 hover:bg-white">Previous</button>
            <button :disabled="balanceMeta.current_page >= balanceMeta.last_page" @click="loadBalances(balanceMeta.current_page + 1)" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg disabled:opacity-40 hover:bg-white">Next</button>
          </div>
        </div>
      </div>
    </template>

    <!-- Leave Type Modal -->
    <div v-if="showTypeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingType ? 'Edit Leave Type' : 'Add Leave Type' }}</h3>
          <button @click="showTypeModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4 overflow-y-auto">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Name *</label>
            <input v-model="typeForm.name" type="text" placeholder="e.g. Annual Leave" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea v-model="typeForm.description" rows="2" placeholder="Optional description..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Days Per Year *</label>
            <input v-model.number="typeForm.days_per_year" type="number" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div v-if="typeForm.is_carry_forward">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Max Carry Forward Days</label>
            <input v-model.number="typeForm.max_carry_forward_days" type="number" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div class="space-y-3">
            <div class="flex items-center space-x-3">
              <label class="relative inline-flex items-center cursor-pointer">
                <input v-model="typeForm.is_paid" type="checkbox" class="sr-only peer" />
                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-accent after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
              </label>
              <span class="text-sm font-medium text-gray-700">Paid leave</span>
            </div>
            <div class="flex items-center space-x-3">
              <label class="relative inline-flex items-center cursor-pointer">
                <input v-model="typeForm.is_carry_forward" type="checkbox" class="sr-only peer" />
                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-accent after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
              </label>
              <span class="text-sm font-medium text-gray-700">Allow carry forward</span>
            </div>
            <div class="flex items-center space-x-3">
              <label class="relative inline-flex items-center cursor-pointer">
                <input v-model="typeForm.requires_document" type="checkbox" class="sr-only peer" />
                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-accent after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
              </label>
              <span class="text-sm font-medium text-gray-700">Requires document</span>
            </div>
            <div class="flex items-center space-x-3">
              <label class="relative inline-flex items-center cursor-pointer">
                <input v-model="typeForm.is_active" type="checkbox" class="sr-only peer" />
                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-accent after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
              </label>
              <span class="text-sm font-medium text-gray-700">Active</span>
            </div>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showTypeModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveType" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">
            {{ saving ? 'Saving...' : (editingType ? 'Update' : 'Create') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Allocate Balance Modal -->
    <div v-if="showBalanceModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">Allocate Leave Balance</h3>
          <button @click="showBalanceModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Employee *</label>
            <select v-model="balanceForm.employee_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
              <option value="">Select employee</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ empDisplayName(emp) }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Leave Type *</label>
            <select v-model="balanceForm.leave_type_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
              <option value="">Select leave type</option>
              <option v-for="lt in leaveTypes" :key="lt.id" :value="lt.id">{{ lt.name }}</option>
            </select>
          </div>
          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Year *</label>
              <input v-model.number="balanceForm.year" type="number" min="2000" max="2100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Total Days *</label>
              <input v-model.number="balanceForm.total_days" type="number" min="0" step="0.5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Used Days</label>
              <input v-model.number="balanceForm.used_days" type="number" min="0" step="0.5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showBalanceModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveBalance" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Allocate' }}
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
          <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Leave Type</h3>
          <p class="text-sm text-gray-600">Are you sure you want to delete <span class="font-semibold">{{ deletingType?.name }}</span>? This action cannot be undone.</p>
          <div v-if="formError" class="mt-3 text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg text-left">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="deleteType" :disabled="deleting" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePermissions } from '@/composables/usePermissions';
import axios from 'axios';

const { can } = usePermissions();

const tabs = [
  { id: 'types', label: 'Leave Types' },
  { id: 'balances', label: 'Leave Balances' },
];

const activeTab = ref('types');
const leaveTypes = ref([]);
const balances = ref([]);
const employees = ref([]);
const loading = ref(false);
const error = ref(null);
const saving = ref(false);
const deleting = ref(false);
const formError = ref(null);

const showTypeModal = ref(false);
const showBalanceModal = ref(false);
const showDeleteModal = ref(false);
const editingType = ref(null);
const deletingType = ref(null);

const balanceMeta = ref({ current_page: 1, last_page: 1 });
const balanceFilters = ref({
  year: new Date().getFullYear(),
  leave_type_id: '',
});

const emptyTypeForm = () => ({
  name: '',
  description: '',
  days_per_year: 0,
  is_paid: true,
  is_carry_forward: false,
  max_carry_forward_days: 0,
  requires_document: false,
  is_active: true,
});

const typeForm = ref(emptyTypeForm());
const balanceForm = ref({
  employee_id: '',
  leave_type_id: '',
  year: new Date().getFullYear(),
  total_days: 0,
  used_days: 0,
});

const empDisplayName = (emp) => {
  if (!emp) return '—';
  if (emp.full_name) return emp.full_name;
  const name = `${emp.first_name || ''} ${emp.last_name || ''}`.trim();
  return name || emp.user?.name || emp.employee_code || `#${emp.id}`;
};

const employeeName = (b) => {
  const emp = b.employee;
  if (!emp) return '—';
  return empDisplayName(emp) || emp.user?.name || '—';
};

const loadLeaveTypes = async () => {
  const response = await axios.get('/leave-types', { params: { all: 1 } });
  leaveTypes.value = Array.isArray(response.data) ? response.data : (response.data.data || []);
};

const loadBalances = async (page = 1) => {
  loading.value = true;
  error.value = null;
  try {
    const params = {
      page,
      per_page: 20,
      year: balanceFilters.value.year || new Date().getFullYear(),
    };
    if (balanceFilters.value.leave_type_id) {
      params.leave_type_id = balanceFilters.value.leave_type_id;
    }
    const response = await axios.get('/leave-balances', { params });
    const data = response.data;
    balances.value = data.data || [];
    balanceMeta.value = {
      current_page: data.current_page || 1,
      last_page: data.last_page || 1,
    };
  } catch (err) {
    error.value = 'Failed to load leave balances';
  } finally {
    loading.value = false;
  }
};

const loadEmployees = async () => {
  try {
    const response = await axios.get('/employees/dropdown');
    employees.value = Array.isArray(response.data) ? response.data : (response.data.data || []);
  } catch {
    try {
      const response = await axios.get('/employees/all');
      employees.value = Array.isArray(response.data) ? response.data : (response.data.data || []);
    } catch {
      employees.value = [];
    }
  }
};

const reload = async () => {
  if (activeTab.value === 'types') {
    loading.value = true;
    error.value = null;
    try {
      await loadLeaveTypes();
    } catch {
      error.value = 'Failed to load leave types';
    } finally {
      loading.value = false;
    }
  } else {
    await loadBalances();
  }
};

const switchTab = async (tabId) => {
  activeTab.value = tabId;
  await reload();
};

const openTypeModal = (lt = null) => {
  editingType.value = lt;
  formError.value = null;
  if (lt) {
    typeForm.value = {
      name: lt.name || '',
      description: lt.description || '',
      days_per_year: lt.days_per_year ?? 0,
      is_paid: !!lt.is_paid,
      is_carry_forward: !!lt.is_carry_forward,
      max_carry_forward_days: lt.max_carry_forward_days ?? 0,
      requires_document: !!lt.requires_document,
      is_active: lt.is_active !== false,
    };
  } else {
    typeForm.value = emptyTypeForm();
  }
  showTypeModal.value = true;
};

const saveType = async () => {
  formError.value = null;
  if (!typeForm.value.name.trim()) {
    formError.value = 'Name is required';
    return;
  }
  if (typeForm.value.days_per_year == null || typeForm.value.days_per_year < 0) {
    formError.value = 'Days per year is required';
    return;
  }
  saving.value = true;
  try {
    const payload = { ...typeForm.value };
    if (!payload.is_carry_forward) {
      payload.max_carry_forward_days = 0;
    }
    if (editingType.value) {
      await axios.put(`/leave-types/${editingType.value.id}`, payload);
    } else {
      await axios.post('/leave-types', payload);
    }
    showTypeModal.value = false;
    await loadLeaveTypes();
  } catch (err) {
    formError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {}).flat().join(' ') || 'Failed to save leave type';
  } finally {
    saving.value = false;
  }
};

const openDeleteModal = (lt) => {
  deletingType.value = lt;
  formError.value = null;
  showDeleteModal.value = true;
};

const deleteType = async () => {
  deleting.value = true;
  formError.value = null;
  try {
    await axios.delete(`/leave-types/${deletingType.value.id}`);
    showDeleteModal.value = false;
    await loadLeaveTypes();
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to delete leave type';
  } finally {
    deleting.value = false;
  }
};

const openBalanceModal = async () => {
  formError.value = null;
  balanceForm.value = {
    employee_id: '',
    leave_type_id: '',
    year: balanceFilters.value.year || new Date().getFullYear(),
    total_days: 0,
    used_days: 0,
  };
  if (!employees.value.length) {
    await loadEmployees();
  }
  if (!leaveTypes.value.length) {
    await loadLeaveTypes();
  }
  showBalanceModal.value = true;
};

const saveBalance = async () => {
  formError.value = null;
  const f = balanceForm.value;
  if (!f.employee_id) { formError.value = 'Employee is required'; return; }
  if (!f.leave_type_id) { formError.value = 'Leave type is required'; return; }
  if (!f.year) { formError.value = 'Year is required'; return; }
  if (f.total_days == null || f.total_days < 0) { formError.value = 'Total days is required'; return; }
  saving.value = true;
  try {
    await axios.post('/leave-balances', {
      employee_id: f.employee_id,
      leave_type_id: f.leave_type_id,
      year: f.year,
      total_days: f.total_days,
      used_days: f.used_days || 0,
    });
    showBalanceModal.value = false;
    await loadBalances();
  } catch (err) {
    formError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {}).flat().join(' ') || 'Failed to allocate balance';
  } finally {
    saving.value = false;
  }
};

onMounted(async () => {
  loading.value = true;
  error.value = null;
  try {
    await loadLeaveTypes();
  } catch {
    error.value = 'Failed to load leave types';
  } finally {
    loading.value = false;
  }
});
</script>
