<template>
  <div class="p-6 space-y-6">
    <div v-if="loading" class="flex items-center justify-center py-24">
      <div class="text-center">
        <div class="inline-block h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-gray-900 mb-3"></div>
        <p class="text-sm text-gray-600">Loading dashboard...</p>
      </div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-lg">
      {{ error }}
    </div>

    <div v-else>
      <!-- Admin / HR / Super Admin -->
      <div v-if="isAdmin" class="space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
          <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ todayLabel }}</p>
            <h1 class="text-3xl font-bold text-gray-900 mt-1">{{ greeting }}, {{ displayName }}</h1>
            <p class="text-sm text-gray-500 mt-1">Organization overview and today’s workforce status</p>
          </div>
          <button
            @click="refreshDashboard"
            :disabled="loading"
            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
          >
            <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Refresh
          </button>
        </div>

        <!-- Personal check-in for linked employee profiles -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <h2 class="text-base font-semibold text-gray-900">My Attendance</h2>
              <p v-if="hasEmployeeProfile" class="text-sm text-gray-500 mt-0.5">
                <template v-if="stats?.my_attendance_today">
                  Checked in at <span class="font-medium text-gray-900">{{ stats.my_attendance_today.check_in }}</span>
                  · Session {{ calculateDuration(stats.my_attendance_today.check_in) }}
                </template>
                <template v-else>You are not checked in yet today.</template>
              </p>
              <p v-else class="text-sm text-amber-700 mt-0.5">
                No employee profile is linked to this account, so personal check-in is unavailable.
              </p>
            </div>
            <div v-if="hasEmployeeProfile" class="flex items-center gap-3">
              <button
                v-if="stats?.my_attendance_today"
                @click="handleCheckOut"
                :disabled="processingAttendance"
                class="px-5 py-2.5 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50"
              >
                {{ processingAttendance ? 'Processing...' : 'Check Out' }}
              </button>
              <button
                v-else
                @click="handleCheckIn"
                :disabled="processingAttendance"
                class="px-5 py-2.5 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50"
              >
                {{ processingAttendance ? 'Processing...' : 'Check In' }}
              </button>
            </div>
          </div>
        </div>

        <!-- KPI strip -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Active Employees</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats?.total_employees || 0) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ stats?.recent_hires || 0 }} hired in last 30 days</p>
          </div>
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Present Today</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats?.present_today || 0) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ attendanceRate }}% attendance rate</p>
          </div>
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Absent Today</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats?.absent_today || 0) }}</p>
            <p class="text-xs text-gray-500 mt-2">Needs follow-up</p>
          </div>
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">On Leave</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats?.on_leave_today || 0) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ stats?.pending_leave_requests || 0 }} pending requests</p>
          </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
          <!-- Attendance trend -->
          <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
              <div>
                <h3 class="text-base font-semibold text-gray-900">Attendance · Last 7 Days</h3>
                <p class="text-sm text-gray-500">Present, absent, and leave volume</p>
              </div>
              <div class="hidden sm:flex items-center gap-4 text-xs text-gray-500">
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Present</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>Absent</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>Leave</span>
              </div>
            </div>
            <div class="space-y-3">
              <div v-for="(day, index) in stats?.attendance_trend" :key="index" class="flex items-center gap-3">
                <div class="w-10 text-xs font-medium text-gray-600">{{ day.day }}</div>
                <div class="flex-1 h-8 bg-gray-100 rounded-md overflow-hidden flex">
                  <div
                    v-if="day.present > 0"
                    class="bg-emerald-500 text-white text-[10px] font-medium flex items-center justify-center"
                    :style="{ width: barWidth(day.present, day.total) }"
                  >{{ day.present }}</div>
                  <div
                    v-if="day.absent > 0"
                    class="bg-rose-500 text-white text-[10px] font-medium flex items-center justify-center"
                    :style="{ width: barWidth(day.absent, day.total) }"
                  >{{ day.absent }}</div>
                  <div
                    v-if="day.on_leave > 0"
                    class="bg-amber-500 text-white text-[10px] font-medium flex items-center justify-center"
                    :style="{ width: barWidth(day.on_leave, day.total) }"
                  >{{ day.on_leave }}</div>
                </div>
                <div class="w-10 text-right text-xs font-semibold text-gray-700">{{ day.total }}</div>
              </div>
              <p v-if="!stats?.attendance_trend?.length" class="text-sm text-gray-500 py-8 text-center">No attendance data for the last 7 days</p>
            </div>
          </div>

          <!-- Payroll snapshot -->
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900">Payroll Snapshot</h3>
            <p class="text-sm text-gray-500 mb-5">Current month</p>
            <div class="space-y-4">
              <div class="flex justify-between items-baseline">
                <span class="text-sm text-gray-600">Net payroll</span>
                <span class="text-lg font-bold text-gray-900">Rs. {{ formatNumber(stats?.payroll_stats?.total_payroll_current_month || 0) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Paid</span>
                <span class="font-semibold text-gray-900">{{ stats?.payroll_stats?.processed_payrolls || 0 }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Draft</span>
                <span class="font-semibold text-gray-900">{{ stats?.payroll_stats?.pending_payrolls || 0 }}</span>
              </div>
              <div class="flex justify-between text-sm pt-3 border-t border-gray-100">
                <span class="text-gray-600">Pending OT</span>
                <span class="font-semibold text-gray-900">{{ stats?.pending_overtime_requests || 0 }}</span>
              </div>
              <router-link to="/payroll" class="block mt-2 text-center text-sm font-medium text-gray-900 border border-gray-300 rounded-lg py-2.5 hover:bg-gray-50">
                Open Payroll
              </router-link>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Departments -->
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
              <div>
                <h3 class="text-base font-semibold text-gray-900">Departments</h3>
                <p class="text-sm text-gray-500">{{ stats?.departments || 0 }} active</p>
              </div>
            </div>
            <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
              <div v-for="(dept, index) in stats?.department_stats" :key="index">
                <div class="flex justify-between text-sm mb-1">
                  <span class="text-gray-700">{{ dept.name }}</span>
                  <span class="font-semibold text-gray-900">{{ dept.employee_count }}</span>
                </div>
                <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                  <div class="h-full bg-gray-800 rounded-full" :style="{ width: `${(dept.employee_count / (stats.total_employees || 1)) * 100}%` }"></div>
                </div>
              </div>
              <p v-if="!stats?.department_stats?.length" class="text-sm text-gray-500 text-center py-6">No department data</p>
            </div>
          </div>

          <!-- Pending leaves -->
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
              <div>
                <h3 class="text-base font-semibold text-gray-900">Pending Leave Approvals</h3>
                <p class="text-sm text-gray-500">Latest requests awaiting action</p>
              </div>
              <router-link to="/leaves" class="text-sm font-medium text-gray-700 hover:text-gray-900">View all</router-link>
            </div>
            <div class="space-y-3">
              <div
                v-for="leave in stats?.recent_leaves"
                :key="leave.id"
                class="flex items-start justify-between gap-3 p-3 rounded-lg border border-gray-100 bg-gray-50"
              >
                <div>
                  <p class="text-sm font-semibold text-gray-900">{{ leave.employee?.first_name }} {{ leave.employee?.last_name }}</p>
                  <p class="text-xs text-gray-500 mt-0.5">{{ leave.leave_type?.name }} · {{ leave.total_days }} day(s)</p>
                  <p class="text-xs text-gray-500">{{ formatDate(leave.start_date) }} – {{ formatDate(leave.end_date) }}</p>
                </div>
                <span class="shrink-0 px-2 py-1 text-[11px] font-medium rounded bg-amber-100 text-amber-800">Pending</span>
              </div>
              <p v-if="!stats?.recent_leaves?.length" class="text-sm text-gray-500 text-center py-8">No pending leave requests</p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Recent hires -->
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Recent Joinees</h3>
            <div class="space-y-3">
              <div v-for="emp in stats?.recent_employees" :key="emp.id" class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-700">
                  {{ (emp.first_name?.[0] || '') + (emp.last_name?.[0] || '') }}
                </div>
                <div class="min-w-0">
                  <p class="text-sm font-medium text-gray-900 truncate">{{ emp.first_name }} {{ emp.last_name }}</p>
                  <p class="text-xs text-gray-500 truncate">{{ emp.department?.name || '—' }} · {{ formatDate(emp.joining_date) }}</p>
                </div>
              </div>
              <p v-if="!stats?.recent_employees?.length" class="text-sm text-gray-500 text-center py-6">No recent hires</p>
            </div>
          </div>

          <!-- Birthdays -->
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Upcoming Birthdays</h3>
            <div class="space-y-3">
              <div v-for="emp in stats?.upcoming_birthdays" :key="emp.id" class="flex justify-between text-sm">
                <span class="text-gray-800">{{ emp.first_name }} {{ emp.last_name }}</span>
                <span class="text-gray-500">{{ formatBirthday(emp.date_of_birth) }}</span>
              </div>
              <p v-if="!stats?.upcoming_birthdays?.length" class="text-sm text-gray-500 text-center py-6">No birthdays in the next 30 days</p>
            </div>
          </div>

          <!-- Quick actions -->
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 gap-2">
              <router-link to="/employees/create" class="px-4 py-2.5 text-sm font-medium text-gray-800 border border-gray-200 rounded-lg hover:bg-gray-50">Add Employee</router-link>
              <router-link to="/attendance" class="px-4 py-2.5 text-sm font-medium text-gray-800 border border-gray-200 rounded-lg hover:bg-gray-50">Attendance</router-link>
              <router-link to="/payroll" class="px-4 py-2.5 text-sm font-medium text-gray-800 border border-gray-200 rounded-lg hover:bg-gray-50">Generate Payroll</router-link>
              <router-link to="/leaves" class="px-4 py-2.5 text-sm font-medium text-gray-800 border border-gray-200 rounded-lg hover:bg-gray-50">Leave Approvals</router-link>
            </div>
          </div>
        </div>

        <!-- Announcements -->
        <div v-if="stats?.announcements?.length" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
          <h3 class="text-base font-semibold text-gray-900 mb-4">Announcements</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div v-for="item in stats.announcements" :key="item.id" class="p-4 border border-gray-100 rounded-lg bg-gray-50">
              <p class="text-sm font-semibold text-gray-900">{{ item.title }}</p>
              <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ item.content }}</p>
              <p class="text-xs text-gray-400 mt-2">{{ formatDate(item.created_at) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Manager / Section Head — team overview only (no org admin actions) -->
      <div v-else-if="isManager" class="space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
          <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ todayLabel }}</p>
            <h1 class="text-3xl font-bold text-gray-900 mt-1">{{ greeting }}, {{ displayName }}</h1>
            <p class="text-sm text-gray-500 mt-1">Your team overview and today’s status</p>
          </div>
          <button
            @click="refreshDashboard"
            :disabled="loading"
            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
          >
            Refresh
          </button>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <h2 class="text-base font-semibold text-gray-900">My Attendance</h2>
              <p v-if="hasEmployeeProfile" class="text-sm text-gray-500 mt-0.5">
                <template v-if="stats?.my_attendance_today">
                  Checked in at <span class="font-medium text-gray-900">{{ stats.my_attendance_today.check_in }}</span>
                  · Session {{ calculateDuration(stats.my_attendance_today.check_in) }}
                </template>
                <template v-else>You are not checked in yet today.</template>
              </p>
              <p v-else class="text-sm text-amber-700 mt-0.5">No employee profile is linked to this account.</p>
            </div>
            <div v-if="hasEmployeeProfile" class="flex items-center gap-3">
              <button
                v-if="stats?.my_attendance_today"
                @click="handleCheckOut"
                :disabled="processingAttendance"
                class="px-5 py-2.5 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50"
              >
                {{ processingAttendance ? 'Processing...' : 'Check Out' }}
              </button>
              <button
                v-else
                @click="handleCheckIn"
                :disabled="processingAttendance"
                class="px-5 py-2.5 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50"
              >
                {{ processingAttendance ? 'Processing...' : 'Check In' }}
              </button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Team Members</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats?.total_team_members || stats?.total_employees || 0) }}</p>
          </div>
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Present Today</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats?.present_today || 0) }}</p>
          </div>
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Absent Today</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats?.absent_today || 0) }}</p>
          </div>
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">On Leave</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatNumber(stats?.on_leave_today || 0) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ stats?.pending_leave_requests || 0 }} pending requests</p>
          </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
          <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-5">Team Attendance · Last 7 Days</h3>
            <div class="space-y-3">
              <div v-for="(day, index) in stats?.attendance_trend" :key="index" class="flex items-center gap-3">
                <div class="w-10 text-xs font-medium text-gray-600">{{ day.day }}</div>
                <div class="flex-1 h-8 bg-gray-100 rounded-md overflow-hidden flex">
                  <div v-if="day.present > 0" class="bg-emerald-500 text-white text-[10px] font-medium flex items-center justify-center" :style="{ width: barWidth(day.present, day.total) }">{{ day.present }}</div>
                  <div v-if="day.absent > 0" class="bg-rose-500 text-white text-[10px] font-medium flex items-center justify-center" :style="{ width: barWidth(day.absent, day.total) }">{{ day.absent }}</div>
                  <div v-if="day.on_leave > 0" class="bg-amber-500 text-white text-[10px] font-medium flex items-center justify-center" :style="{ width: barWidth(day.on_leave, day.total) }">{{ day.on_leave }}</div>
                </div>
                <div class="w-10 text-right text-xs font-semibold text-gray-700">{{ day.total }}</div>
              </div>
              <p v-if="!stats?.attendance_trend?.length" class="text-sm text-gray-500 py-8 text-center">No attendance data</p>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 gap-2">
              <router-link to="/attendance" class="px-4 py-2.5 text-sm font-medium text-gray-800 border border-gray-200 rounded-lg hover:bg-gray-50">Team Attendance</router-link>
              <router-link to="/leaves" class="px-4 py-2.5 text-sm font-medium text-gray-800 border border-gray-200 rounded-lg hover:bg-gray-50">Leave Approvals</router-link>
              <router-link to="/employees" class="px-4 py-2.5 text-sm font-medium text-gray-800 border border-gray-200 rounded-lg hover:bg-gray-50">My Team</router-link>
              <router-link to="/files" class="px-4 py-2.5 text-sm font-medium text-gray-800 border border-gray-200 rounded-lg hover:bg-gray-50">My Files</router-link>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
              <h3 class="text-base font-semibold text-gray-900">Pending Leave Approvals</h3>
              <router-link to="/leaves" class="text-sm font-medium text-gray-700 hover:text-gray-900">View all</router-link>
            </div>
            <div class="space-y-3">
              <div v-for="leave in stats?.recent_leaves" :key="leave.id" class="flex items-start justify-between gap-3 p-3 rounded-lg border border-gray-100 bg-gray-50">
                <div>
                  <p class="text-sm font-semibold text-gray-900">{{ leave.employee?.first_name }} {{ leave.employee?.last_name }}</p>
                  <p class="text-xs text-gray-500 mt-0.5">{{ leave.leave_type?.name }} · {{ leave.total_days }} day(s)</p>
                </div>
                <span class="shrink-0 px-2 py-1 text-[11px] font-medium rounded bg-amber-100 text-amber-800">Pending</span>
              </div>
              <p v-if="!stats?.recent_leaves?.length" class="text-sm text-gray-500 text-center py-8">No pending leave requests</p>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Team Members</h3>
            <div class="space-y-3 max-h-72 overflow-y-auto">
              <div v-for="emp in stats?.team_members" :key="emp.id" class="flex items-center justify-between text-sm p-2 rounded-lg hover:bg-gray-50">
                <span class="font-medium text-gray-900">{{ emp.first_name }} {{ emp.last_name }}</span>
                <span class="text-gray-500">{{ emp.designation?.title || emp.department?.name || '—' }}</span>
              </div>
              <p v-if="!stats?.team_members?.length" class="text-sm text-gray-500 text-center py-8">No team members assigned</p>
            </div>
          </div>
        </div>

        <div v-if="stats?.announcements?.length" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
          <h3 class="text-base font-semibold text-gray-900 mb-4">Announcements</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div v-for="item in stats.announcements" :key="item.id" class="p-4 border border-gray-100 rounded-lg bg-gray-50">
              <p class="text-sm font-semibold text-gray-900">{{ item.title }}</p>
              <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ item.content }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Employee -->
      <div v-else class="space-y-6">
        <div>
          <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">{{ todayLabel }}</p>
          <h1 class="text-3xl font-bold text-gray-900 mt-1">{{ greeting }}, {{ displayName }}</h1>
          <p class="text-sm text-gray-500 mt-1">Your workday at a glance</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <h2 class="text-base font-semibold text-gray-900">Today’s Attendance</h2>
              <p class="text-sm text-gray-500 mt-1">
                <template v-if="stats?.my_attendance_today">
                  Checked in at <span class="font-medium text-gray-900">{{ stats.my_attendance_today.check_in }}</span>
                  · {{ calculateDuration(stats.my_attendance_today.check_in) }}
                </template>
                <template v-else>Ready when you are — check in to start your day.</template>
              </p>
            </div>
            <button
              v-if="stats?.my_attendance_today"
              @click="handleCheckOut"
              :disabled="processingAttendance"
              class="px-6 py-3 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50"
            >
              {{ processingAttendance ? 'Processing...' : 'Check Out' }}
            </button>
            <button
              v-else
              @click="handleCheckIn"
              :disabled="processingAttendance"
              class="px-6 py-3 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50"
            >
              {{ processingAttendance ? 'Processing...' : 'Check In' }}
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold">Present Days</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats?.my_attendance_summary?.present_days || 0 }}</p>
          </div>
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold">Absent Days</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats?.my_attendance_summary?.absent_days || 0 }}</p>
          </div>
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold">Leave Days</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats?.my_attendance_summary?.leave_days || 0 }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Leave Balance</h3>
            <div v-if="stats?.my_leave_balance?.length" class="space-y-3">
              <div v-for="balance in stats.my_leave_balance" :key="balance.id" class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                <div class="flex justify-between text-sm mb-2">
                  <span class="font-medium text-gray-900">{{ balance.leave_type?.name }}</span>
                  <span class="font-bold text-gray-900">{{ balance.remaining_days }} left</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div class="h-full bg-gray-800 rounded-full" :style="{ width: `${Math.min(100, (balance.remaining_days / (balance.total_days || 1)) * 100)}%` }"></div>
                </div>
              </div>
            </div>
            <p v-else class="text-sm text-gray-500 text-center py-8">No leave balance available</p>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-base font-semibold text-gray-900">My Leave Applications</h3>
              <router-link to="/leaves" class="text-sm text-gray-600 hover:text-gray-900">View all</router-link>
            </div>
            <div v-if="stats?.my_pending_leaves?.length" class="space-y-3">
              <div v-for="leave in stats.my_pending_leaves" :key="leave.id" class="p-3 border border-amber-100 bg-amber-50 rounded-lg">
                <div class="flex justify-between">
                  <span class="text-sm font-semibold text-gray-900">{{ leave.leave_type?.name }}</span>
                  <span class="text-xs px-2 py-0.5 bg-amber-200 text-amber-900 rounded">Pending</span>
                </div>
                <p class="text-xs text-gray-600 mt-1">{{ formatDate(leave.start_date) }} – {{ formatDate(leave.end_date) }} · {{ leave.total_days }} day(s)</p>
              </div>
            </div>
            <p v-else class="text-sm text-gray-500 text-center py-8">No pending applications</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Recent Attendance</h3>
            <div v-if="stats?.my_recent_attendance?.length" class="space-y-2">
              <div v-for="att in stats.my_recent_attendance" :key="att.id" class="flex justify-between items-center p-3 border border-gray-100 rounded-lg">
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ formatDate(att.date) }}</p>
                  <p class="text-xs text-gray-500">{{ att.check_in || '--' }} – {{ att.check_out || '--' }}</p>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded capitalize"
                  :class="{
                    'bg-emerald-100 text-emerald-800': att.status === 'present',
                    'bg-amber-100 text-amber-800': att.status === 'late' || att.status === 'on_leave',
                    'bg-rose-100 text-rose-800': att.status === 'absent',
                    'bg-gray-100 text-gray-700': !['present','late','on_leave','absent'].includes(att.status)
                  }">{{ att.status }}</span>
              </div>
            </div>
            <p v-else class="text-sm text-gray-500 text-center py-8">No recent attendance</p>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Upcoming Leaves</h3>
            <div v-if="stats?.my_upcoming_leaves?.length" class="space-y-3">
              <div v-for="leave in stats.my_upcoming_leaves" :key="leave.id" class="p-3 border border-gray-100 rounded-lg">
                <p class="text-sm font-semibold text-gray-900">{{ leave.leave_type?.name }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ formatDate(leave.start_date) }} – {{ formatDate(leave.end_date) }}</p>
              </div>
            </div>
            <p v-else class="text-sm text-gray-500 text-center py-8">No upcoming leaves</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useDashboardStore } from '@/stores/dashboard';
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';

const route = useRoute();
const authStore = useAuthStore();
const dashboardStore = useDashboardStore();
const { error: showError, success: showSuccess } = useNotification();

const stats = ref(null);
const loading = ref(true);
const error = ref(null);
const processingAttendance = ref(false);

const isAdmin = computed(() => authStore.isAdmin);
const isManager = computed(() => authStore.isManager);
const hasEmployeeProfile = computed(() => !!(authStore.user?.employee || stats.value?.has_employee_profile));

const displayName = computed(() => {
  const u = authStore.user;
  if (!u) return 'there';
  return u.name?.split(' ')[0] || u.email?.split('@')[0] || 'there';
});

const greeting = computed(() => {
  const hour = new Date().getHours();
  if (hour < 12) return 'Good morning';
  if (hour < 17) return 'Good afternoon';
  return 'Good evening';
});

const todayLabel = computed(() =>
  new Date().toLocaleDateString('en-PK', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
);

const attendanceRate = computed(() => {
  if (!stats.value?.total_employees) return 0;
  return Math.round((stats.value.present_today / stats.value.total_employees) * 100);
});

const barWidth = (value, total) => {
  if (!total) return '0%';
  return `${Math.max(4, (value / total) * 100)}%`;
};

const formatNumber = (num) => new Intl.NumberFormat('en-PK').format(num || 0);

const formatDate = (date) => {
  if (!date) return '—';
  return new Date(date).toLocaleDateString('en-PK', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const formatBirthday = (date) => {
  if (!date) return '—';
  return new Date(date).toLocaleDateString('en-PK', { month: 'short', day: 'numeric' });
};

const calculateDuration = (checkInTime) => {
  if (!checkInTime) return '0h 0m';
  const now = new Date();
  const [hours, minutes] = String(checkInTime).split(':');
  const checkIn = new Date();
  checkIn.setHours(parseInt(hours, 10), parseInt(minutes || '0', 10), 0);
  const diffMs = Math.max(0, now - checkIn);
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
  const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
  return `${diffHours}h ${diffMinutes}m`;
};

const handleCheckIn = async () => {
  processingAttendance.value = true;
  try {
    await axios.post('/attendance/check-in');
    showSuccess('Checked in successfully');
    await refreshDashboard();
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to check in. Please try again.');
  } finally {
    processingAttendance.value = false;
  }
};

const handleCheckOut = async () => {
  processingAttendance.value = true;
  try {
    await axios.post('/attendance/check-out');
    showSuccess('Checked out successfully');
    await refreshDashboard();
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to check out. Please try again.');
  } finally {
    processingAttendance.value = false;
  }
};

const refreshDashboard = async () => {
  loading.value = true;
  error.value = null;
  try {
    dashboardStore.$reset();
    stats.value = await dashboardStore.fetchDashboardData();
    // Keep auth employee state fresh for check-in visibility
    if (!authStore.user?.employee) {
      try {
        await authStore.fetchUser();
      } catch (_) {
        /* ignore */
      }
    }
  } catch (err) {
    error.value = 'Failed to load dashboard data. Please try again.';
    console.error('Dashboard error:', err);
  } finally {
    loading.value = false;
  }
};

watch(
  () => route.query.denied,
  (deniedModule) => {
    if (deniedModule) {
      showError(route.query.message || 'You do not have permission to access this module');
    }
  },
  { immediate: true }
);

onMounted(async () => {
  await refreshDashboard();
});
</script>
