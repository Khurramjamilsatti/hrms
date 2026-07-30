<template>
  <div class="p-4 md:p-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Attendance Report</h1>
        <p class="text-sm text-gray-500 mt-1">
          {{ calendarData?.employee?.full_name || 'Your attendance' }}
          <span v-if="calendarData?.employee?.employee_code" class="text-gray-400">· {{ calendarData.employee.employee_code }}</span>
        </p>
      </div>
      <div class="flex items-center gap-2">
        <button
          v-if="canViewTeamRecords"
          @click="viewMode = viewMode === 'calendar' ? 'list' : 'calendar'"
          class="px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors"
        >
          {{ viewMode === 'calendar' ? 'All Records' : 'Calendar' }}
        </button>
      </div>
    </div>

    <!-- Calendar Report -->
    <template v-if="viewMode === 'calendar'">
      <!-- Employee picker (managers / section heads / HR) -->
      <div v-if="canSelectEmployee" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-4">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Employee</label>
        <div class="relative">
          <input
            v-model="employeeSearch"
            @input="filterEmployees"
            @focus="showEmployeeDropdown = true"
            type="text"
            placeholder="Search by name or employee ID..."
            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent"
          />
          <div
            v-if="showEmployeeDropdown && filteredEmployees.length"
            class="absolute z-40 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto"
          >
            <button
              v-if="currentEmployeeId"
              type="button"
              @click="selectEmployee({ id: currentEmployeeId, full_name: 'Me (current user)', employee_code: '' })"
              class="w-full text-left px-4 py-2.5 hover:bg-gray-50 border-b border-gray-100"
            >
              <div class="text-sm font-medium text-accent">Me (current user)</div>
            </button>
            <button
              v-for="emp in filteredEmployees"
              :key="emp.id"
              type="button"
              @click="selectEmployee(emp)"
              class="w-full text-left px-4 py-2.5 hover:bg-gray-50 border-b border-gray-100 last:border-0"
            >
              <div class="text-sm font-medium text-gray-900">{{ emp.full_name || `${emp.first_name || ''} ${emp.last_name || ''}`.trim() }}</div>
              <div class="text-xs text-gray-500">{{ emp.employee_code }} · {{ emp.department?.name || 'N/A' }}</div>
            </button>
          </div>
        </div>
        <div v-if="selectedEmployeeLabel" class="mt-2 text-xs text-gray-500">
          Showing: <span class="font-semibold text-gray-800">{{ selectedEmployeeLabel }}</span>
        </div>
      </div>

      <!-- Month navigation -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-200 px-4 py-3 mb-4 flex items-center justify-between">
        <button
          type="button"
          @click="shiftMonth(-1)"
          class="w-10 h-10 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors"
          aria-label="Previous month"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <div class="text-lg font-bold text-gray-900">{{ monthLabel }}</div>
        <button
          type="button"
          @click="shiftMonth(1)"
          class="w-10 h-10 rounded-full flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors"
          aria-label="Next month"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>

      <!-- Status filter chips -->
      <div class="flex gap-2 overflow-x-auto pb-2 mb-4 scrollbar-hide">
        <button
          v-for="chip in statusChips"
          :key="chip.value"
          type="button"
          @click="statusFilter = chip.value"
          class="shrink-0 px-4 py-1.5 rounded-full text-sm font-semibold transition-colors"
          :class="statusFilter === chip.value
            ? 'bg-accent text-white shadow'
            : 'bg-accent/10 text-accent hover:bg-accent/20'"
        >
          {{ chip.label }}
        </button>
      </div>

      <!-- Summary stats -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-200 px-3 py-4 mb-4">
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 text-center">
          <div>
            <div class="text-xs text-gray-500 mb-1">Present</div>
            <div class="text-xl font-bold text-green-600">{{ summary.present }}</div>
          </div>
          <div>
            <div class="text-xs text-gray-500 mb-1">Absent</div>
            <div class="text-xl font-bold text-red-600">{{ summary.absent }}</div>
          </div>
          <div>
            <div class="text-xs text-gray-500 mb-1">Late</div>
            <div class="text-xl font-bold text-amber-500">{{ summary.late }}</div>
          </div>
          <div>
            <div class="text-xs text-gray-500 mb-1">Half</div>
            <div class="text-xl font-bold text-yellow-600">{{ summary.half_day }}</div>
          </div>
          <div>
            <div class="text-xs text-gray-500 mb-1">Leave</div>
            <div class="text-xl font-bold text-indigo-600">{{ summary.on_leave }}</div>
          </div>
          <div>
            <div class="text-xs text-gray-500 mb-1">Hrs</div>
            <div class="text-xl font-bold text-accent">{{ summary.working_hours }}</div>
          </div>
        </div>
      </div>

      <!-- Loading / error -->
      <div v-if="loading" class="bg-white rounded-2xl border border-gray-200 p-12 text-center text-gray-500 mb-4">
        Loading attendance calendar...
      </div>
      <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4">
        {{ error }}
        <button @click="loadCalendar" class="ml-2 underline text-sm">Retry</button>
      </div>

      <!-- Month grid calendar -->
      <div v-else class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 md:p-4">
        <div class="grid grid-cols-7 gap-1 mb-2">
          <div v-for="day in weekDays" :key="day" class="text-center text-[11px] font-semibold text-gray-500 py-1">{{ day }}</div>
        </div>
        <div class="grid grid-cols-7 gap-1">
          <button
            v-for="cell in calendarCells"
            :key="cell.key"
            type="button"
            :disabled="!cell.inMonth"
            @click="openDayPopup(cell)"
            class="relative min-h-[52px] md:min-h-[72px] rounded-xl p-1.5 text-left transition-all border"
            :class="cellClass(cell)"
          >
            <div class="text-xs font-bold" :class="cell.inMonth ? 'text-gray-900' : 'text-gray-300'">{{ cell.day }}</div>
            <div v-if="cell.inMonth && cell.status && cell.status !== 'upcoming'" class="mt-1">
              <span class="inline-block w-2 h-2 rounded-full" :class="statusDot(cell.status)"></span>
              <div class="hidden md:block text-[10px] leading-tight mt-0.5 truncate" :class="statusTextClass(cell.status)">
                {{ statusLabel(cell.status) }}
              </div>
            </div>
          </button>
        </div>
      </div>

      <!-- Day detail popup -->
      <div
        v-if="showDayPopup && selectedDayDetail"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
        @click.self="closeDayPopup"
      >
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between gap-3">
            <div>
              <div class="text-lg font-bold text-gray-900">{{ formatDate(selectedDayDetail.date) }}</div>
              <div class="text-xs text-gray-500 mt-0.5">{{ calendarData?.employee?.full_name }}</div>
            </div>
            <button type="button" @click="closeDayPopup" class="text-gray-400 hover:text-gray-600 p-1" aria-label="Close">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </button>
          </div>

          <div class="px-5 py-4 space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-500">Status</span>
              <span class="px-3 py-1 rounded-full text-xs font-semibold capitalize" :class="statusBadgeClass(selectedDayDetail.status)">
                {{ statusLabel(selectedDayDetail.status) }}
              </span>
            </div>

            <template v-if="selectedDayDetail.status === 'weekend'">
              <p class="text-sm text-gray-600">This day is marked as a weekend.</p>
            </template>
            <template v-else-if="selectedDayDetail.status === 'upcoming'">
              <p class="text-sm text-gray-600">No attendance yet for this upcoming date.</p>
            </template>
            <template v-else>
              <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-green-50 border border-green-100 p-3">
                  <div class="flex items-center gap-1.5 text-xs font-semibold text-green-700 mb-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-green-100">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </span>
                    Check-in
                  </div>
                  <div class="text-base font-bold text-gray-900">{{ formatTime(selectedDayDetail.check_in) }}</div>
                </div>
                <div class="rounded-xl bg-red-50 border border-red-100 p-3">
                  <div class="flex items-center gap-1.5 text-xs font-semibold text-red-600 mb-1">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-red-100">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </span>
                    Check-out
                  </div>
                  <div class="text-base font-bold text-gray-900">{{ formatTime(selectedDayDetail.check_out) }}</div>
                </div>
              </div>

              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Working hours</span>
                <span class="font-semibold text-gray-900">{{ selectedDayDetail.working_hours || 0 }}h</span>
              </div>
              <div v-if="selectedDayDetail.overtime_hours" class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Overtime</span>
                <span class="font-semibold text-blue-600">+{{ selectedDayDetail.overtime_hours }}h</span>
              </div>
              <div v-if="selectedDayDetail.sessions_count > 1" class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Sessions</span>
                <span class="font-semibold text-gray-900">{{ selectedDayDetail.sessions_count }}</span>
              </div>
              <div v-if="selectedDayDetail.remarks" class="text-sm">
                <div class="text-gray-500 mb-1">Remarks</div>
                <p class="text-gray-800 bg-gray-50 rounded-lg px-3 py-2">{{ selectedDayDetail.remarks }}</p>
              </div>

              <div v-if="selectedDayDetail.sessions?.length > 1" class="border-t border-gray-100 pt-3 space-y-2">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Sessions</div>
                <div
                  v-for="(session, index) in selectedDayDetail.sessions"
                  :key="session.id || index"
                  class="flex items-center justify-between text-sm bg-gray-50 rounded-lg px-3 py-2"
                >
                  <span class="text-gray-600">#{{ index + 1 }} · {{ formatTime(session.check_in) }} – {{ formatTime(session.check_out) }}</span>
                  <span class="font-medium text-gray-900">{{ session.working_hours || 0 }}h</span>
                </div>
              </div>
            </template>
          </div>

          <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex justify-end">
            <button type="button" @click="closeDayPopup" class="px-4 py-2 text-sm font-medium text-white bg-accent hover:bg-accent-dark rounded-lg transition-colors">
              Close
            </button>
          </div>
        </div>
      </div>
    </template>

    <!-- Legacy all-records list for team/HR -->
    <template v-else>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
            <input v-model="listFilters.date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" @change="loadAttendanceList" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
            <select v-model="listFilters.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" @change="loadAttendanceList">
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
            <input v-model="listFilters.search" type="text" placeholder="Search by name or code..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" @input="loadAttendanceList" />
          </div>
          <div class="flex items-end">
            <button @click="resetListFilters" class="w-full px-4 py-2 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors">Reset Filters</button>
          </div>
        </div>
      </div>

      <div v-if="listLoading" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center text-gray-600">Loading attendance records...</div>
      <div v-else-if="listError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">{{ listError }}</div>
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
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="row in attendances" :key="row.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ formatDate(row.date) }}</td>
                <td class="px-6 py-4">
                  <div class="text-sm font-semibold text-gray-900">{{ row.employee?.full_name }}</div>
                  <div class="text-xs text-gray-500">{{ row.employee?.employee_code }}</div>
                </td>
                <td class="px-6 py-4 text-sm">{{ row.sessions_count }}</td>
                <td class="px-6 py-4 text-sm font-semibold">{{ row.total_working_hours || 0 }}h</td>
                <td class="px-6 py-4 text-center">
                  <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full capitalize" :class="statusBadgeClass(row.status)">{{ statusLabel(row.status) }}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="pagination" class="px-6 py-4 bg-gray-100 border-t flex items-center justify-between">
          <div class="text-sm text-gray-600">{{ pagination.total }} records</div>
          <div class="flex gap-2">
            <button @click="loadAttendanceList(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-4 py-2 text-sm rounded-lg disabled:opacity-50 bg-accent text-white">Previous</button>
            <button @click="loadAttendanceList(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-4 py-2 text-sm rounded-lg disabled:opacity-50 bg-accent text-white">Next</button>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import { usePermissions } from '@/composables/usePermissions';

const authStore = useAuthStore();
const { canAny } = usePermissions();

const viewMode = ref('calendar');
const loading = ref(false);
const error = ref(null);
const calendarData = ref(null);
const statusFilter = ref('');
const showDayPopup = ref(false);
const selectedDayDetail = ref(null);

const now = new Date();
const selectedMonth = ref(now.getMonth() + 1);
const selectedYear = ref(now.getFullYear());

const currentEmployeeId = computed(() => authStore.user?.employee?.id || null);
const selectedEmployeeId = ref(currentEmployeeId.value);

const canSelectEmployee = computed(() =>
  authStore.isAdmin ||
  authStore.isManager ||
  canAny(['attendance.manage', 'attendance.reports'])
);

const canViewTeamRecords = computed(() => canSelectEmployee.value);

const employees = ref([]);
const filteredEmployees = ref([]);
const employeeSearch = ref('');
const showEmployeeDropdown = ref(false);

const selectedEmployeeLabel = computed(() => {
  if (!selectedEmployeeId.value) return '';
  if (selectedEmployeeId.value === currentEmployeeId.value) {
    return calendarData.value?.employee?.full_name
      ? `${calendarData.value.employee.full_name} (you)`
      : 'You';
  }
  return calendarData.value?.employee
    ? `${calendarData.value.employee.full_name} (${calendarData.value.employee.employee_code})`
    : '';
});

const monthLabel = computed(() => {
  return new Date(selectedYear.value, selectedMonth.value - 1, 1)
    .toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
});

const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const statusChips = [
  { value: '', label: 'All' },
  { value: 'present', label: 'Present' },
  { value: 'late', label: 'Late' },
  { value: 'absent', label: 'Absent' },
  { value: 'half_day', label: 'Half day' },
  { value: 'on_leave', label: 'Leave' },
  { value: 'weekend', label: 'Weekend' },
];

const summary = computed(() => ({
  present: calendarData.value?.summary?.present ?? 0,
  absent: calendarData.value?.summary?.absent ?? 0,
  late: calendarData.value?.summary?.late ?? 0,
  half_day: calendarData.value?.summary?.half_day ?? 0,
  on_leave: calendarData.value?.summary?.on_leave ?? 0,
  working_hours: calendarData.value?.summary?.working_hours ?? 0,
}));

const daysByDate = computed(() => {
  const map = {};
  (calendarData.value?.days || []).forEach((d) => { map[d.date] = d; });
  return map;
});

const calendarCells = computed(() => {
  const year = selectedYear.value;
  const month = selectedMonth.value - 1;
  const first = new Date(year, month, 1);
  const last = new Date(year, month + 1, 0);
  const cells = [];

  for (let i = 0; i < first.getDay(); i++) {
    const d = new Date(year, month, -i);
    cells.unshift(buildCell(d, false));
  }
  for (let day = 1; day <= last.getDate(); day++) {
    cells.push(buildCell(new Date(year, month, day), true));
  }
  while (cells.length % 7 !== 0) {
    const next = cells.length - (first.getDay() + last.getDate()) + 1;
    cells.push(buildCell(new Date(year, month + 1, next), false));
  }
  return cells;
});

function toDateKey(date) {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
}

function buildCell(date, inMonth) {
  const key = toDateKey(date);
  const dayData = daysByDate.value[key];
  return {
    key: `${inMonth ? 'in' : 'out'}-${key}`,
    date: key,
    day: date.getDate(),
    inMonth,
    status: dayData?.status || null,
    isToday: dayData?.is_today || false,
  };
}

function cellClass(cell) {
  if (!cell.inMonth) return 'border-transparent bg-transparent cursor-default';
  const dimmed = statusFilter.value && cell.status !== statusFilter.value;
  const base = `border-gray-100 hover:border-accent/40 hover:shadow-sm cursor-pointer ${dimmed ? 'opacity-35' : ''}`;
  if (selectedDayDetail.value?.date === cell.date && showDayPopup.value) return `${base} bg-accent/10 border-accent ring-1 ring-accent`;
  if (cell.isToday) return `${base} bg-gray-50 border-gray-200`;
  if (cell.status === 'absent') return `${base} bg-red-50/60`;
  if (cell.status === 'late') return `${base} bg-amber-50/70`;
  if (cell.status === 'present') return `${base} bg-green-50/60`;
  if (cell.status === 'on_leave') return `${base} bg-indigo-50/60`;
  if (cell.status === 'weekend') return `${base} bg-gray-50`;
  return `${base} bg-white`;
}

function openDayPopup(cell) {
  if (!cell.inMonth) return;
  selectedDayDetail.value = daysByDate.value[cell.date] || {
    date: cell.date,
    day: cell.day,
    status: 'upcoming',
    check_in: null,
    check_out: null,
    working_hours: 0,
    overtime_hours: 0,
    sessions: [],
  };
  showDayPopup.value = true;
}

function closeDayPopup() {
  showDayPopup.value = false;
  selectedDayDetail.value = null;
}

function statusDot(status) {
  return {
    present: 'bg-green-500',
    late: 'bg-amber-500',
    absent: 'bg-red-500',
    half_day: 'bg-yellow-500',
    on_leave: 'bg-indigo-500',
    weekend: 'bg-gray-400',
    upcoming: 'bg-gray-300',
  }[status] || 'bg-gray-300';
}

function statusBadgeClass(status) {
  return {
    present: 'bg-green-100 text-green-700',
    late: 'bg-red-50 text-red-600',
    absent: 'bg-red-50 text-red-600',
    half_day: 'bg-yellow-100 text-yellow-700',
    on_leave: 'bg-indigo-100 text-indigo-700',
    weekend: 'bg-gray-100 text-gray-600',
    upcoming: 'bg-gray-50 text-gray-400',
  }[status] || 'bg-gray-100 text-gray-600';
}

function statusTextClass(status) {
  return {
    present: 'text-green-700',
    late: 'text-amber-700',
    absent: 'text-red-600',
    half_day: 'text-yellow-700',
    on_leave: 'text-indigo-700',
    weekend: 'text-gray-500',
  }[status] || 'text-gray-500';
}

function statusLabel(status) {
  if (!status) return '—';
  if (status === 'on_leave') return 'Leave';
  if (status === 'half_day') return 'Half day';
  return status.replace('_', ' ');
}

function formatTime(value) {
  if (!value) return '-';
  const str = String(value);
  return str.length >= 8 ? str.slice(0, 8) : str;
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'short', year: 'numeric', month: 'short', day: 'numeric',
  });
}

function shiftMonth(delta) {
  let m = selectedMonth.value + delta;
  let y = selectedYear.value;
  if (m < 1) { m = 12; y -= 1; }
  if (m > 12) { m = 1; y += 1; }
  selectedMonth.value = m;
  selectedYear.value = y;
}

async function loadCalendar() {
  if (!selectedEmployeeId.value && !currentEmployeeId.value) {
    error.value = 'No employee profile linked. Select an employee to view attendance.';
    return;
  }

  loading.value = true;
  error.value = null;
  try {
    const params = {
      month: selectedMonth.value,
      year: selectedYear.value,
    };
    if (selectedEmployeeId.value) params.employee_id = selectedEmployeeId.value;

    const response = await axios.get('/attendance/calendar', { params });
    calendarData.value = response.data;
    selectedEmployeeId.value = response.data.employee?.id || selectedEmployeeId.value;
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load attendance calendar';
    calendarData.value = null;
  } finally {
    loading.value = false;
  }
}

async function loadEmployees() {
  if (!canSelectEmployee.value) return;
  try {
    let response;
    try {
      response = await axios.get('/employees/dropdown');
    } catch {
      response = await axios.get('/employees', { params: { per_page: 100 } });
    }
    employees.value = Array.isArray(response.data) ? response.data : (response.data.data || []);
    filteredEmployees.value = employees.value;
  } catch (err) {
    console.error('Failed to load employees', err);
  }
}

function filterEmployees() {
  const q = employeeSearch.value.toLowerCase().trim();
  if (!q) {
    filteredEmployees.value = employees.value;
    return;
  }
  filteredEmployees.value = employees.value.filter((emp) => {
    const name = (emp.full_name || `${emp.first_name || ''} ${emp.last_name || ''}`).toLowerCase();
    const code = String(emp.employee_code || '').toLowerCase();
    const id = String(emp.id || '');
    return name.includes(q) || code.includes(q) || id.includes(q);
  });
}

function selectEmployee(emp) {
  selectedEmployeeId.value = emp.id;
  employeeSearch.value = '';
  showEmployeeDropdown.value = false;
  loadCalendar();
}

watch([selectedMonth, selectedYear], () => {
  if (viewMode.value === 'calendar') {
    closeDayPopup();
    loadCalendar();
  }
});

// List view
const attendances = ref([]);
const listLoading = ref(false);
const listError = ref(null);
const pagination = ref(null);
const listFilters = ref({ date: '', status: '', search: '' });

async function loadAttendanceList(page = 1) {
  listLoading.value = true;
  listError.value = null;
  try {
    const response = await axios.get('/attendance', {
      params: {
        page,
        date: listFilters.value.date,
        status: listFilters.value.status,
        search: listFilters.value.search,
      },
    });
    attendances.value = response.data.data || [];
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
      total: response.data.total,
    };
  } catch (err) {
    listError.value = 'Failed to load attendance records';
  } finally {
    listLoading.value = false;
  }
}

function resetListFilters() {
  listFilters.value = { date: '', status: '', search: '' };
  loadAttendanceList();
}

watch(viewMode, (mode) => {
  if (mode === 'list') loadAttendanceList();
  else loadCalendar();
});

onMounted(() => {
  selectedEmployeeId.value = currentEmployeeId.value;
  loadCalendar();
  loadEmployees();

  document.addEventListener('click', (e) => {
    if (!e.target.closest?.('.relative')) showEmployeeDropdown.value = false;
  });
});
</script>
