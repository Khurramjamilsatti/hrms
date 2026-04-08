<template>
  <div class="p-6">
    <!-- Header -->
    <div class="mb-6">
      <router-link
        to="/loans"
        class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mb-4 font-medium transition-colors"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Loans
      </router-link>
      
      <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ loan.loan_number }}</h1>
          <div class="flex items-center gap-4 text-gray-600">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
              </svg>
              <span>{{ employeeName }}</span>
            </div>
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
              </svg>
              <span class="capitalize">{{ loan.loan_type }} Loan</span>
            </div>
          </div>
        </div>
        <span
          :class="['px-6 py-2 rounded-full text-sm font-semibold shadow-sm', getStatusClass(loan.status)]"
        >
          {{ loan.status?.toUpperCase() }}
        </span>
      </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm opacity-90">Loan Amount</p>
          <svg class="w-8 h-8 opacity-80" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/>
          </svg>
        </div>
        <p class="text-3xl font-bold">{{ formatCurrency(loan.amount) }}</p>
        <p class="text-sm opacity-90 mt-1">{{ loan.interest_rate }}% interest</p>
      </div>
      
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm opacity-90">Amount Paid</p>
          <svg class="w-8 h-8 opacity-80" fill="currentColor" viewBox="0 0 24 24">
            <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
          </svg>
        </div>
        <p class="text-3xl font-bold">{{ formatCurrency(loan.total_paid) }}</p>
        <p class="text-sm opacity-90 mt-1">{{ loan.payments?.length || 0 }} payments made</p>
      </div>
      
      <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm opacity-90">Balance Due</p>
          <svg class="w-8 h-8 opacity-80" fill="currentColor" viewBox="0 0 24 24">
            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
          </svg>
        </div>
        <p class="text-3xl font-bold">{{ formatCurrency(loan.balance_amount) }}</p>
        <p class="text-sm opacity-90 mt-1">{{ loan.installments - (loan.payments?.length || 0) }} installments left</p>
      </div>
      
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between mb-2">
          <p class="text-sm opacity-90">Monthly Installment</p>
          <svg class="w-8 h-8 opacity-80" fill="currentColor" viewBox="0 0 24 24">
            <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
          </svg>
        </div>
        <p class="text-3xl font-bold">{{ formatCurrency(loan.installment_amount) }}</p>
        <p class="text-sm opacity-90 mt-1">{{ loan.installments }} month term</p>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <!-- Loan Details -->
      <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
          <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
          </svg>
          Loan Information
        </h2>
        
        <div class="grid grid-cols-2 gap-6">
          <div class="col-span-2 md:col-span-1">
            <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Start Date</label>
            <p class="text-gray-900 font-medium">{{ formatDate(loan.start_date) }}</p>
          </div>
          <div class="col-span-2 md:col-span-1">
            <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">End Date</label>
            <p class="text-gray-900 font-medium">{{ formatDate(loan.end_date) }}</p>
          </div>
          <div class="col-span-2">
            <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Purpose</label>
            <p class="text-gray-900 bg-gray-50 p-4 rounded-lg border border-gray-200">{{ loan.purpose || 'N/A' }}</p>
          </div>
          <div v-if="loan.remarks" class="col-span-2">
            <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Remarks</label>
            <p class="text-gray-900 bg-blue-50 p-4 rounded-lg border border-blue-200">{{ loan.remarks }}</p>
          </div>
        </div>
      </div>

      <!-- Employee Card -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
          <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
          </svg>
          Employee
        </h2>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Name</label>
            <p class="text-gray-900 font-medium">{{ employeeName }}</p>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Email</label>
            <p class="text-gray-900">{{ loan.employee?.user?.email }}</p>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Department</label>
            <p class="text-gray-900">{{ loan.employee?.department?.name || 'N/A' }}</p>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Employee Code</label>
            <p class="text-gray-900 font-mono bg-gray-100 px-3 py-1 rounded inline-block">
              {{ loan.employee?.employee_code }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Guarantor Information -->
    <div v-if="loan.guarantor_name" class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
        </svg>
        Guarantor Information
      </h2>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Name</label>
          <p class="text-gray-900 font-medium">{{ loan.guarantor_name }}</p>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Contact</label>
          <p class="text-gray-900">{{ loan.guarantor_contact }}</p>
        </div>
      </div>
    </div>

    <!-- Payment History -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
          <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/>
        </svg>
        Payment History
        <span class="ml-auto text-sm font-normal text-gray-500">{{ loan.payments?.length || 0 }} payments</span>
      </h2>
      
      <div v-if="loan.payments?.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Amount</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Principal</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Interest</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Method</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Processed By</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="payment in loan.payments" :key="payment.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatDate(payment.payment_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                {{ formatCurrency(payment.amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatCurrency(payment.principal_amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatCurrency(payment.interest_amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm capitalize">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                  {{ payment.payment_method?.replace('_', ' ') }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ payment.processor?.name || 'System' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div v-else class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
        <p class="text-gray-500 text-lg font-medium">No payments recorded yet</p>
        <p class="text-gray-400 text-sm mt-1">Payment history will appear here once transactions are recorded</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { useNotification } from '@/composables/useNotification';

const { error: showError } = useNotification();

const route = useRoute();
const loan = ref({});

const employeeName = computed(() => {
  if (!loan.value.employee) return 'N/A';
  if (loan.value.employee.user?.name) return loan.value.employee.user.name;
  const firstName = loan.value.employee.first_name || '';
  const lastName = loan.value.employee.last_name || '';
  return `${firstName} ${lastName}`.trim() || 'N/A';
});

const fetchLoanDetails = async () => {
  try {
    const response = await axios.get(`/loans/${route.params.id}`);
    loan.value = response.data;
  } catch (error) {
    console.error('Error fetching loan details:', error);
    showError(error.response?.data?.message || 'Failed to load loan details');
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
  return new Date(date).toLocaleDateString('en-PK');
};

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-blue-100 text-blue-800',
    rejected: 'bg-red-100 text-red-800',
    disbursed: 'bg-purple-100 text-purple-800',
    active: 'bg-green-100 text-green-800',
    completed: 'bg-gray-100 text-gray-800',
    defaulted: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  fetchLoanDetails();
});
</script>
