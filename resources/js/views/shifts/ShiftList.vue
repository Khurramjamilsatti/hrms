<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-lg font-semibold">My Shifts</h3>
      <button v-if="isManager" @click="$router.push('/shifts/rosters')" class="btn btn-primary">
        Manage Rosters
      </button>
    </div>

    <!-- Calendar View of Shifts -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <div class="flex justify-between items-center mb-4">
        <h4 class="font-semibold">Monthly Schedule</h4>
        <input type="month" v-model="currentMonth" @change="fetchShifts" class="form-input w-48" />
      </div>

      <div class="grid grid-cols-7 gap-2">
        <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day" class="text-center text-sm font-medium text-gray-700 py-2">
          {{ day }}
        </div>
        
        <div v-for="date in calendarDates" :key="date.toString()" class="border rounded p-2 min-h-24" :class="date.getMonth() !== new Date(currentMonth).getMonth() ? 'bg-gray-50' : ''">
          <div class="text-sm font-medium mb-1">{{ date.getDate() }}</div>
          <div v-for="shift in getShiftsForDate(date)" :key="shift.id" class="text-xs bg-primary-100 text-primary-800 rounded px-1 py-0.5 mb-1">
            {{ shift.shift?.name }} <br>
            {{ shift.start_time }} - {{ shift.end_time }}
          </div>
        </div>
      </div>
    </div>

    <!-- Shift Swap Requests -->
    <div class="bg-white rounded-lg shadow p-6">
      <div class="flex justify-between items-center mb-4">
        <h4 class="font-semibold">Shift Swap Requests</h4>
        <button @click="showSwapForm = true" class="btn btn-secondary">Request Swap</button>
      </div>

      <div class="space-y-3">
        <div v-for="swap in swapRequests" :key="swap.id" class="border rounded p-4">
          <div class="flex justify-between items-start">
            <div>
              <p class="font-medium">{{ swap.requester?.name }} ↔ {{ swap.swapper?.name }}</p>
              <p class="text-sm text-gray-600">{{ swap.reason }}</p>
              <p class="text-xs text-gray-500 mt-1">Date: {{ formatDate(swap.requester_assignment?.date) }}</p>
            </div>
            <span class="px-2 py-1 text-xs rounded-full" :class="getStatusClass(swap.status)">
              {{ swap.status }}
            </span>
          </div>
          <div v-if="swap.status === 'pending' && swap.swapper_id === authStore.user.id" class="mt-3 flex space-x-2">
            <button @click="respondToSwap(swap.id, 'accept')" class="btn btn-sm btn-primary">Accept</button>
            <button @click="respondToSwap(swap.id, 'decline')" class="btn btn-sm btn-secondary">Decline</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const shifts = ref([]);
const swapRequests = ref([]);
const currentMonth = ref(new Date().toISOString().substr(0, 7));
const showSwapForm = ref(false);

const isManager = computed(() => authStore.isAdmin || authStore.isManager);

const calendarDates = computed(() => {
  const [year, month] = currentMonth.value.split('-');
  const firstDay = new Date(year, month - 1, 1);
  const lastDay = new Date(year, month, 0);
  const dates = [];
  
  // Add days from previous month
  const startDay = firstDay.getDay();
  for (let i = startDay - 1; i >= 0; i--) {
    dates.push(new Date(year, month - 1, -i));
  }
  
  // Add days of current month
  for (let i = 1; i <= lastDay.getDate(); i++) {
    dates.push(new Date(year, month - 1, i));
  }
  
  // Add days from next month
  const remainingDays = 42 - dates.length;
  for (let i = 1; i <= remainingDays; i++) {
    dates.push(new Date(year, month, i));
  }
  
  return dates;
});

const fetchShifts = async () => {
  try {
    const [year, month] = currentMonth.value.split('-');
    const response = await axios.get(`/api/shift-scheduling/assignments?employee_id=${authStore.user.id}&month=${month}&year=${year}`);
    shifts.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch shifts:', error);
  }
};

const fetchSwapRequests = async () => {
  try {
    const response = await axios.get('/api/shift-scheduling/swap-requests');
    swapRequests.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch swap requests:', error);
  }
};

const getShiftsForDate = (date) => {
  const dateStr = date.toISOString().substr(0, 10);
  return shifts.value.filter(shift => shift.date === dateStr);
};

const respondToSwap = async (id, response) => {
  try {
    await axios.post(`/api/shift-scheduling/swap-requests/${id}/respond`, { response });
    fetchSwapRequests();
    fetchShifts();
  } catch (error) {
    console.error('Failed to respond to swap:', error);
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PK');
};

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    accepted: 'bg-blue-100 text-blue-800',
    declined: 'bg-red-100 text-red-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  fetchShifts();
  fetchSwapRequests();
});
</script>
