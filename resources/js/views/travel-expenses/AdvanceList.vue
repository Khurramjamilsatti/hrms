<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Advance Requests</h1>
      <button @click="openCreateModal" class="inline-flex items-center px-5 py-2.5 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors shadow">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Request Advance
      </button>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <div v-else-if="advances.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Advance Requests</h3>
      <p class="text-gray-500">Click "Request Advance" to submit one.</p>
    </div>

    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Request #</th>
            <th v-if="needsEmployeePicker" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Employee</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Purpose</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Required Date</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="advance in advances" :key="advance.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ advance.request_number }}</td>
            <td v-if="needsEmployeePicker" class="px-6 py-4 text-sm text-gray-700">
              {{ advance.employee?.first_name }} {{ advance.employee?.last_name }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ advance.purpose }}</td>
            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ formatCurrency(advance.amount) }}</td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs rounded-full font-semibold" :class="getStatusClass(advance.status)">{{ advance.status }}</span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ formatDate(advance.required_date) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Request Modal -->
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">Request Advance</h3>
          <button @click="showForm = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        <form @submit.prevent="submitAdvance">
          <div class="px-6 py-5 space-y-4">
            <div v-if="needsEmployeePicker">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Employee *</label>
              <SearchableSelect
                v-model="form.employee_id"
                :options="employeeOptions"
                placeholder="Select employee..."
                search-placeholder="Search by name or code..."
              />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Purpose</label>
              <textarea v-model="form.purpose" rows="3" required placeholder="Purpose of advance..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Amount (PKR)</label>
              <input v-model="form.amount" type="number" step="0.01" min="0" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Required Date</label>
              <input v-model="form.required_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
          </div>
          <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
            <button type="button" @click="showForm = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
            <button type="submit" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">{{ saving ? 'Submitting...' : 'Submit' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { useEmployeeRecordPicker } from '@/composables/useEmployeeRecordPicker';

const authStore = useAuthStore();
const {
  showEmployeePicker: needsEmployeePicker,
  applyOwnEmployeeToForm,
  validateEmployeeForSubmit,
} = useEmployeeRecordPicker('travel');
const advances = ref([]);
const employees = ref([]);
const loading = ref(false);
const showForm = ref(false);
const saving = ref(false);
const formError = ref(null);

const employeeOptions = computed(() =>
  employees.value.map((e) => ({
    value: e.id,
    label: `${e.first_name || ''} ${e.last_name || ''}`.trim() + (e.employee_code ? ` (${e.employee_code})` : ''),
  }))
);

const form = reactive({
  employee_id: null,
  purpose: '',
  amount: '',
  required_date: '',
});

const fetchAdvances = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/travel-expenses/advance-requests');
    advances.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch advances:', error);
  } finally {
    loading.value = false;
  }
};

const fetchEmployees = async () => {
  if (!needsEmployeePicker.value) return;
  try {
    const response = await axios.get('/employees', { params: { per_page: 200, employment_status: 'active' } });
    employees.value = response.data.data || response.data || [];
  } catch (error) {
    console.error('Failed to fetch employees:', error);
  }
};

const openCreateModal = () => {
  formError.value = null;
  Object.assign(form, {
    employee_id: null,
    purpose: '',
    amount: '',
    required_date: '',
  });
  applyOwnEmployeeToForm(form);
  showForm.value = true;
};

const submitAdvance = async () => {
  formError.value = null;
  const employeeCheck = validateEmployeeForSubmit(form);
  if (!employeeCheck.valid) {
    formError.value = employeeCheck.message;
    return;
  }
  saving.value = true;
  try {
    const payload = {
      purpose: form.purpose,
      amount: form.amount,
      required_date: form.required_date,
    };
    if (needsEmployeePicker.value) {
      payload.employee_id = employeeCheck.employeeId;
    }
    await axios.post('/travel-expenses/advance-requests', payload);
    showForm.value = false;
    fetchAdvances();
  } catch (error) {
    formError.value = error.response?.data?.message || 'Failed to submit advance request';
  } finally {
    saving.value = false;
  }
};

const formatDate = (date) => date ? new Date(date).toLocaleDateString('en-PK') : '—';
const formatCurrency = (amount) => `Rs. ${parseFloat(amount || 0).toLocaleString('en-PK')}`;
const getStatusClass = (status) => ({
  pending: 'bg-yellow-100 text-yellow-800',
  approved: 'bg-green-100 text-green-800',
  rejected: 'bg-red-100 text-red-800',
  paid: 'bg-blue-100 text-blue-800',
  settled: 'bg-gray-100 text-gray-800',
}[status] || 'bg-gray-100 text-gray-800');

onMounted(async () => {
  await Promise.all([fetchAdvances(), fetchEmployees()]);
});
</script>
