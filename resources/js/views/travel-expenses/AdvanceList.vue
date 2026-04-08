<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-lg font-semibold">Advance Requests</h3>
      <button @click="showForm = true" class="btn btn-primary">Request Advance</button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Request #</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Required Date</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="advance in advances" :key="advance.id">
            <td class="px-6 py-4 text-sm font-medium">{{ advance.request_number }}</td>
            <td class="px-6 py-4 text-sm">{{ advance.purpose }}</td>
            <td class="px-6 py-4 text-sm font-semibold">{{ formatCurrency(advance.amount) }}</td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs rounded-full" :class="getStatusClass(advance.status)">
                {{ advance.status }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm">{{ formatDate(advance.required_date) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const advances = ref([]);
const showForm = ref(false);

const fetchAdvances = async () => {
  try {
    const response = await axios.get('/api/travel-expenses/advance-requests');
    advances.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch advances:', error);
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PK');
};

const formatCurrency = (amount) => {
  return `Rs. ${parseFloat(amount).toLocaleString('en-PK')}`;
};

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    paid: 'bg-blue-100 text-blue-800',
    settled: 'bg-gray-100 text-gray-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  fetchAdvances();
});
</script>
