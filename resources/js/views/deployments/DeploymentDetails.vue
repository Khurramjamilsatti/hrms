<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
      <router-link
        to="/deployments"
        class="inline-flex items-center text-sm text-gray-600 hover:text-blue-600 mb-4"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Deployments
      </router-link>
      
      <div class="flex justify-between items-start">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">
            {{ deployment.deployment_number }}
            <span
              v-if="deployment.departure_from_long_leave"
              class="text-sm font-semibold px-2 py-1 bg-purple-100 text-purple-800 rounded"
            >
              Long Leave
            </span>
          </h1>
          <p class="text-gray-600 mt-1">
            {{ deployment.employee?.user?.name }} • {{ deployment.deployment_type }}
          </p>
        </div>
        <span
          :class="['px-4 py-2 rounded-full text-sm font-semibold', getStatusClass(deployment.status)]"
        >
          {{ deployment.status?.toUpperCase() }}
        </span>
      </div>
    </div>

    <!-- Deployment Details Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Deployment Information</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-600">Type</label>
          <p class="mt-1 text-gray-900 capitalize">{{ deployment.deployment_type }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Country</label>
          <p class="mt-1 text-gray-900">{{ deployment.country }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">City</label>
          <p class="mt-1 text-gray-900">{{ deployment.city }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Location</label>
          <p class="mt-1 text-gray-900">{{ deployment.location }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Start Date</label>
          <p class="mt-1 text-gray-900">{{ formatDate(deployment.start_date) }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">End Date</label>
          <p class="mt-1 text-gray-900">{{ formatDate(deployment.end_date) }}</p>
        </div>
        <div v-if="deployment.expected_return_date">
          <label class="block text-sm font-medium text-gray-600">Expected Return</label>
          <p class="mt-1 text-gray-900">{{ formatDate(deployment.expected_return_date) }}</p>
        </div>
        <div v-if="deployment.actual_return_date">
          <label class="block text-sm font-medium text-gray-600">Actual Return</label>
          <p class="mt-1 text-gray-900">{{ formatDate(deployment.actual_return_date) }}</p>
        </div>
        <div v-if="deployment.allowance_amount">
          <label class="block text-sm font-medium text-gray-600">Allowance</label>
          <p class="mt-1 text-gray-900 font-semibold">
            {{ formatCurrency(deployment.allowance_amount) }}
          </p>
        </div>
      </div>
      
      <div v-if="deployment.extension_count > 0" class="mt-6 p-4 bg-orange-50 border border-orange-200 rounded-md">
        <p class="text-orange-800 font-semibold">
          This deployment has been extended {{ deployment.extension_count }} {{ deployment.extension_count === 1 ? 'time' : 'times' }}
        </p>
        <p class="text-sm text-orange-700 mt-1">
          Current extension ends: {{ formatDate(deployment.current_extension_end_date) }}
        </p>
      </div>
    </div>

    <!-- Project Details Card -->
    <div v-if="deployment.project_name || deployment.client_name" class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Project Details</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div v-if="deployment.project_name">
          <label class="block text-sm font-medium text-gray-600">Project Name</label>
          <p class="mt-1 text-gray-900">{{ deployment.project_name }}</p>
        </div>
        <div v-if="deployment.client_name">
          <label class="block text-sm font-medium text-gray-600">Client</label>
          <p class="mt-1 text-gray-900">{{ deployment.client_name }}</p>
        </div>
        <div v-if="deployment.role">
          <label class="block text-sm font-medium text-gray-600">Role</label>
          <p class="mt-1 text-gray-900">{{ deployment.role }}</p>
        </div>
        <div v-if="deployment.reporting_manager">
          <label class="block text-sm font-medium text-gray-600">Reporting Manager</label>
          <p class="mt-1 text-gray-900">{{ deployment.reporting_manager }}</p>
        </div>
      </div>
      
      <div v-if="deployment.purpose" class="mt-6">
        <label class="block text-sm font-medium text-gray-600">Purpose</label>
        <p class="mt-1 text-gray-900">{{ deployment.purpose }}</p>
      </div>
    </div>

    <!-- Long Leave Details (if applicable) -->
    <div v-if="deployment.departure_from_long_leave" class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold text-purple-900 mb-4">Long Leave Information</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-purple-600">Leave Start Date</label>
          <p class="mt-1 text-purple-900">{{ formatDate(deployment.long_leave_start_date) }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-purple-600">Leave End Date</label>
          <p class="mt-1 text-purple-900">{{ formatDate(deployment.long_leave_end_date) }}</p>
        </div>
      </div>
      <p class="mt-4 text-sm text-purple-700">
        This employee was on long leave before this deployment
      </p>
    </div>

    <!-- Logistics Details Card -->
    <div v-if="hasLogisticsInfo" class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Logistics & Status</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-if="deployment.visa_status">
          <label class="block text-sm font-medium text-gray-600">Visa Status</label>
          <p class="mt-1">
            <span :class="['px-2 py-1 rounded text-sm font-medium', getVisaStatusClass(deployment.visa_status)]">
              {{ deployment.visa_status?.replace('_', ' ').toUpperCase() }}
            </span>
          </p>
        </div>
        <div v-if="deployment.insurance_status">
          <label class="block text-sm font-medium text-gray-600">Insurance Status</label>
          <p class="mt-1">
            <span :class="['px-2 py-1 rounded text-sm font-medium', getInsuranceStatusClass(deployment.insurance_status)]">
              {{ deployment.insurance_status?.replace('_', ' ').toUpperCase() }}
            </span>
          </p>
        </div>
        <div v-if="deployment.travel_ticket_status">
          <label class="block text-sm font-medium text-gray-600">Travel Ticket Status</label>
          <p class="mt-1">
            <span :class="['px-2 py-1 rounded text-sm font-medium', getTicketStatusClass(deployment.travel_ticket_status)]">
              {{ deployment.travel_ticket_status?.toUpperCase() }}
            </span>
          </p>
        </div>
        <div v-if="deployment.accommodation_type">
          <label class="block text-sm font-medium text-gray-600">Accommodation Type</label>
          <p class="mt-1 text-gray-900">{{ deployment.accommodation_type }}</p>
        </div>
      </div>
      
      <div v-if="deployment.accommodation_details" class="mt-6">
        <label class="block text-sm font-medium text-gray-600">Accommodation Details</label>
        <p class="mt-1 text-gray-900">{{ deployment.accommodation_details }}</p>
      </div>
      
      <div v-if="deployment.transport_details" class="mt-6">
        <label class="block text-sm font-medium text-gray-600">Transport Details</label>
        <p class="mt-1 text-gray-900">{{ deployment.transport_details }}</p>
      </div>
    </div>

    <!-- Employee Details Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Employee Information</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-600">Name</label>
          <p class="mt-1 text-gray-900">{{ deployment.employee?.user?.name }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Email</label>
          <p class="mt-1 text-gray-900">{{ deployment.employee?.user?.email }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Department</label>
          <p class="mt-1 text-gray-900">{{ deployment.employee?.department?.name || 'N/A' }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Employee ID</label>
          <p class="mt-1 text-gray-900">{{ deployment.employee?.employee_id }}</p>
        </div>
      </div>
    </div>

    <!-- Extension History -->
    <div v-if="deployment.extensions?.length > 0" class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Extension History</h2>
      <div class="space-y-4">
        <div
          v-for="extension in deployment.extensions"
          :key="extension.id"
          class="border-l-4 border-orange-400 pl-4 py-2"
        >
          <div class="flex justify-between items-start">
            <div>
              <p class="font-semibold text-gray-900">Extension #{{ extension.extension_number }}</p>
              <p class="text-sm text-gray-600 mt-1">
                {{ formatDate(extension.previous_end_date) }} → {{ formatDate(extension.new_end_date) }}
              </p>
              <p class="text-sm text-gray-700 mt-2">{{ extension.reason }}</p>
            </div>
            <span
              :class="['px-3 py-1 rounded-full text-sm font-semibold', getExtensionStatusClass(extension.status)]"
            >
              {{ extension.status?.toUpperCase() }}
            </span>
          </div>
          <div v-if="extension.approved_by" class="mt-2 text-sm text-gray-500">
            Approved by {{ extension.approver?.name }} on {{ formatDate(extension.approved_at) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Approval Details Card -->
    <div v-if="deployment.approved_by" class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Approval Information</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-600">Approved By</label>
          <p class="mt-1 text-gray-900">{{ deployment.approver?.name || 'N/A' }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Approval Date</label>
          <p class="mt-1 text-gray-900">{{ formatDate(deployment.approved_at) }}</p>
        </div>
        <div v-if="deployment.created_by">
          <label class="block text-sm font-medium text-gray-600">Created By</label>
          <p class="mt-1 text-gray-900">{{ deployment.creator?.name || 'N/A' }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Created Date</label>
          <p class="mt-1 text-gray-900">{{ formatDate(deployment.created_at) }}</p>
        </div>
      </div>
      
      <div v-if="deployment.notes" class="mt-6">
        <label class="block text-sm font-medium text-gray-600">Notes</label>
        <p class="mt-1 text-gray-900">{{ deployment.notes }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const deployment = ref({});

const hasLogisticsInfo = computed(() => {
  return deployment.value.visa_status || 
         deployment.value.insurance_status || 
         deployment.value.travel_ticket_status ||
         deployment.value.accommodation_type ||
         deployment.value.accommodation_details ||
         deployment.value.transport_details;
});

const fetchDeploymentDetails = async () => {
  try {
    const response = await axios.get(`/api/deployments/${route.params.id}`);
    deployment.value = response.data;
  } catch (error) {
    console.error('Error fetching deployment details:', error);
    alert('Failed to load deployment details');
  }
};

const formatCurrency = (value) => {
  if (!value) return 'Rs. 0';
  return new Intl.NumberFormat('en-PK', {
    style: 'currency',
    currency: 'PKR',
    minimumFractionDigits: 0,
  }).format(value).replace('PKR', 'Rs.');
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-PK', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-blue-100 text-blue-800',
    rejected: 'bg-red-100 text-red-800',
    active: 'bg-green-100 text-green-800',
    completed: 'bg-purple-100 text-purple-800',
    cancelled: 'bg-gray-100 text-gray-800',
    extended: 'bg-orange-100 text-orange-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getExtensionStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getVisaStatusClass = (status) => {
  const classes = {
    not_required: 'bg-gray-100 text-gray-800',
    pending: 'bg-yellow-100 text-yellow-800',
    in_process: 'bg-blue-100 text-blue-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    issued: 'bg-purple-100 text-purple-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getInsuranceStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    active: 'bg-green-100 text-green-800',
    expired: 'bg-red-100 text-red-800',
    not_required: 'bg-gray-100 text-gray-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getTicketStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    booked: 'bg-blue-100 text-blue-800',
    issued: 'bg-green-100 text-green-800',
    used: 'bg-purple-100 text-purple-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  fetchDeploymentDetails();
});
</script>
