<template>
  <div class="p-6">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Employee Deployments</h1>
      <p class="text-gray-600 mt-1">Manage employee deployments and assignments</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Total Deployments</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
          </div>
          <svg class="w-12 h-12 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
          </svg>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Active</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.active }}</p>
          </div>
          <svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
          </svg>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">Pending Approval</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.pending }}</p>
          </div>
          <svg class="w-12 h-12 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
          </svg>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600">From Long Leave</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.fromLongLeave }}</p>
          </div>
          <svg class="w-12 h-12 text-purple-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="mb-6 flex justify-between items-center">
      <div class="flex gap-4">
        <select v-model="filters.status" @change="fetchDeployments" class="px-4 py-2 border border-gray-300 rounded-lg">
          <option value="">All Status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="active">Active</option>
          <option value="completed">Completed</option>
          <option value="extended">Extended</option>
        </select>
        
        <select v-model="filters.deployment_type" @change="fetchDeployments" class="px-4 py-2 border border-gray-300 rounded-lg">
          <option value="">All Types</option>
          <option value="domestic">Domestic</option>
          <option value="international">International</option>
          <option value="project">Project</option>
          <option value="temporary">Temporary</option>
          <option value="permanent">Permanent</option>
        </select>
        
        <label class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
          <input v-model="filters.departure_from_long_leave" @change="fetchDeployments" type="checkbox" class="rounded" />
          <span class="text-sm text-gray-700">From Long Leave</span>
        </label>
      </div>
      
      <button @click="showCreateModal = true" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Create Deployment
      </button>
    </div>

    <!-- Deployments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deployment #</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="deployment in deployments" :key="deployment.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ deployment.deployment_number }}
              <span v-if="deployment.departure_from_long_leave" class="ml-2 px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">Long Leave</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ deployment.employee?.first_name }} {{ deployment.employee?.last_name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">{{ deployment.deployment_type }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">
              <div>{{ deployment.location }}</div>
              <div class="text-xs text-gray-500">{{ deployment.city }}, {{ deployment.country }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              <div>{{ formatDate(deployment.start_date) }}</div>
              <div class="text-xs text-gray-500">to {{ formatDate(deployment.end_date) }}</div>
              <span v-if="deployment.extension_count > 0" class="text-xs text-orange-600">+{{ deployment.extension_count }} ext</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusClass(deployment.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ deployment.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
              <button @click="viewDetails(deployment.id)" class="text-blue-600 hover:text-blue-900">View</button>
              <button v-if="deployment.status === 'pending'" @click="approveDeployment(deployment)" class="text-green-600 hover:text-green-900">Approve</button>
              <button v-if="deployment.status === 'approved'" @click="activateDeployment(deployment)" class="text-blue-600 hover:text-blue-900">Activate</button>
              <button v-if="deployment.status === 'active'" @click="showExtendModal(deployment)" class="text-orange-600 hover:text-orange-900">Extend</button>
              <button v-if="deployment.status === 'active'" @click="completeDeployment(deployment)" class="text-purple-600 hover:text-purple-900">Complete</button>
              <button @click="viewHistory(deployment)" class="text-gray-600 hover:text-gray-900">History</button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-if="deployments.length === 0" class="text-center py-12">
        <p class="text-gray-500">No deployments found</p>
      </div>
    </div>
    
    <!-- Pagination -->
    <Pagination
      v-if="deployments.length > 0"
      :current-page="pagination.current_page"
      :total-pages="pagination.last_page"
      :total="pagination.total"
      :from="pagination.from"
      :to="pagination.to"
      @page-change="handlePageChange"
    />

    <!-- Create Deployment Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showCreateModal = false">
      <div class="relative top-10 mx-auto p-8 border w-full max-w-4xl shadow-lg rounded-lg bg-white">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Deployment</h3>
        <form @submit.prevent="submitDeployment" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Employee*</label>
              <select v-model="deploymentForm.employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Employee</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                  {{ emp.first_name }} {{ emp.last_name }}
                </option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Deployment Type*</label>
              <select v-model="deploymentForm.deployment_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="domestic">Domestic</option>
                <option value="international">International</option>
                <option value="project">Project</option>
                <option value="temporary">Temporary</option>
                <option value="permanent">Permanent</option>
              </select>
            </div>
          </div>
          
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Country*</label>
              <input v-model="deploymentForm.country" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">City*</label>
              <input v-model="deploymentForm.city" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Location*</label>
              <input v-model="deploymentForm.location" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Start Date*</label>
              <input v-model="deploymentForm.start_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">End Date*</label>
              <input v-model="deploymentForm.end_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
            </div>
          </div>
          
          <div class="flex items-center gap-2 p-4 bg-purple-50 rounded-lg">
            <input v-model="deploymentForm.departure_from_long_leave" type="checkbox" class="rounded" />
            <label class="text-sm font-medium text-gray-700">Departure from Long Leave</label>
          </div>
          
          <div v-if="deploymentForm.departure_from_long_leave" class="grid grid-cols-2 gap-4 p-4 bg-purple-50 rounded-lg">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Long Leave Start Date</label>
              <input v-model="deploymentForm.long_leave_start_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Long Leave End Date</label>
              <input v-model="deploymentForm.long_leave_end_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
            <input v-model="deploymentForm.project_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Allowance Amount</label>
              <input v-model="deploymentForm.allowance_amount" type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Allowance Currency</label>
              <input v-model="deploymentForm.allowance_currency" placeholder="PKR, USD, AED" class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
            </div>
          </div>
          
          <div class="flex justify-end gap-4 mt-6">
            <button type="button" @click="showCreateModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create Deployment</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Extend Modal -->
    <div v-if="showExtensionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showExtensionModal = false">
      <div class="relative top-20 mx-auto p-8 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Request Deployment Extension</h3>
        <form @submit.prevent="submitExtension" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New End Date*</label>
            <input v-model="extensionForm.new_end_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Extension*</label>
            <textarea v-model="extensionForm.reason" required rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
            <textarea v-model="extensionForm.remarks" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
          </div>
          
          <div class="flex justify-end gap-4 mt-6">
            <button type="button" @click="showExtensionModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Request Extension</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Pagination from '@/components/Pagination.vue';

const router = useRouter();
const deployments = ref([]);
const pagination = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0 });
const employees = ref([]);
const stats = ref({ total: 0, active: 0, pending: 0, fromLongLeave: 0 });
const filters = ref({ status: '', deployment_type: '', departure_from_long_leave: false });
const showCreateModal = ref(false);
const showExtensionModal = ref(false);
const selectedDeployment = ref(null);

const deploymentForm = ref({
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
  allowance_currency: 'PKR'
});

const extensionForm = ref({
  new_end_date: '',
  reason: '',
  remarks: ''
});

const fetchDeployments = async (page = 1) => {
  try {
    const params = new URLSearchParams();
    params.append('page', page);
    if (filters.value.status) params.append('status', filters.value.status);
    if (filters.value.deployment_type) params.append('deployment_type', filters.value.deployment_type);
    if (filters.value.departure_from_long_leave) params.append('departure_from_long_leave', 'true');
    
    const response = await axios.get(`/api/deployments?${params}`);
    deployments.value = response.data.data || response.data;
    
    // Update pagination data
    if (response.data.current_page) {
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        total: response.data.total,
        from: response.data.from || 0,
        to: response.data.to || 0
      };
    }
    
    // Calculate stats
    stats.value.total = pagination.value.total || deployments.value.length;
    stats.value.active = deployments.value.filter(d => d.status === 'active').length;
    stats.value.pending = deployments.value.filter(d => d.status === 'pending').length;
    stats.value.fromLongLeave = deployments.value.filter(d => d.departure_from_long_leave).length;
  } catch (error) {
    console.error('Failed to fetch deployments:', error);
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
    const response = await axios.get('/api/employees/dropdown');
    employees.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch employees:', error);
  }
};

const submitDeployment = async () => {
  try {
    await axios.post('/api/deployments', deploymentForm.value);
    showCreateModal.value = false;
    deploymentForm.value = {
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
      allowance_currency: 'PKR'
    };
    fetchDeployments();
    alert('Deployment created successfully!');
  } catch (error) {
    console.error('Failed to create deployment:', error);
    alert('Failed to create deployment');
  }
};

const approveDeployment = async (deployment) => {
  if (!confirm(`Approve deployment ${deployment.deployment_number}?`)) return;
  try {
    await axios.post(`/api/deployments/${deployment.id}/approve`, { remarks: 'Approved' });
    fetchDeployments();
    alert('Deployment approved successfully!');
  } catch (error) {
    console.error('Failed to approve deployment:', error);
    alert('Failed to approve deployment');
  }
};

const activateDeployment = async (deployment) => {
  if (!confirm(`Activate deployment ${deployment.deployment_number}?`)) return;
  try {
    await axios.post(`/api/deployments/${deployment.id}/activate`, { remarks: 'Employee deployed' });
    fetchDeployments();
    alert('Deployment activated successfully!');
  } catch (error) {
    console.error('Failed to activate deployment:', error);
    alert('Failed to activate deployment');
  }
};

const completeDeployment = async (deployment) => {
  if (!confirm(`Complete deployment ${deployment.deployment_number}?`)) return;
  try {
    await axios.post(`/api/deployments/${deployment.id}/complete`, {
      actual_end_date: new Date().toISOString().split('T')[0],
      remarks: 'Deployment completed'
    });
    fetchDeployments();
    alert('Deployment completed successfully!');
  } catch (error) {
    console.error('Failed to complete deployment:', error);
    alert('Failed to complete deployment');
  }
};

const showExtendModal = (deployment) => {
  selectedDeployment.value = deployment;
  extensionForm.value.new_end_date = '';
  showExtensionModal.value = true;
};

const submitExtension = async () => {
  try {
    await axios.post(`/api/deployments/${selectedDeployment.value.id}/extend`, extensionForm.value);
    showExtensionModal.value = false;
    extensionForm.value = { new_end_date: '', reason: '', remarks: '' };
    fetchDeployments();
    alert('Extension request submitted successfully!');
  } catch (error) {
    console.error('Failed to submit extension:', error);
    alert('Failed to submit extension');
  }
};

const viewHistory = async (deployment) => {
  try {
    const response = await axios.get(`/api/deployments/employees/${deployment.employee_id}/history`);
    const history = response.data.data || response.data;
    alert(`Employee has ${history.length} deployment(s) in history`);
    // You can create a history modal here similar to CV history
  } catch (error) {
    console.error('Failed to fetch deployment history:', error);
    alert('Failed to fetch deployment history');
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-PK', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-blue-100 text-blue-800',
    active: 'bg-green-100 text-green-800',
    completed: 'bg-purple-100 text-purple-800',
    extended: 'bg-orange-100 text-orange-800',
    cancelled: 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  fetchDeployments();
  fetchEmployees();
});
</script>
