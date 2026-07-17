<template>
  <div class="p-6">
    <div v-if="loading" class="flex items-center justify-center py-24">
      <div class="text-center">
        <div class="mb-3 inline-block h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-gray-900" />
        <p class="text-sm text-gray-600">Loading deployment…</p>
      </div>
    </div>

    <div v-else-if="error" class="rounded-lg border border-red-200 bg-red-50 px-5 py-4 text-red-700">
      {{ error }}
      <button type="button" class="ml-2 text-sm underline" @click="fetchDeploymentDetails">Try again</button>
    </div>

    <template v-else>
      <!-- Header -->
      <div class="mb-6">
        <router-link
          to="/deployments"
          class="mb-4 inline-flex items-center text-sm font-medium text-gray-600 hover:text-accent"
        >
          <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Back to Deployments
        </router-link>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
          <div>
            <div class="flex flex-wrap items-center gap-2">
              <h1 class="text-3xl font-bold text-gray-900">{{ deployment.deployment_number || 'Deployment' }}</h1>
              <span
                v-if="deployment.departure_from_long_leave"
                class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700"
              >
                Long Leave
              </span>
            </div>
            <p class="mt-1 text-sm text-gray-500">
              {{ employeeLabel }}
              <span v-if="deployment.deployment_type"> · <span class="capitalize">{{ deployment.deployment_type }}</span></span>
              <span v-if="deployment.location"> · {{ deployment.location }}</span>
            </p>
          </div>
          <span
            class="inline-flex self-start rounded-full px-3 py-1.5 text-xs font-semibold uppercase tracking-wide capitalize"
            :class="getStatusClass(deployment.status)"
          >
            {{ deployment.status || '—' }}
          </span>
        </div>
      </div>

      <!-- Summary strip -->
      <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Start Date</p>
          <p class="mt-2 text-lg font-bold text-gray-900">{{ formatDate(deployment.start_date) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">End Date</p>
          <p class="mt-2 text-lg font-bold text-gray-900">{{ formatDate(deployment.end_date) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Location</p>
          <p class="mt-2 text-lg font-bold text-gray-900">{{ deployment.city || deployment.location || '—' }}</p>
          <p class="mt-0.5 text-xs text-gray-500">{{ deployment.country || '' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Allowance</p>
          <p class="mt-2 text-lg font-bold text-gray-900">{{ formatCurrency(deployment.allowance_amount, deployment.allowance_currency) }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
          <!-- Deployment info -->
          <div class="rounded-lg border border-gray-200 bg-white p-6 shadow">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Deployment Information</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div class="field-block">
                <p class="field-label">Type</p>
                <p class="field-value capitalize">{{ deployment.deployment_type || '—' }}</p>
              </div>
              <div class="field-block">
                <p class="field-label">Location</p>
                <p class="field-value">{{ deployment.location || '—' }}</p>
              </div>
              <div class="field-block">
                <p class="field-label">City</p>
                <p class="field-value">{{ deployment.city || '—' }}</p>
              </div>
              <div class="field-block">
                <p class="field-label">Country</p>
                <p class="field-value">{{ deployment.country || '—' }}</p>
              </div>
              <div class="field-block">
                <p class="field-label">Start Date</p>
                <p class="field-value">{{ formatDate(deployment.start_date) }}</p>
              </div>
              <div class="field-block">
                <p class="field-label">End Date</p>
                <p class="field-value">{{ formatDate(deployment.end_date) }}</p>
              </div>
              <div v-if="deployment.expected_return_date" class="field-block">
                <p class="field-label">Expected Return</p>
                <p class="field-value">{{ formatDate(deployment.expected_return_date) }}</p>
              </div>
              <div v-if="deployment.actual_return_date" class="field-block">
                <p class="field-label">Actual Return</p>
                <p class="field-value">{{ formatDate(deployment.actual_return_date) }}</p>
              </div>
              <div v-if="deployment.allowance_amount" class="field-block">
                <p class="field-label">Allowance</p>
                <p class="field-value">{{ formatCurrency(deployment.allowance_amount, deployment.allowance_currency) }}</p>
              </div>
              <div v-if="deployment.extension_count > 0" class="field-block">
                <p class="field-label">Extensions</p>
                <p class="field-value">{{ deployment.extension_count }}</p>
              </div>
            </div>

            <div v-if="deployment.extension_count > 0" class="mt-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">
              <p class="text-sm font-semibold text-amber-900">
                Extended {{ deployment.extension_count }} {{ deployment.extension_count === 1 ? 'time' : 'times' }}
              </p>
              <p v-if="deployment.current_extension_end_date" class="mt-1 text-sm text-amber-800">
                Current extension ends {{ formatDate(deployment.current_extension_end_date) }}
              </p>
            </div>
          </div>

          <!-- Project -->
          <div v-if="deployment.project_name || deployment.client_name || deployment.role || deployment.purpose" class="rounded-lg border border-gray-200 bg-white p-6 shadow">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Project Details</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div v-if="deployment.project_name" class="field-block">
                <p class="field-label">Project Name</p>
                <p class="field-value">{{ deployment.project_name }}</p>
              </div>
              <div v-if="deployment.client_name" class="field-block">
                <p class="field-label">Client</p>
                <p class="field-value">{{ deployment.client_name }}</p>
              </div>
              <div v-if="deployment.role" class="field-block">
                <p class="field-label">Role</p>
                <p class="field-value">{{ deployment.role }}</p>
              </div>
              <div v-if="deployment.reporting_manager" class="field-block">
                <p class="field-label">Reporting Manager</p>
                <p class="field-value">{{ deployment.reporting_manager }}</p>
              </div>
              <div v-if="deployment.purpose" class="field-block sm:col-span-2">
                <p class="field-label">Purpose</p>
                <p class="field-value whitespace-pre-line">{{ deployment.purpose }}</p>
              </div>
            </div>
          </div>

          <!-- Logistics -->
          <div v-if="hasLogisticsInfo" class="rounded-lg border border-gray-200 bg-white p-6 shadow">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Logistics & Status</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
              <div v-if="deployment.visa_status" class="field-block">
                <p class="field-label">Visa Status</p>
                <span class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize" :class="getVisaStatusClass(deployment.visa_status)">
                  {{ formatStatusLabel(deployment.visa_status) }}
                </span>
              </div>
              <div v-if="deployment.insurance_status" class="field-block">
                <p class="field-label">Insurance Status</p>
                <span class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize" :class="getInsuranceStatusClass(deployment.insurance_status)">
                  {{ formatStatusLabel(deployment.insurance_status) }}
                </span>
              </div>
              <div v-if="deployment.travel_ticket_status" class="field-block">
                <p class="field-label">Travel Ticket</p>
                <span class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize" :class="getTicketStatusClass(deployment.travel_ticket_status)">
                  {{ formatStatusLabel(deployment.travel_ticket_status) }}
                </span>
              </div>
              <div v-if="deployment.accommodation_type" class="field-block">
                <p class="field-label">Accommodation Type</p>
                <p class="field-value">{{ deployment.accommodation_type }}</p>
              </div>
              <div v-if="deployment.accommodation_details" class="field-block sm:col-span-2">
                <p class="field-label">Accommodation Details</p>
                <p class="field-value whitespace-pre-line">{{ deployment.accommodation_details }}</p>
              </div>
              <div v-if="deployment.transport_details" class="field-block sm:col-span-2">
                <p class="field-label">Transport Details</p>
                <p class="field-value whitespace-pre-line">{{ deployment.transport_details }}</p>
              </div>
            </div>
          </div>

          <!-- Extensions -->
          <div v-if="deployment.extensions?.length" class="rounded-lg border border-gray-200 bg-white p-6 shadow">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Extension History</h2>
            <div class="space-y-3">
              <div
                v-for="extension in deployment.extensions"
                :key="extension.id"
                class="rounded-lg border border-gray-200 bg-gray-50 p-4"
              >
                <div class="flex flex-wrap items-start justify-between gap-3">
                  <div>
                    <p class="text-sm font-semibold text-gray-900">Extension #{{ extension.extension_number }}</p>
                    <p class="mt-1 text-sm text-gray-600">
                      {{ formatDate(extension.previous_end_date) }} → {{ formatDate(extension.new_end_date) }}
                    </p>
                    <p v-if="extension.reason" class="mt-2 text-sm text-gray-700">{{ extension.reason }}</p>
                    <p v-if="extension.approved_by" class="mt-2 text-xs text-gray-500">
                      Approved by {{ extension.approver?.name || '—' }} on {{ formatDate(extension.approved_at) }}
                    </p>
                  </div>
                  <span
                    class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                    :class="getExtensionStatusClass(extension.status)"
                  >
                    {{ extension.status }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <div class="rounded-lg border border-gray-200 bg-white p-6 shadow">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Employee</h2>
            <div class="space-y-4">
              <div class="field-block">
                <p class="field-label">Name</p>
                <p class="field-value">{{ employeeLabel }}</p>
              </div>
              <div class="field-block">
                <p class="field-label">Email</p>
                <p class="field-value">{{ deployment.employee?.user?.email || deployment.employee?.email || '—' }}</p>
              </div>
              <div class="field-block">
                <p class="field-label">Employee Code</p>
                <p class="field-value">{{ deployment.employee?.employee_code || deployment.employee?.employee_id || '—' }}</p>
              </div>
              <div class="field-block">
                <p class="field-label">Department</p>
                <p class="field-value">{{ deployment.employee?.department?.name || '—' }}</p>
              </div>
              <div v-if="deployment.employee?.designation?.title" class="field-block">
                <p class="field-label">Designation</p>
                <p class="field-value">{{ deployment.employee.designation.title }}</p>
              </div>
            </div>
            <router-link
              v-if="deployment.employee_id || deployment.employee?.id"
              :to="`/employees/${deployment.employee_id || deployment.employee?.id}`"
              class="mt-5 inline-flex text-sm font-semibold text-accent hover:text-accent-dark"
            >
              View employee profile →
            </router-link>
          </div>

          <div v-if="deployment.departure_from_long_leave" class="rounded-lg border border-gray-200 bg-white p-6 shadow">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Long Leave</h2>
            <div class="space-y-4">
              <div class="field-block">
                <p class="field-label">Leave Start</p>
                <p class="field-value">{{ formatDate(deployment.long_leave_start_date) }}</p>
              </div>
              <div class="field-block">
                <p class="field-label">Leave End</p>
                <p class="field-value">{{ formatDate(deployment.long_leave_end_date) }}</p>
              </div>
            </div>
            <p class="mt-4 text-sm text-gray-500">Employee was on long leave before this deployment.</p>
          </div>

          <div class="rounded-lg border border-gray-200 bg-white p-6 shadow">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Record Info</h2>
            <div class="space-y-4">
              <div v-if="deployment.approved_by" class="field-block">
                <p class="field-label">Approved By</p>
                <p class="field-value">{{ deployment.approver?.name || '—' }}</p>
              </div>
              <div v-if="deployment.approved_at" class="field-block">
                <p class="field-label">Approved On</p>
                <p class="field-value">{{ formatDate(deployment.approved_at) }}</p>
              </div>
              <div class="field-block">
                <p class="field-label">Created By</p>
                <p class="field-value">{{ deployment.creator?.name || '—' }}</p>
              </div>
              <div class="field-block">
                <p class="field-label">Created On</p>
                <p class="field-value">{{ formatDate(deployment.created_at) }}</p>
              </div>
              <div v-if="deployment.notes" class="field-block">
                <p class="field-label">Notes</p>
                <p class="field-value whitespace-pre-line">{{ deployment.notes }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const loading = ref(true);
const error = ref('');
const deployment = ref({});

const employeeLabel = computed(() => {
  const emp = deployment.value.employee;
  if (!emp) return '—';
  return (
    emp.full_name
    || emp.user?.name
    || `${emp.first_name || ''} ${emp.last_name || ''}`.trim()
    || '—'
  );
});

const hasLogisticsInfo = computed(() =>
  !!(
    deployment.value.visa_status
    || deployment.value.insurance_status
    || deployment.value.travel_ticket_status
    || deployment.value.accommodation_type
    || deployment.value.accommodation_details
    || deployment.value.transport_details
  )
);

const fetchDeploymentDetails = async () => {
  loading.value = true;
  error.value = '';
  try {
    const response = await axios.get(`/deployments/${route.params.id}`);
    deployment.value = response.data.data || response.data;
  } catch (err) {
    console.error('Error fetching deployment details:', err);
    error.value = err.response?.data?.message || 'Failed to load deployment details';
  } finally {
    loading.value = false;
  }
};

const formatCurrency = (value, currency = 'PKR') => {
  if (value == null || value === '') return '—';
  const code = currency || 'PKR';
  try {
    return new Intl.NumberFormat(undefined, {
      style: 'currency',
      currency: code,
      maximumFractionDigits: 0,
    }).format(Number(value));
  } catch (_) {
    return `${code} ${Number(value).toLocaleString()}`;
  }
};

const formatDate = (date) => {
  if (!date) return '—';
  return new Date(date).toLocaleDateString('en-PK', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const formatStatusLabel = (status) => String(status || '').replaceAll('_', ' ');

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-800',
    pending: 'bg-amber-100 text-amber-800',
    approved: 'bg-blue-100 text-blue-800',
    rejected: 'bg-red-100 text-red-800',
    active: 'bg-emerald-100 text-emerald-800',
    completed: 'bg-gray-100 text-gray-800',
    cancelled: 'bg-gray-100 text-gray-800',
    extended: 'bg-amber-100 text-amber-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getExtensionStatusClass = (status) => {
  const classes = {
    pending: 'bg-amber-100 text-amber-800',
    approved: 'bg-emerald-100 text-emerald-800',
    rejected: 'bg-red-100 text-red-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getVisaStatusClass = (status) => {
  const classes = {
    not_required: 'bg-gray-100 text-gray-800',
    pending: 'bg-amber-100 text-amber-800',
    in_process: 'bg-blue-100 text-blue-800',
    approved: 'bg-emerald-100 text-emerald-800',
    rejected: 'bg-red-100 text-red-800',
    issued: 'bg-emerald-100 text-emerald-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getInsuranceStatusClass = (status) => {
  const classes = {
    pending: 'bg-amber-100 text-amber-800',
    active: 'bg-emerald-100 text-emerald-800',
    expired: 'bg-red-100 text-red-800',
    not_required: 'bg-gray-100 text-gray-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getTicketStatusClass = (status) => {
  const classes = {
    pending: 'bg-amber-100 text-amber-800',
    booked: 'bg-blue-100 text-blue-800',
    issued: 'bg-emerald-100 text-emerald-800',
    used: 'bg-gray-100 text-gray-800',
    cancelled: 'bg-red-100 text-red-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

onMounted(fetchDeploymentDetails);
</script>

<style scoped>
.field-block {
  @apply min-w-0;
}
.field-label {
  @apply text-xs font-semibold uppercase tracking-wide text-gray-500;
}
.field-value {
  @apply mt-1 text-sm font-medium text-gray-900;
}
</style>
