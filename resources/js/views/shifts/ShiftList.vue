<template>
  <div class="p-6 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">My Schedule</h1>
        <p class="text-sm text-gray-500 mt-1">View your assigned shifts and manage swap requests</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <button
          type="button"
          @click="goPrevMonth"
          class="px-3 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
        >
          Previous
        </button>
        <button
          type="button"
          @click="goToday"
          class="px-3 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
        >
          Today
        </button>
        <button
          type="button"
          @click="goNextMonth"
          class="px-3 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
        >
          Next
        </button>
        <button
          v-if="canManage"
          type="button"
          @click="$router.push('/shifts')"
          class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
        >
          Manage Shifts
        </button>
        <button
          v-if="canManage"
          type="button"
          @click="$router.push('/shifts/rosters')"
          class="px-4 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800"
        >
          Rosters
        </button>
      </div>
    </div>

    <div v-if="!employeeId" class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-900">
      Your account is not linked to an employee profile, so personal shifts cannot be loaded.
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">This month</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ shifts.length }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Upcoming</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ upcomingShifts.length }}</p>
      </div>
      <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Pending swaps</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ pendingSwaps.length }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
      <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <h2 class="text-base font-bold text-gray-900">{{ monthLabel }}</h2>
          <input
            v-model="currentMonth"
            type="month"
            class="w-full sm:w-48 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
          />
        </div>

        <div v-if="loading" class="flex justify-center py-20">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
        </div>
        <div v-else class="p-3 md:p-4">
          <div class="grid grid-cols-7 gap-1 md:gap-2 mb-2">
            <div
              v-for="day in weekDays"
              :key="day"
              class="text-center text-[11px] font-semibold uppercase tracking-wide text-gray-500 py-2"
            >
              {{ day }}
            </div>
          </div>
          <div class="grid grid-cols-7 gap-1 md:gap-2">
            <button
              v-for="cell in calendarDates"
              :key="cell.key"
              type="button"
              @click="selectedDay = cell.key"
              class="text-left border rounded-lg p-1.5 min-h-[72px] md:min-h-[96px] transition-colors"
              :class="cellClass(cell)"
            >
              <div
                class="inline-flex items-center justify-center w-6 h-6 text-xs font-semibold rounded-full mb-1"
                :class="dayNumberClass(cell)"
              >
                {{ cell.date.getDate() }}
              </div>
              <div
                v-for="shift in cell.shifts.slice(0, 2)"
                :key="shift.id"
                class="text-[10px] md:text-[11px] font-medium rounded px-1 py-0.5 mb-0.5 truncate bg-sky-50 text-sky-900 border border-sky-100"
              >
                {{ shift.shift?.name || 'Shift' }}
              </div>
              <p v-if="cell.shifts.length > 2" class="text-[10px] text-gray-500">+{{ cell.shifts.length - 2 }} more</p>
            </button>
          </div>
        </div>
      </div>

      <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
          <h3 class="text-base font-bold text-gray-900 mb-3">{{ selectedDayLabel }}</h3>
          <div v-if="selectedDayShifts.length" class="space-y-3">
            <div
              v-for="shift in selectedDayShifts"
              :key="`sel-${shift.id}`"
              class="rounded-lg border border-gray-100 p-3"
            >
              <p class="text-sm font-semibold text-gray-900">{{ shift.shift?.name || 'Shift' }}</p>
              <p class="text-xs text-gray-500 mt-1">
                {{ formatTime(shift.start_time || shift.shift?.start_time) }}
                –
                {{ formatTime(shift.end_time || shift.shift?.end_time) }}
              </p>
              <p v-if="shift.roster?.name" class="text-xs text-gray-500 mt-1">Roster: {{ shift.roster.name }}</p>
              <p v-if="shift.is_day_off" class="text-xs font-semibold text-amber-700 mt-1">Day off</p>
            </div>
          </div>
          <p v-else class="text-sm text-gray-500 py-6 text-center">No shifts on this day</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
          <h3 class="text-base font-bold text-gray-900 mb-3">Upcoming</h3>
          <div v-if="upcomingShifts.length" class="space-y-3 max-h-64 overflow-y-auto">
            <div
              v-for="shift in upcomingShifts"
              :key="`up-${shift.id}`"
              class="border-l-4 border-sky-500 pl-3 py-1"
            >
              <p class="text-sm font-semibold text-gray-900">{{ shift.shift?.name || 'Shift' }}</p>
              <p class="text-xs text-gray-500">{{ formatDate(shift.date) }} · {{ formatTime(shift.start_time || shift.shift?.start_time) }}</p>
            </div>
          </div>
          <p v-else class="text-sm text-gray-500 py-6 text-center">No upcoming shifts</p>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-gray-900">Swap Requests</h2>
        <span class="text-xs font-semibold text-gray-500">{{ swapRequests.length }} total</span>
      </div>

      <div v-if="!swapRequests.length" class="text-sm text-gray-500 text-center py-10">
        No shift swap requests right now
      </div>
      <div v-else class="space-y-3">
        <div
          v-for="swap in swapRequests"
          :key="swap.id"
          class="rounded-xl border border-gray-100 p-4"
        >
          <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
            <div>
              <p class="font-semibold text-gray-900">
                {{ employeeLabel(swap.requester) }}
                <span class="text-gray-400 font-normal">↔</span>
                {{ employeeLabel(swap.swapper) }}
              </p>
              <p class="text-sm text-gray-600 mt-1">{{ swap.reason || 'No reason provided' }}</p>
              <p class="text-xs text-gray-500 mt-1">
                {{ formatDate(swap.requester_assignment?.date) }}
                ·
                {{ swap.requester_assignment?.shift?.name || 'Shift' }}
              </p>
            </div>
            <span class="self-start px-2.5 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(swap.status)">
              {{ swap.status }}
            </span>
          </div>
          <div v-if="swap.status === 'pending' && isSwapTarget(swap)" class="mt-3 flex gap-2">
            <button
              type="button"
              @click="respondToSwap(swap.id, 'accept')"
              class="px-3 py-1.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700"
            >
              Accept
            </button>
            <button
              type="button"
              @click="respondToSwap(swap.id, 'decline')"
              class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
            >
              Decline
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import { useNotification } from '@/composables/useNotification';
import { usePermissions } from '@/composables/usePermissions';

const authStore = useAuthStore();
const { error: showError, success } = useNotification();
const { canAny } = usePermissions();

const shifts = ref([]);
const swapRequests = ref([]);
const loading = ref(false);
const currentMonth = ref(new Date().toISOString().slice(0, 7));
const selectedDay = ref(new Date().toISOString().slice(0, 10));
const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const employeeId = computed(() => authStore.user?.employee?.id || authStore.user?.employee_id || null);
const canManage = computed(() =>
  canAny(['shifts.assign', 'shifts.manage', 'shifts.create', 'shifts.update', 'shifts.delete'])
);

const monthLabel = computed(() => {
  const [y, m] = currentMonth.value.split('-').map(Number);
  return new Date(y, m - 1, 1).toLocaleDateString('en-PK', { month: 'long', year: 'numeric' });
});

const calendarDates = computed(() => {
  const [yearStr, monthStr] = currentMonth.value.split('-');
  const year = Number(yearStr);
  const month = Number(monthStr) - 1;
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);
  const cells = [];

  for (let i = firstDay.getDay() - 1; i >= 0; i--) {
    cells.push(buildCell(new Date(year, month, -i), false));
  }
  for (let d = 1; d <= lastDay.getDate(); d++) {
    cells.push(buildCell(new Date(year, month, d), true));
  }
  const remaining = 42 - cells.length;
  for (let i = 1; i <= remaining; i++) {
    cells.push(buildCell(new Date(year, month + 1, i), false));
  }
  return cells;
});

const selectedDayShifts = computed(() =>
  shifts.value.filter((shift) => String(shift.date).slice(0, 10) === selectedDay.value)
);

const selectedDayLabel = computed(() =>
  new Date(`${selectedDay.value}T00:00:00`).toLocaleDateString('en-PK', {
    weekday: 'long',
    month: 'short',
    day: 'numeric',
  })
);

const upcomingShifts = computed(() => {
  const today = new Date().toISOString().slice(0, 10);
  return [...shifts.value]
    .filter((s) => String(s.date).slice(0, 10) >= today)
    .sort((a, b) => String(a.date).localeCompare(String(b.date)))
    .slice(0, 8);
});

const pendingSwaps = computed(() => swapRequests.value.filter((s) => s.status === 'pending'));

const toDateKey = (date) => {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
};

const buildCell = (date, inMonth) => {
  const key = toDateKey(date);
  return {
    key,
    date,
    inMonth,
    isToday: key === new Date().toISOString().slice(0, 10),
    shifts: shifts.value.filter((shift) => String(shift.date).slice(0, 10) === key),
  };
};

const cellClass = (cell) => {
  const classes = [];
  if (!cell.inMonth) classes.push('bg-gray-50 border-gray-100 text-gray-400');
  else classes.push('bg-white border-gray-100 hover:bg-gray-50');
  if (cell.key === selectedDay.value) classes.push('ring-2 ring-gray-900');
  if (cell.isToday) classes.push('border-gray-300');
  return classes;
};

const dayNumberClass = (cell) => {
  if (cell.isToday) return 'bg-gray-900 text-white';
  if (!cell.inMonth) return 'text-gray-400';
  return 'text-gray-800';
};

const fetchShifts = async () => {
  if (!employeeId.value) {
    shifts.value = [];
    return;
  }
  loading.value = true;
  try {
    const [year, month] = currentMonth.value.split('-');
    const response = await axios.get('/shift-scheduling/assignments', {
      params: {
        employee_id: employeeId.value,
        month,
        year,
        per_page: 200,
      },
    });
    shifts.value = response.data.data || response.data || [];
  } catch (err) {
    console.error(err);
    showError('Failed to load shifts');
  } finally {
    loading.value = false;
  }
};

const fetchSwapRequests = async () => {
  try {
    const response = await axios.get('/shift-scheduling/swap-requests');
    swapRequests.value = response.data.data || response.data || [];
  } catch (err) {
    console.error(err);
  }
};

const employeeLabel = (employee) => {
  if (!employee) return 'Colleague';
  const name = `${employee.first_name || ''} ${employee.last_name || ''}`.trim();
  return name || employee.name || employee.employee_code || 'Colleague';
};

const isSwapTarget = (swap) => Number(swap.swapper_id) === Number(employeeId.value);

const respondToSwap = async (id, response) => {
  try {
    await axios.post(`/shift-scheduling/swap-requests/${id}/respond`, { response });
    success(response === 'accept' ? 'Swap accepted' : 'Swap declined');
    await Promise.all([fetchSwapRequests(), fetchShifts()]);
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to respond to swap');
  }
};

const goPrevMonth = () => {
  const [y, m] = currentMonth.value.split('-').map(Number);
  const d = new Date(y, m - 2, 1);
  currentMonth.value = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
};

const goNextMonth = () => {
  const [y, m] = currentMonth.value.split('-').map(Number);
  const d = new Date(y, m, 1);
  currentMonth.value = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
};

const goToday = () => {
  const today = new Date();
  currentMonth.value = today.toISOString().slice(0, 7);
  selectedDay.value = today.toISOString().slice(0, 10);
};

watch(currentMonth, () => {
  fetchShifts();
  const [y, m] = currentMonth.value.split('-');
  if (!selectedDay.value.startsWith(`${y}-${m}`)) {
    selectedDay.value = `${y}-${m}-01`;
  }
});

const formatDate = (date) =>
  date ? new Date(date).toLocaleDateString('en-PK', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

const formatTime = (time) => (time ? String(time).slice(0, 5) : '—');

const getStatusClass = (status) => ({
  pending: 'bg-amber-50 text-amber-800 border border-amber-200',
  accepted: 'bg-sky-50 text-sky-800 border border-sky-200',
  declined: 'bg-rose-50 text-rose-800 border border-rose-200',
  rejected: 'bg-rose-50 text-rose-800 border border-rose-200',
  approved: 'bg-emerald-50 text-emerald-800 border border-emerald-200',
}[status] || 'bg-gray-100 text-gray-700');

onMounted(async () => {
  await Promise.all([fetchShifts(), fetchSwapRequests()]);
});
</script>
