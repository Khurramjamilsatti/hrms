<template>
  <div class="attendance-page relative min-h-[calc(100vh-4rem)] -m-6 p-4 md:p-8 overflow-hidden">
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-[#f7f4fc] via-[#fff8f8] to-[#f3f0fa]"></div>
    <div class="pointer-events-none absolute -top-24 -right-16 h-72 w-72 rounded-full bg-accent/10 blur-3xl"></div>
    <div class="pointer-events-none absolute top-40 -left-20 h-64 w-64 rounded-full bg-brand/5 blur-3xl"></div>

    <div class="relative max-w-6xl mx-auto">
      <!-- Header -->
      <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 mb-6">
        <div class="flex items-start gap-4">
          <div class="hidden sm:flex h-14 w-14 rounded-2xl bg-gradient-to-br from-brand to-brand-soft text-white items-center justify-center shadow-lg shadow-brand/20">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-accent mb-1">Workforce</p>
            <h1 class="text-3xl md:text-4xl font-bold text-brand tracking-tight">Attendance Report</h1>
            <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-ink-muted">
              <span class="inline-flex items-center gap-2 rounded-full bg-white/80 border border-surface-border px-3 py-1 shadow-sm">
                <span class="h-6 w-6 rounded-full bg-accent/15 text-accent text-xs font-bold inline-flex items-center justify-center">
                  {{ employeeInitials }}
                </span>
                <span class="font-medium text-ink">{{ calendarData?.employee?.full_name || 'Your attendance' }}</span>
              </span>
              <span v-if="calendarData?.employee?.employee_code" class="text-ink-muted">{{ calendarData.employee.employee_code }}</span>
              <span v-if="calendarData?.employee?.department" class="text-ink-muted">· {{ calendarData.employee.department }}</span>
            </div>
          </div>
        </div>

        <button
          v-if="canViewTeamRecords"
          @click="viewMode = viewMode === 'calendar' ? 'list' : 'calendar'"
          class="self-start lg:self-auto inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border border-surface-border bg-white/90 text-ink-soft hover:border-accent/40 hover:text-accent shadow-sm transition-all"
        >
          <svg v-if="viewMode === 'calendar'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          {{ viewMode === 'calendar' ? 'All Records' : 'Calendar View' }}
        </button>
      </div>

      <!-- Calendar Report -->
      <template v-if="viewMode === 'calendar'">
        <!-- Employee picker -->
        <div v-if="canSelectEmployee" class="mb-5 rounded-3xl border border-white/70 bg-white/80 backdrop-blur-sm shadow-[0_10px_40px_rgba(30,20,51,0.06)] p-4 md:p-5">
          <label class="block text-sm font-semibold text-ink mb-2">View employee calendar</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-muted">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input
              v-model="employeeSearch"
              @input="filterEmployees"
              @focus="showEmployeeDropdown = true"
              type="text"
              placeholder="Search by name or employee ID..."
              class="w-full pl-10 pr-4 py-3 border border-surface-border rounded-2xl bg-surface/60 focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent transition-shadow"
            />
            <div
              v-if="showEmployeeDropdown && filteredEmployees.length"
              class="absolute z-40 w-full mt-2 bg-white border border-surface-border rounded-2xl shadow-xl max-h-64 overflow-y-auto"
            >
              <button
                v-if="currentEmployeeId"
                type="button"
                @click="selectEmployee({ id: currentEmployeeId, full_name: 'Me (current user)', employee_code: '' })"
                class="w-full text-left px-4 py-3 hover:bg-accent/5 border-b border-surface-border"
              >
                <div class="text-sm font-semibold text-accent">Me (current user)</div>
              </button>
              <button
                v-for="emp in filteredEmployees"
                :key="emp.id"
                type="button"
                @click="selectEmployee(emp)"
                class="w-full text-left px-4 py-3 hover:bg-surface-muted border-b border-surface-border last:border-0"
              >
                <div class="text-sm font-semibold text-ink">{{ emp.full_name || `${emp.first_name || ''} ${emp.last_name || ''}`.trim() }}</div>
                <div class="text-xs text-ink-muted">{{ emp.employee_code }} · {{ emp.department?.name || 'N/A' }}</div>
              </button>
            </div>
          </div>
          <p v-if="selectedEmployeeLabel" class="mt-2 text-xs text-ink-muted">
            Showing <span class="font-semibold text-ink">{{ selectedEmployeeLabel }}</span>
          </p>
        </div>

        <!-- Month + filters + stats shell -->
        <div class="rounded-[28px] border border-white/80 bg-white/85 backdrop-blur-sm shadow-[0_18px_50px_rgba(30,20,51,0.08)] overflow-hidden mb-5">
          <!-- Month navigation -->
          <div class="px-4 md:px-6 py-4 md:py-5 flex items-center justify-between gap-3 bg-gradient-to-r from-brand via-brand-soft to-[#3a2a5c] text-white">
            <button
              type="button"
              @click="shiftMonth(-1)"
              class="w-11 h-11 rounded-2xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-colors"
              aria-label="Previous month"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <div class="text-center">
              <div class="text-xl md:text-2xl font-bold tracking-tight">{{ monthLabel }}</div>
              <div class="text-xs text-white/70 mt-0.5">Tap any day for details</div>
            </div>
            <button
              type="button"
              @click="shiftMonth(1)"
              class="w-11 h-11 rounded-2xl flex items-center justify-center bg-white/10 hover:bg-white/20 transition-colors"
              aria-label="Next month"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
          </div>

          <div class="p-4 md:p-6 space-y-5">
            <!-- Status filter chips -->
            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
              <button
                v-for="chip in statusChips"
                :key="chip.value"
                type="button"
                @click="statusFilter = chip.value"
                class="shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200"
                :class="statusFilter === chip.value
                  ? 'bg-accent text-white shadow-md shadow-accent/30 scale-[1.02]'
                  : 'bg-accent/8 text-accent hover:bg-accent/15'"
              >
                {{ chip.label }}
              </button>
            </div>

            <!-- Summary stats -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
              <div
                v-for="stat in summaryCards"
                :key="stat.key"
                class="rounded-2xl border p-3.5 transition-transform hover:-translate-y-0.5"
                :class="stat.cardClass"
              >
                <div class="flex items-center justify-between mb-2">
                  <span class="text-[11px] font-semibold uppercase tracking-wide" :class="stat.labelClass">{{ stat.label }}</span>
                  <span class="h-7 w-7 rounded-lg inline-flex items-center justify-center text-sm" :class="stat.iconClass">{{ stat.icon }}</span>
                </div>
                <div class="text-2xl font-bold tracking-tight" :class="stat.valueClass">{{ stat.value }}</div>
              </div>
            </div>

            <!-- Loading / error -->
            <div v-if="loading" class="rounded-3xl border border-surface-border bg-surface/70 p-14 text-center">
              <div class="mx-auto mb-3 h-10 w-10 rounded-full border-2 border-accent/30 border-t-accent animate-spin"></div>
              <div class="text-ink-muted font-medium">Loading attendance calendar...</div>
            </div>
            <div v-else-if="error" class="rounded-2xl border border-red-200 bg-red-50 text-red-700 px-4 py-3">
              {{ error }}
              <button @click="loadCalendar" class="ml-2 underline text-sm font-semibold">Retry</button>
            </div>

            <!-- Month grid calendar -->
            <div v-else class="rounded-3xl border border-surface-border bg-gradient-to-b from-white to-surface/40 p-3 md:p-5">
              <div class="grid grid-cols-7 gap-1.5 md:gap-2 mb-2">
                <div
                  v-for="day in weekDays"
                  :key="day"
                  class="text-center text-[11px] md:text-xs font-bold uppercase tracking-wider text-ink-muted py-2"
                >
                  {{ day }}
                </div>
              </div>
              <div class="grid grid-cols-7 gap-1.5 md:gap-2">
                <button
                  v-for="cell in calendarCells"
                  :key="cell.key"
                  type="button"
                  :disabled="!cell.inMonth"
                  @click="openDayPopup(cell)"
                  class="group relative min-h-[58px] md:min-h-[88px] rounded-2xl p-1.5 md:p-2.5 text-left transition-all duration-200 border"
                  :class="cellClass(cell)"
                >
                  <div class="flex items-start justify-between gap-1">
                    <span
                      class="inline-flex h-6 w-6 md:h-7 md:w-7 items-center justify-center rounded-full text-xs md:text-sm font-bold"
                      :class="dayNumberClass(cell)"
                    >
                      {{ cell.day }}
                    </span>
                    <span
                      v-if="cell.inMonth && cell.status && cell.status !== 'upcoming'"
                      class="mt-1 h-2 w-2 rounded-full shrink-0"
                      :class="statusDot(cell.status)"
                    ></span>
                  </div>
                  <div v-if="cell.inMonth && cell.status && cell.status !== 'upcoming'" class="mt-1.5 md:mt-2">
                    <div class="text-[10px] md:text-[11px] font-semibold leading-tight truncate" :class="statusTextClass(cell.status)">
                      {{ statusLabel(cell.status) }}
                    </div>
                    <div
                      v-if="cellTimes(cell)"
                      class="hidden md:block mt-1 text-[10px] text-ink-muted truncate opacity-80 group-hover:opacity-100"
                    >
                      {{ cellTimes(cell) }}
                    </div>
                  </div>
                </button>
              </div>

              <!-- Legend -->
              <div class="mt-4 pt-4 border-t border-surface-border flex flex-wrap gap-x-4 gap-y-2">
                <div v-for="item in legendItems" :key="item.status" class="inline-flex items-center gap-1.5 text-xs text-ink-muted">
                  <span class="h-2.5 w-2.5 rounded-full" :class="statusDot(item.status)"></span>
                  {{ item.label }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Day detail popup -->
        <Transition name="popup">
          <div
            v-if="showDayPopup && selectedDayDetail"
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-brand/40 backdrop-blur-[2px] p-0 sm:p-4"
            @click.self="closeDayPopup"
          >
            <div class="bg-white w-full sm:max-w-md sm:rounded-3xl rounded-t-3xl shadow-2xl overflow-hidden animate-popup">
              <div class="h-1.5 w-12 rounded-full bg-border mx-auto mt-3 sm:hidden"></div>
              <div class="px-5 pt-4 pb-4 bg-gradient-to-br from-brand to-brand-soft text-white">
                <div class="flex items-start justify-between gap-3">
                  <div>
                    <div class="text-xl font-bold tracking-tight">{{ formatDate(selectedDayDetail.date) }}</div>
                    <div class="text-sm text-white/75 mt-1">{{ calendarData?.employee?.full_name }}</div>
                  </div>
                  <button type="button" @click="closeDayPopup" class="rounded-xl bg-white/10 hover:bg-white/20 p-2 transition-colors" aria-label="Close">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                  </button>
                </div>
                <div class="mt-4">
                  <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-white/15 border border-white/20 capitalize">
                    {{ statusLabel(selectedDayDetail.status) }}
                  </span>
                </div>
              </div>

              <div class="px-5 py-5 space-y-4">
                <template v-if="selectedDayDetail.status === 'weekend'">
                  <div class="rounded-2xl bg-surface-muted px-4 py-5 text-center">
                    <div class="text-2xl mb-1">🌿</div>
                    <p class="text-sm text-ink-soft font-medium">Weekend — no workday attendance expected.</p>
                  </div>
                </template>
                <template v-else-if="selectedDayDetail.status === 'upcoming'">
                  <div class="rounded-2xl bg-surface-muted px-4 py-5 text-center">
                    <p class="text-sm text-ink-soft font-medium">This date is still upcoming. No attendance recorded yet.</p>
                  </div>
                </template>
                <template v-else>
                  <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 p-3.5">
                      <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 mb-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-emerald-100">
                          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </span>
                        Check-in
                      </div>
                      <div class="text-xl font-bold text-ink tracking-tight">{{ formatTime(selectedDayDetail.check_in) }}</div>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-rose-50 to-white border border-rose-100 p-3.5">
                      <div class="flex items-center gap-1.5 text-xs font-bold text-rose-600 mb-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-rose-100">
                          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        </span>
                        Check-out
                      </div>
                      <div class="text-xl font-bold text-ink tracking-tight">{{ formatTime(selectedDayDetail.check_out) }}</div>
                    </div>
                  </div>

                  <div class="rounded-2xl border border-surface-border bg-surface/60 divide-y divide-surface-border">
                    <div class="flex items-center justify-between px-4 py-3 text-sm">
                      <span class="text-ink-muted">Working hours</span>
                      <span class="font-bold text-ink">{{ selectedDayDetail.working_hours || 0 }}h</span>
                    </div>
                    <div v-if="selectedDayDetail.overtime_hours" class="flex items-center justify-between px-4 py-3 text-sm">
                      <span class="text-ink-muted">Overtime</span>
                      <span class="font-bold text-blue-600">+{{ selectedDayDetail.overtime_hours }}h</span>
                    </div>
                    <div v-if="selectedDayDetail.sessions_count > 1" class="flex items-center justify-between px-4 py-3 text-sm">
                      <span class="text-ink-muted">Sessions</span>
                      <span class="font-bold text-ink">{{ selectedDayDetail.sessions_count }}</span>
                    </div>
                  </div>

                  <div v-if="selectedDayDetail.remarks" class="text-sm">
                    <div class="text-ink-muted mb-1.5 font-medium">Remarks</div>
                    <p class="text-ink bg-surface-muted rounded-2xl px-4 py-3 leading-relaxed">{{ selectedDayDetail.remarks }}</p>
                  </div>

                  <div v-if="selectedDayDetail.sessions?.length > 1" class="space-y-2">
                    <div class="text-xs font-bold text-ink-muted uppercase tracking-wide">Sessions</div>
                    <div
                      v-for="(session, index) in selectedDayDetail.sessions"
                      :key="session.id || index"
                      class="flex items-center justify-between text-sm bg-surface-muted rounded-xl px-3.5 py-2.5"
                    >
                      <span class="text-ink-soft">#{{ index + 1 }} · {{ formatTime(session.check_in) }} – {{ formatTime(session.check_out) }}</span>
                      <span class="font-semibold text-ink">{{ session.working_hours || 0 }}h</span>
                    </div>
                  </div>
                </template>
              </div>

              <div class="px-5 py-4 bg-surface/80 border-t border-surface-border">
                <button
                  type="button"
                  @click="closeDayPopup"
                  class="w-full px-4 py-3 text-sm font-bold text-white bg-accent hover:bg-accent-dark rounded-2xl shadow-lg shadow-accent/25 transition-colors"
                >
                  Close
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </template>

      <!-- Legacy all-records list for team/HR -->
      <template v-else>
        <div class="bg-white/90 rounded-3xl shadow-sm border border-surface-border p-4 mb-5">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-semibold text-ink mb-2">Date</label>
              <input v-model="listFilters.date" type="date" class="w-full px-4 py-2.5 border border-surface-border rounded-xl focus:outline-none focus:ring-2 focus:ring-accent/40" @change="loadAttendanceList" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-ink mb-2">Status</label>
              <select v-model="listFilters.status" class="w-full px-4 py-2.5 border border-surface-border rounded-xl focus:outline-none focus:ring-2 focus:ring-accent/40" @change="loadAttendanceList">
                <option value="">All Status</option>
                <option value="present">Present</option>
                <option value="late">Late</option>
                <option value="half_day">Half Day</option>
                <option value="on_leave">On Leave</option>
                <option value="absent">Absent</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-ink mb-2">Search Employee</label>
              <input v-model="listFilters.search" type="text" placeholder="Search by name or code..." class="w-full px-4 py-2.5 border border-surface-border rounded-xl focus:outline-none focus:ring-2 focus:ring-accent/40" @input="loadAttendanceList" />
            </div>
            <div class="flex items-end">
              <button @click="resetListFilters" class="w-full px-4 py-2.5 bg-accent hover:bg-accent-dark text-white font-semibold rounded-xl transition-colors">Reset Filters</button>
            </div>
          </div>
        </div>

        <div v-if="listLoading" class="bg-white rounded-3xl border border-surface-border p-12 text-center text-ink-muted">Loading attendance records...</div>
        <div v-else-if="listError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl">{{ listError }}</div>
        <div v-else class="bg-white rounded-3xl shadow-sm border border-surface-border overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-surface-muted border-b border-surface-border">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-bold text-ink-muted uppercase tracking-wider">Date</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-ink-muted uppercase tracking-wider">Employee</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-ink-muted uppercase tracking-wider">Sessions</th>
                  <th class="px-6 py-4 text-left text-xs font-bold text-ink-muted uppercase tracking-wider">Total Hours</th>
                  <th class="px-6 py-4 text-center text-xs font-bold text-ink-muted uppercase tracking-wider">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-surface-border">
                <tr v-for="row in attendances" :key="row.id" class="hover:bg-surface-muted/60">
                  <td class="px-6 py-4 text-sm font-semibold text-ink">{{ formatDate(row.date) }}</td>
                  <td class="px-6 py-4">
                    <div class="text-sm font-semibold text-ink">{{ row.employee?.full_name }}</div>
                    <div class="text-xs text-ink-muted">{{ row.employee?.employee_code }}</div>
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
          <div v-if="pagination" class="px-6 py-4 bg-surface-muted border-t border-surface-border flex items-center justify-between">
            <div class="text-sm text-ink-muted">{{ pagination.total }} records</div>
            <div class="flex gap-2">
              <button @click="loadAttendanceList(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-4 py-2 text-sm rounded-xl disabled:opacity-50 bg-accent text-white font-semibold">Previous</button>
              <button @click="loadAttendanceList(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-4 py-2 text-sm rounded-xl disabled:opacity-50 bg-accent text-white font-semibold">Next</button>
            </div>
          </div>
        </div>
      </template>
    </div>
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

const employeeInitials = computed(() => {
  const name = calendarData.value?.employee?.full_name || authStore.user?.name || 'A';
  return name.split(' ').filter(Boolean).map((p) => p[0]).join('').slice(0, 2).toUpperCase();
});

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

const legendItems = [
  { status: 'present', label: 'Present' },
  { status: 'late', label: 'Late' },
  { status: 'absent', label: 'Absent' },
  { status: 'half_day', label: 'Half day' },
  { status: 'on_leave', label: 'Leave' },
  { status: 'weekend', label: 'Weekend' },
];

const summary = computed(() => ({
  present: calendarData.value?.summary?.present ?? 0,
  absent: calendarData.value?.summary?.absent ?? 0,
  late: calendarData.value?.summary?.late ?? 0,
  half_day: calendarData.value?.summary?.half_day ?? 0,
  on_leave: calendarData.value?.summary?.on_leave ?? 0,
  working_hours: calendarData.value?.summary?.working_hours ?? 0,
}));

const summaryCards = computed(() => [
  { key: 'present', label: 'Present', value: summary.value.present, icon: '✓', cardClass: 'bg-emerald-50/80 border-emerald-100', labelClass: 'text-emerald-700/80', valueClass: 'text-emerald-700', iconClass: 'bg-emerald-100 text-emerald-700' },
  { key: 'absent', label: 'Absent', value: summary.value.absent, icon: '✕', cardClass: 'bg-rose-50/80 border-rose-100', labelClass: 'text-rose-700/80', valueClass: 'text-rose-600', iconClass: 'bg-rose-100 text-rose-600' },
  { key: 'late', label: 'Late', value: summary.value.late, icon: '⏱', cardClass: 'bg-amber-50/80 border-amber-100', labelClass: 'text-amber-700/80', valueClass: 'text-amber-600', iconClass: 'bg-amber-100 text-amber-600' },
  { key: 'half', label: 'Half', value: summary.value.half_day, icon: '½', cardClass: 'bg-yellow-50/80 border-yellow-100', labelClass: 'text-yellow-700/80', valueClass: 'text-yellow-700', iconClass: 'bg-yellow-100 text-yellow-700' },
  { key: 'leave', label: 'Leave', value: summary.value.on_leave, icon: '✈', cardClass: 'bg-indigo-50/80 border-indigo-100', labelClass: 'text-indigo-700/80', valueClass: 'text-indigo-600', iconClass: 'bg-indigo-100 text-indigo-600' },
  { key: 'hrs', label: 'Hrs', value: summary.value.working_hours, icon: '⌛', cardClass: 'bg-accent/5 border-accent/15', labelClass: 'text-accent/80', valueClass: 'text-accent', iconClass: 'bg-accent/15 text-accent' },
]);

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
    check_in: dayData?.check_in || null,
    check_out: dayData?.check_out || null,
  };
}

function cellTimes(cell) {
  if (!cell.check_in && !cell.check_out) return '';
  return `${formatTime(cell.check_in)} · ${formatTime(cell.check_out)}`;
}

function cellClass(cell) {
  if (!cell.inMonth) return 'border-transparent bg-transparent cursor-default';
  const dimmed = statusFilter.value && cell.status !== statusFilter.value;
  const base = `hover:-translate-y-0.5 hover:shadow-md cursor-pointer ${dimmed ? 'opacity-30' : ''}`;
  if (selectedDayDetail.value?.date === cell.date && showDayPopup.value) {
    return `${base} bg-accent/10 border-accent ring-2 ring-accent/30 shadow-md`;
  }
  if (cell.isToday) return `${base} bg-white border-brand/30 ring-2 ring-brand/15 shadow-sm`;
  if (cell.status === 'absent') return `${base} bg-rose-50/90 border-rose-100`;
  if (cell.status === 'late') return `${base} bg-amber-50/90 border-amber-100`;
  if (cell.status === 'present') return `${base} bg-emerald-50/90 border-emerald-100`;
  if (cell.status === 'on_leave') return `${base} bg-indigo-50/90 border-indigo-100`;
  if (cell.status === 'half_day') return `${base} bg-yellow-50/90 border-yellow-100`;
  if (cell.status === 'weekend') return `${base} bg-slate-50 border-slate-100`;
  return `${base} bg-white border-surface-border`;
}

function dayNumberClass(cell) {
  if (!cell.inMonth) return 'text-gray-300';
  if (cell.isToday) return 'bg-brand text-white shadow-sm';
  if (cell.status === 'absent') return 'text-rose-700';
  if (cell.status === 'present') return 'text-emerald-700';
  if (cell.status === 'late') return 'text-amber-700';
  if (cell.status === 'on_leave') return 'text-indigo-700';
  return 'text-ink';
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
    present: 'bg-emerald-500',
    late: 'bg-amber-500',
    absent: 'bg-rose-500',
    half_day: 'bg-yellow-500',
    on_leave: 'bg-indigo-500',
    weekend: 'bg-slate-400',
    upcoming: 'bg-slate-300',
  }[status] || 'bg-slate-300';
}

function statusBadgeClass(status) {
  return {
    present: 'bg-emerald-100 text-emerald-700',
    late: 'bg-amber-100 text-amber-700',
    absent: 'bg-rose-100 text-rose-700',
    half_day: 'bg-yellow-100 text-yellow-700',
    on_leave: 'bg-indigo-100 text-indigo-700',
    weekend: 'bg-slate-100 text-slate-600',
    upcoming: 'bg-slate-50 text-slate-400',
  }[status] || 'bg-slate-100 text-slate-600';
}

function statusTextClass(status) {
  return {
    present: 'text-emerald-700',
    late: 'text-amber-700',
    absent: 'text-rose-600',
    half_day: 'text-yellow-700',
    on_leave: 'text-indigo-700',
    weekend: 'text-slate-500',
  }[status] || 'text-ink-muted';
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
  return new Date(`${date}T00:00:00`).toLocaleDateString('en-US', {
    weekday: 'long', year: 'numeric', month: 'short', day: 'numeric',
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

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.popup-enter-active,
.popup-leave-active {
  transition: opacity 0.2s ease;
}
.popup-enter-from,
.popup-leave-to {
  opacity: 0;
}
.popup-enter-active .animate-popup,
.popup-leave-active .animate-popup {
  transition: transform 0.22s ease, opacity 0.22s ease;
}
.popup-enter-from .animate-popup,
.popup-leave-to .animate-popup {
  opacity: 0;
  transform: translateY(16px) scale(0.98);
}
</style>
