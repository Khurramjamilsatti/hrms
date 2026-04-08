<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Attendance Management</h1>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Present Today</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.present }}</h3>
          </div>
          <div class="bg-gray-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Late Arrivals</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.late }}</h3>
          </div>
          <div class="bg-gray-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
              <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">On Leave</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.on_leave }}</h3>
          </div>
          <div class="bg-gray-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
              <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Absent</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.absent }}</h3>
          </div>
          <div class="bg-gray-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
          <input 
            v-model="filters.date" 
            type="date" 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
            @change="loadAttendance"
          />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <select 
            v-model="filters.status" 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
            @change="loadAttendance"
          >
            <option value="">All Status</option>
            <option value="present">Present</option>
            <option value="late">Late</option>
            <option value="half_day">Half Day</option>
            <option value="on_leave">On Leave</option>
            <option value="absent">Absent</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Search Employee</label>
          <input 
            v-model="filters.search" 
            type="text" 
            placeholder="Search by name or code..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
            @input="loadAttendance"
          />
        </div>
        <div class="flex items-end">
          <button 
            @click="resetFilters"
            class="w-full px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors"
          >
            Reset Filters
          </button>
        </div>
      </div>
    </div>

    <div v-if="loading" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <div class="text-gray-600">Loading attendance records...</div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
      {{ error }}
    </div>

    <!-- Attendance Table -->
    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-100 border-b border-gray-300">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Employee</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Sessions</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Total Hours</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Remarks</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Details</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <template v-for="attendance in attendances" :key="attendance.id">
              <!-- Main Row -->
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ formatDate(attendance.date) }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div>
                    <div class="text-sm font-semibold text-gray-900">{{ attendance.employee?.full_name }}</div>
                    <div class="text-xs text-gray-500">{{ attendance.employee?.employee_code }}</div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                    {{ attendance.sessions_count }} {{ attendance.sessions_count === 1 ? 'Session' : 'Sessions' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div>
                    <span class="text-sm font-semibold text-gray-900">{{ attendance.total_working_hours || 0 }}h</span>
                    <span v-if="attendance.total_overtime_hours > 0" class="ml-1 text-xs text-blue-600">(+{{ attendance.total_overtime_hours }}h OT)</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span :class="{
                    'inline-flex px-2.5 py-1 text-xs font-semibold rounded-full': true,
                    'bg-green-100 text-green-700': attendance.status === 'present',
                    'bg-amber-100 text-amber-700': attendance.status === 'late',
                    'bg-blue-100 text-blue-700': attendance.status === 'half_day',
                    'bg-gray-100 text-gray-700': attendance.status === 'on_leave',
                    'bg-red-100 text-red-700': attendance.status === 'absent',
                  }">
                    {{ attendance.status?.replace('_', ' ') }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="text-sm text-gray-600">{{ attendance.remarks || '-' }}</span>
                </td>
                <td class="px-6 py-4 text-center">
                  <button 
                    @click="toggleSessionDetails(attendance.id)"
                    class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                  >
                    {{ expandedRows.has(attendance.id) ? 'Hide' : 'Show' }}
                  </button>
                </td>
              </tr>
              
              <!-- Expanded Session Details -->
              <tr v-if="expandedRows.has(attendance.id)" class="bg-gray-50">
                <td colspan="7" class="px-6 py-4">
                  <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Session Details</h4>
                    <div class="space-y-2">
                      <div 
                        v-for="(session, index) in attendance.sessions" 
                        :key="session.id"
                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                      >
                        <div class="flex items-center space-x-4">
                          <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                            {{ index + 1 }}
                          </span>
                          <div>
                            <div class="text-sm font-medium text-gray-900">
                              Check-in: <span class="text-blue-600">{{ session.check_in || '-' }}</span>
                            </div>
                            <div class="text-sm text-gray-600">
                              Check-out: <span class="text-gray-900">{{ session.check_out || 'In Progress' }}</span>
                            </div>
                          </div>
                        </div>
                        <div class="text-right">
                          <div class="text-sm font-semibold text-gray-900">{{ session.working_hours || 0 }}h</div>
                          <div v-if="session.overtime_hours > 0" class="text-xs text-blue-600">+{{ session.overtime_hours }}h OT</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination" class="px-6 py-4 bg-gray-100 border-t border-gray-300 flex items-center justify-between">
        <div class="text-sm text-gray-600">
          Showing <span class="font-semibold text-gray-900">{{ ((pagination.current_page - 1) * pagination.per_page) + 1 }}</span> to 
          <span class="font-semibold text-gray-900">{{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}</span> of 
          <span class="font-semibold text-gray-900">{{ pagination.total }}</span> records
        </div>
        <div class="flex items-center space-x-2">
          <button
            @click="loadPage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :class="pagination.current_page === 1 ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'"
          >
            Previous
          </button>
          <button
            @click="loadPage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :class="pagination.current_page === pagination.last_page ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const attendances = ref([]);
const loading = ref(false);
const error = ref(null);
const pagination = ref(null);
const showCheckInModal = ref(false);
const expandedRows = ref(new Set());

const filters = ref({
  date: '', // Empty by default to show all records
  status: '',
  search: ''
});

const stats = computed(() => {
  const list = attendances.value || [];
  return {
    present: list.filter(a => a.status === 'present').length,
    late: list.filter(a => a.status === 'late').length,
    on_leave: list.filter(a => a.status === 'on_leave').length,
    absent: list.filter(a => a.status === 'absent').length
  };
});

const toggleSessionDetails = (attendanceId) => {
  if (expandedRows.value.has(attendanceId)) {
    expandedRows.value.delete(attendanceId);
  } else {
    expandedRows.value.add(attendanceId);
  }
};

const loadAttendance = async (page = 1) => {
  loading.value = true;
  error.value = null;
  
  try {
    const params = {
      page,
      date: filters.value.date,
      status: filters.value.status,
      search: filters.value.search
    };
    
    const response = await axios.get('/attendance', { params });
    attendances.value = response.data.data || [];
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
      total: response.data.total
    };
  } catch (err) {
    error.value = 'Failed to load attendance records';
    console.error('Failed to load attendance:', err);
  } finally {
    loading.value = false;
  }
};

const loadPage = (page) => {
  loadAttendance(page);
};

const resetFilters = () => {
  filters.value = {
    date: '', // Empty to show all records
    status: '',
    search: ''
  };
  loadAttendance();
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', { 
    weekday: 'short', 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });
};

const getShift = (attendance) => {
  if (!attendance.check_in) return 'N/A';
  
  const checkInTime = new Date('2000-01-01 ' + attendance.check_in);
  const hour = checkInTime.getHours();
  
  if (hour < 12) return 'Morning';
  if (hour < 18) return 'Afternoon';
  return 'Evening';
};

const isLate = (attendance) => {
  if (!attendance.check_in || attendance.status !== 'late') return false;
  return true;
};

onMounted(() => {
  loadAttendance();
});
</script>
