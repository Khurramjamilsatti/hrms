<template>
  <div class="p-6 space-y-6">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-20">
      <div class="text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-blue-600 mb-4"></div>
        <p class="text-gray-600 font-medium">Loading dashboard...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg shadow-sm">
      <div class="flex items-center">
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium">{{ error }}</span>
      </div>
    </div>

    <!-- Admin/Manager Dashboard -->
    <div v-else>
      <div v-if="isAdmin || isManager">
        <!-- Page Header -->
        <div class="mb-6 flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-1">Welcome back! Here's what's happening today.</p>
          </div>
          <button 
            @click="refreshDashboard" 
            :disabled="loading"
            class="flex items-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-5 h-5" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span>Refresh</span>
          </button>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
          <!-- Total Employees -->
          <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-3">
              <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-blue-100 text-sm font-medium">Total Employees</p>
                <h3 class="text-4xl font-bold">{{ stats?.total_employees || 0 }}</h3>
              </div>
            </div>
            <div class="mt-4 pt-4 border-t border-blue-400 border-opacity-30">
              <p class="text-sm text-blue-100">
                <span class="font-semibold">{{ stats?.recent_hires || 0 }}</span> new hires this month
              </p>
            </div>
          </div>

          <!-- Present Today -->
          <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-3">
              <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-green-100 text-sm font-medium">Present Today</p>
                <h3 class="text-4xl font-bold">{{ stats?.present_today || 0 }}</h3>
              </div>
            </div>
            <div class="mt-4 pt-4 border-t border-green-400 border-opacity-30">
              <p class="text-sm text-green-100">
                <span class="font-semibold">{{ attendanceRate }}%</span> attendance rate
              </p>
            </div>
          </div>

          <!-- Absent Today -->
          <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-3">
              <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-red-100 text-sm font-medium">Absent Today</p>
                <h3 class="text-4xl font-bold">{{ stats?.absent_today || 0 }}</h3>
              </div>
            </div>
            <div class="mt-4 pt-4 border-t border-red-400 border-opacity-30">
              <p class="text-sm text-red-100">
                Requires attention
              </p>
            </div>
          </div>

          <!-- On Leave -->
          <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between mb-3">
              <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-amber-100 text-sm font-medium">On Leave</p>
                <h3 class="text-4xl font-bold">{{ stats?.on_leave_today || 0 }}</h3>
              </div>
            </div>
            <div class="mt-4 pt-4 border-t border-amber-400 border-opacity-30">
              <p class="text-sm text-amber-100">
                <span class="font-semibold">{{ stats?.pending_leave_requests || 0 }}</span> pending requests
              </p>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <!-- Attendance Trend -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="mb-5">
              <h3 class="text-lg font-bold text-gray-900">Attendance Trend (Last 7 Days)</h3>
              <p class="text-sm text-gray-600 mt-1">Daily attendance overview</p>
            </div>
            <div class="space-y-3">
              <div v-for="(day, index) in stats?.attendance_trend" :key="index" class="flex items-center">
                <div class="w-16 text-sm font-medium text-gray-700">{{ day.day }}</div>
                <div class="flex-1 flex items-center space-x-2">
                  <div class="flex-1 bg-gray-100 rounded-full h-8 overflow-hidden flex">
                    <div 
                      v-if="day.present > 0"
                      class="bg-green-500 flex items-center justify-center text-white text-xs font-medium"
                      :style="{ width: `${(day.present / day.total * 100)}%` }"
                    >
                      {{ day.present > 0 ? day.present : '' }}
                    </div>
                    <div 
                      v-if="day.absent > 0"
                      class="bg-red-500 flex items-center justify-center text-white text-xs font-medium"
                      :style="{ width: `${(day.absent / day.total * 100)}%` }"
                    >
                      {{ day.absent > 0 ? day.absent : '' }}
                    </div>
                    <div 
                      v-if="day.on_leave > 0"
                      class="bg-amber-500 flex items-center justify-center text-white text-xs font-medium"
                      :style="{ width: `${(day.on_leave / day.total * 100)}%` }"
                    >
                      {{ day.on_leave > 0 ? day.on_leave : '' }}
                    </div>
                  </div>
                  <div class="text-sm font-semibold text-gray-700 w-12 text-right">{{ day.total }}</div>
                </div>
              </div>
            </div>
            <div class="flex items-center justify-center space-x-6 mt-6 pt-4 border-t border-gray-200">
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                <span class="text-xs text-gray-600">Present</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                <span class="text-xs text-gray-600">Absent</span>
              </div>
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-amber-500 rounded-full"></div>
                <span class="text-xs text-gray-600">On Leave</span>
              </div>
            </div>
          </div>

          <!-- Department Distribution -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="mb-5">
              <h3 class="text-lg font-bold text-gray-900">Department Distribution</h3>
              <p class="text-sm text-gray-600 mt-1">Employee count by department</p>
            </div>
            <div class="space-y-3">
              <div v-for="(dept, index) in stats?.department_stats" :key="index" class="group">
                <div class="flex items-center justify-between mb-1">
                  <span class="text-sm font-medium text-gray-700">{{ dept.name }}</span>
                  <span class="text-sm font-bold text-gray-900">{{ dept.employee_count }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                  <div 
                    class="h-full rounded-full transition-all duration-500 group-hover:opacity-80"
                    :class="getDeptColor(index)"
                    :style="{ width: `${(dept.employee_count / stats.total_employees * 100)}%` }"
                  ></div>
                </div>
              </div>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-200 text-center">
              <p class="text-sm text-gray-600">
                <span class="font-bold text-gray-900">{{ stats?.departments || 0 }}</span> active departments
              </p>
            </div>
          </div>
        </div>

        <!-- Payroll Summary & Quick Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <!-- Payroll Summary -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="mb-5">
              <h3 class="text-lg font-bold text-gray-900">Payroll Summary</h3>
              <p class="text-sm text-gray-600 mt-1">Current month overview</p>
            </div>
            <div class="space-y-4">
              <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                <div class="flex items-center space-x-3">
                  <div class="bg-purple-500 rounded-lg p-2">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <span class="text-sm font-medium text-gray-700">Total Payroll</span>
                </div>
                <span class="text-xl font-bold text-purple-700">Rs. {{ formatNumber(stats?.payroll_stats?.total_payroll_current_month || 0) }}</span>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div class="p-3 bg-green-50 rounded-lg border border-green-200 text-center">
                  <div class="text-2xl font-bold text-green-600">{{ stats?.payroll_stats?.processed_payrolls || 0 }}</div>
                  <div class="text-xs text-gray-600 mt-1">Processed</div>
                </div>
                <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-center">
                  <div class="text-2xl font-bold text-amber-600">{{ stats?.payroll_stats?.pending_payrolls || 0 }}</div>
                  <div class="text-xs text-gray-600 mt-1">Pending</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Hiring Trend -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="mb-5">
              <h3 class="text-lg font-bold text-gray-900">Hiring Trend</h3>
              <p class="text-sm text-gray-600 mt-1">Last 6 months overview</p>
            </div>
            <div class="flex items-end justify-between h-40 space-x-3">
              <div v-for="(month, index) in stats?.hiring_trend" :key="index" class="flex-1 flex flex-col items-center">
                <div class="w-full bg-gradient-to-t from-indigo-500 to-indigo-400 rounded-t-lg transition-all hover:opacity-80" 
                     :style="{ height: `${month.count > 0 ? (month.count / maxHiring * 100) : 5}%` }">
                </div>
                <div class="mt-2 text-center">
                  <div class="text-xs font-semibold text-gray-700">{{ month.count }}</div>
                  <div class="text-xs text-gray-500 mt-1">{{ month.month }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity & Pending Items -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <!-- Recent Employees -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
              <div>
                <h3 class="text-lg font-bold text-gray-900">Recent Employees</h3>
                <p class="text-sm text-gray-600 mt-1">Newly joined team members</p>
              </div>
              <router-link to="/employees" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                View All →
              </router-link>
            </div>
            <div class="space-y-3">
              <div v-for="employee in stats?.recent_employees" :key="employee.id" 
                   class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer"
                   @click="$router.push(`/employees/${employee.id}`)">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-lg">
                    {{ employee.first_name?.charAt(0) }}{{ employee.last_name?.charAt(0) }}
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-gray-900 truncate">{{ employee.first_name }} {{ employee.last_name }}</p>
                  <p class="text-xs text-gray-600">{{ employee.designation?.title }} • {{ employee.department?.name }}</p>
                </div>
                <div class="flex-shrink-0 text-right">
                  <p class="text-xs text-gray-500">{{ formatDate(employee.joining_date) }}</p>
                </div>
              </div>
              <div v-if="!stats?.recent_employees?.length" class="text-center py-8 text-gray-500">
                No recent employees
              </div>
            </div>
          </div>

          <!-- Pending Leave Requests  -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
              <div>
                <h3 class="text-lg font-bold text-gray-900">Pending Leave Requests</h3>
                <p class="text-sm text-gray-600 mt-1">Requires approval</p>
              </div>
              <router-link to="/leaves" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                View All →
              </router-link>
            </div>
            <div class="space-y-3">
              <div v-for="leave in stats?.recent_leaves" :key="leave.id" 
                   class="flex items-center justify-between p-3 rounded-lg border border-gray-200 hover:border-amber-300 hover:bg-amber-50 transition-colors">
                <div class="flex-1">
                  <div class="flex items-center space-x-2">
                    <span class="text-sm font-semibold text-gray-900">{{ leave.employee?.first_name }} {{ leave.employee?.last_name }}</span>
                    <span class="px-2 py-0.5 text-xs font-medium bg-amber-100 text-amber-700 rounded">{{ leave.leave_type?.name }}</span>
                  </div>
                  <p class="text-xs text-gray-600 mt-1">{{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }} ({{ leave.total_days }} days)</p>
                </div>
                <div class="ml-4">
                  <button class="px-3 py-1.5 text-xs font-medium text-green-600 hover:bg-green-50 border border-green-300 rounded-lg transition-colors">
                    Approve
                  </button>
                </div>
              </div>
              <div v-if="!stats?.recent_leaves?.length" class="text-center py-8 text-gray-500">
                No pending requests
              </div>
            </div>
          </div>
        </div>

        <!-- Announcements, Birthdays & Contracts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
          <!-- Announcements -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
              <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                </svg>
                <h3 class="text-lg font-bold text-gray-900">Announcements</h3>
              </div>
            </div>
            <div v-if="stats?.announcements?.length" class="space-y-3">
              <div v-for="announcement in stats.announcements" :key="announcement.id" 
                   class="p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                <div class="flex items-center justify-between mb-1">
                  <h4 class="text-sm font-semibold text-gray-900">{{ announcement.title }}</h4>
                  <span v-if="announcement.priority === 'high'" 
                        class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded">
                    High Priority
                  </span>
                </div>
                <p class="text-xs text-gray-600 line-clamp-2">{{ announcement.content }}</p>
                <p class="text-xs text-gray-500 mt-2">{{ formatDate(announcement.created_at) }}</p>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
              </svg>
              <p class="text-sm font-medium">No announcements at the moment</p>
              <p class="text-xs text-gray-400 mt-1">Check back later for updates</p>
            </div>
          </div>

          <!-- Upcoming Birthdays -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
              <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6 3a1 1 0 011-1h.01a1 1 0 010 2H7a1 1 0 01-1-1zm2 3a1 1 0 00-2 0v1a2 2 0 00-2 2v1a2 2 0 00-2 2v.683a3.7 3.7 0 011.055.485 1.704 1.704 0 001.89 0 3.704 3.704 0 014.11 0 1.704 1.704 0 001.89 0 3.704 3.704 0 014.11 0 1.704 1.704 0 001.89 0A3.7 3.7 0 0118 12.683V12a2 2 0 00-2-2V9a2 2 0 00-2-2V6a1 1 0 10-2 0v1h-1V6a1 1 0 10-2 0v1H8V6zm10 8.868a3.704 3.704 0 01-4.055-.036 1.704 1.704 0 00-1.89 0 3.704 3.704 0 01-4.11 0 1.704 1.704 0 00-1.89 0A3.704 3.704 0 012 14.868V17a1 1 0 001 1h14a1 1 0 001-1v-2.132zM9 3a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm3 0a1 1 0 011-1h.01a1 1 0 110 2H13a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
                <h3 class="text-lg font-bold text-gray-900">Birthdays (30 days)</h3>
              </div>
            </div>
            <div v-if="stats?.upcoming_birthdays?.length" class="space-y-2">
              <div v-for="emp in stats.upcoming_birthdays" :key="emp.id" 
                   class="flex items-center space-x-3 p-2 rounded-lg hover:bg-pink-50 transition-colors">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center text-white font-bold text-sm">
                    {{ emp.first_name?.charAt(0) }}{{ emp.last_name?.charAt(0) }}
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 truncate">{{ emp.first_name }} {{ emp.last_name }}</p>
                  <p class="text-xs text-gray-600">{{ formatDate(emp.date_of_birth) }}</p>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <p class="text-sm font-medium">No upcoming birthdays</p>
              <p class="text-xs text-gray-400 mt-1">in the next 30 days</p>
            </div>
          </div>

          <!-- Expiring Contracts -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
              <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V8z" clip-rule="evenodd"/>
                </svg>
                <h3 class="text-lg font-bold text-gray-900">Expiring Contracts</h3>
              </div>
            </div>
            <div v-if="stats?.expiring_contracts?.length" class="space-y-2">
              <div v-for="contract in stats.expiring_contracts" :key="contract.id" 
                   class="p-3 border-l-4 border-orange-500 bg-orange-50 rounded">
                <p class="text-sm font-semibold text-gray-900">{{ contract.employee?.first_name }} {{ contract.employee?.last_name }}</p>
                <p class="text-xs text-gray-600">{{ contract.employee?.department?.name }}</p>
                <p class="text-xs text-orange-600 font-medium mt-1">Expires: {{ formatDate(contract.end_date) }}</p>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <p class="text-sm font-medium">No expiring contracts</p>
              <p class="text-xs text-gray-400 mt-1">All contracts are valid</p>
            </div>
          </div>
        </div>

        <!-- Additional Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
          <!-- Gender Distribution -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-5">Gender Distribution</h3>
            <div v-if="stats?.gender_stats?.length" class="space-y-3">
              <div v-for="(item, index) in stats.gender_stats" :key="index">
                <div class="flex items-center justify-between mb-1">
                  <span class="text-sm font-medium text-gray-700 capitalize">{{ item.gender }}</span>
                  <span class="text-sm font-bold text-gray-900">{{ item.count }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                  <div 
                    class="h-full rounded-full transition-all"
                    :class="getGenderColor(item.gender)"
                    :style="{ width: `${(item.count / stats.total_employees * 100)}%` }"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Employment Type -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-5">Employment Type</h3>
            <div v-if="stats?.employment_type_stats?.length" class="space-y-3">
              <div v-for="(item, index) in stats.employment_type_stats" :key="index">
                <div class="flex items-center justify-between mb-1">
                  <span class="text-sm font-medium text-gray-700 capitalize">{{ item.employment_type.replace('_', ' ') }}</span>
                  <span class="text-sm font-bold text-gray-900">{{ item.count }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                  <div 
                    class="h-full rounded-full transition-all"
                    :class="getEmploymentTypeColor(index)"
                    :style="{ width: `${(item.count / stats.total_employees * 100)}%` }"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Leave Type Breakdown -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-5">Leave Types (This Month)</h3>
            <div v-if="stats?.leave_type_stats?.length" class="space-y-3">
              <div v-for="(item, index) in stats.leave_type_stats" :key="index" class="flex items-center justify-between p-2 bg-gray-50 rounded">
                <span class="text-sm font-medium text-gray-700">{{ item.leave_type?.name }}</span>
                <span class="text-sm font-bold text-blue-600">{{ item.count }}</span>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              <p class="text-sm">No leave data</p>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <router-link to="/employees/create" class="flex items-center justify-center space-x-2 p-4 bg-white rounded-lg border-2 border-gray-200 hover:border-blue-500 hover:shadow-md transition-all group">
              <svg class="w-5 h-5 text-gray-600 group-hover:text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
              </svg>
              <span class="text-sm font-medium text-gray-700 group-hover:text-blue-600">Add Employee</span>
            </router-link>
            <router-link to="/attendance" class="flex items-center justify-center space-x-2 p-4 bg-white rounded-lg border-2 border-gray-200 hover:border-green-500 hover:shadow-md transition-all group">
              <svg class="w-5 h-5 text-gray-600 group-hover:text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
              </svg>
              <span class="text-sm font-medium text-gray-700 group-hover:text-green-600">Mark Attendance</span>
            </router-link>
            <router-link to="/payroll" class="flex items-center justify-center space-x-2 p-4 bg-white rounded-lg border-2 border-gray-200 hover:border-purple-500 hover:shadow-md transition-all group">
              <svg class="w-5 h-5 text-gray-600 group-hover:text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
              </svg>
              <span class="text-sm font-medium text-gray-700 group-hover:text-purple-600">Process Payroll</span>
            </router-link>
            <router-link to="/departments" class="flex items-center justify-center space-x-2 p-4 bg-white rounded-lg border-2 border-gray-200 hover:border-indigo-500 hover:shadow-md transition-all group">
              <svg class="w-5 h-5 text-gray-600 group-hover:text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
              </svg>
              <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">Manage Departments</span>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Employee Dashboard -->
      <div v-else>
        <!-- Page Header -->
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-gray-900">My Dashboard</h1>
          <p class="text-gray-600 mt-1">Welcome back! Here's your overview.</p>
        </div>

        <!-- Employee Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-3">
              <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-green-100 text-sm font-medium">Present Days</p>
                <h3 class="text-4xl font-bold">{{ stats?.my_attendance_summary?.present_days || 0 }}</h3>
              </div>
            </div>
            <div class="mt-4 pt-4 border-t border-green-400 border-opacity-30">
              <p class="text-sm text-green-100">This month</p>
            </div>
          </div>

          <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-3">
              <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-red-100 text-sm font-medium">Absent Days</p>
                <h3 class="text-4xl font-bold">{{ stats?.my_attendance_summary?.absent_days || 0 }}</h3>
              </div>
            </div>
            <div class="mt-4 pt-4 border-t border-red-400 border-opacity-30">
              <p class="text-sm text-red-100">This month</p>
            </div>
          </div>

          <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-3">
              <div class="bg-white bg-opacity-20 rounded-lg p-3">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div class="text-right">
                <p class="text-amber-100 text-sm font-medium">Leave Days</p>
                <h3 class="text-4xl font-bold">{{ stats?.my_attendance_summary?.leave_days || 0 }}</h3>
              </div>
            </div>
            <div class="mt-4 pt-4 border-t border-amber-400 border-opacity-30">
              <p class="text-sm text-amber-100">This month</p>
            </div>
          </div>
        </div>

        <!-- Today's Attendance -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
          <h3 class="text-lg font-bold text-gray-900 mb-5">Today's Attendance</h3>
          <div v-if="stats?.my_attendance_today" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
              <div class="text-blue-600 text-sm font-medium mb-2">Current Check In</div>
              <div class="text-2xl font-bold text-gray-900">{{ stats.my_attendance_today.check_in || '--:--' }}</div>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
              <div class="text-gray-600 text-sm font-medium mb-2">Status</div>
              <div class="text-lg font-bold text-green-600">Active Session</div>
            </div>
            <div class="text-center p-4 bg-amber-50 rounded-lg">
              <div class="text-amber-600 text-sm font-medium mb-2">Session Duration</div>
              <div class="text-2xl font-bold text-gray-900">{{ calculateDuration(stats.my_attendance_today.check_in) }}</div>
            </div>
          </div>
          <div v-else class="text-center py-6 text-gray-500">
            <svg class="w-10 h-10 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm">No active attendance session</p>
          </div>
          
          <!-- Dynamic Check In/Out Button -->
          <div class="text-center mt-6">
            <!-- Show Check Out button if there's an active check-in (incomplete attendance) -->
            <button 
              v-if="stats?.my_attendance_today"
              @click="handleCheckOut"
              :disabled="processingAttendance"
              class="inline-flex items-center space-x-3 px-8 py-4 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-semibold rounded-xl shadow-lg transform hover:scale-105 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
              <svg class="w-6 h-6" :class="{ 'animate-spin': processingAttendance }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              <span>{{ processingAttendance ? 'Processing...' : 'Check Out' }}</span>
            </button>
            
            <!-- Show Check In button if no active check-in -->
            <button 
              v-else
              @click="handleCheckIn"
              :disabled="processingAttendance"
              class="inline-flex items-center space-x-3 px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-xl shadow-lg transform hover:scale-105 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
              <svg class="w-6 h-6" :class="{ 'animate-spin': processingAttendance }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
              </svg>
              <span>{{ processingAttendance ? 'Processing...' : 'Check In' }}</span>
            </button>
            
            <!-- Info text about multiple check-ins -->
            <p class="text-sm text-gray-500 mt-3">
              {{ stats?.my_attendance_today ? 'Complete your check-out to start a new session' : 'You can check in multiple times per day' }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Leave Balance -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-5">Leave Balance</h3>
            <div v-if="stats?.my_leave_balance?.length" class="space-y-3">
              <div v-for="balance in stats.my_leave_balance" :key="balance.id" class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm font-semibold text-gray-900">{{ balance.leave_type.name }}</span>
                  <span class="text-lg font-bold text-blue-600">{{ balance.remaining_days }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                  <div 
                    class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full transition-all"
                    :style="{ width: `${(balance.remaining_days / balance.total_days * 100)}%` }"
                  ></div>
                </div>
                <div class="flex items-center justify-between mt-2">
                  <span class="text-xs text-gray-600">Used: {{ balance.used_days || 0 }}</span>
                  <span class="text-xs text-gray-600">Total: {{ balance.total_days }}</span>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              No leave balance information available
            </div>
          </div>

          <!-- Pending Leave Applications -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
              <h3 class="text-lg font-bold text-gray-900">My Leave Applications</h3>
              <router-link to="/leaves" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                View All →
              </router-link>
            </div>
            <div v-if="stats?.my_pending_leaves?.length" class="space-y-3">
              <div v-for="leave in stats.my_pending_leaves" :key="leave.id" 
                   class="p-4 rounded-lg border-2 border-amber-200 bg-amber-50">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm font-semibold text-gray-900">{{ leave.leave_type.name }}</span>
                  <span class="px-2 py-1 text-xs font-medium bg-amber-200 text-amber-800 rounded">Pending</span>
                </div>
                <div class="text-xs text-gray-600">
                  {{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }}
                </div>
                <div class="text-xs font-medium text-gray-700 mt-1">
                  {{ leave.total_days }} day(s)
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p>No pending leave applications</p>
            </div>
          </div>
        </div>

        <!-- Recent Attendance & Additional Info -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
          <!-- Recent Attendance History -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-5">Recent Attendance (Last 7 Days)</h3>
            <div v-if="stats?.my_recent_attendance?.length" class="space-y-2">
              <div v-for="att in stats.my_recent_attendance" :key="att.id" 
                   class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-900">{{ formatDate(att.date) }}</p>
                  <p class="text-xs text-gray-600">{{ att.check_in || '--' }} - {{ att.check_out || '--' }}</p>
                </div>
                <div class="flex items-center space-x-3">
                  <span class="text-xs font-medium text-gray-700">{{ att.working_hours || 0 }}h</span>
                  <span 
                    class="px-2 py-1 text-xs font-medium rounded"
                    :class="{
                      'bg-green-100 text-green-700': att.status === 'present',
                      'bg-yellow-100 text-yellow-700': att.status === 'late',
                      'bg-red-100 text-red-700': att.status === 'absent',
                      'bg-amber-100 text-amber-700': att.status === 'on_leave'
                    }">
                    {{ att.status }}
                  </span>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              <p class="text-sm">No attendance records</p>
            </div>
          </div>

          <!-- Upcoming Approved Leaves -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-5">Upcoming Leaves</h3>
            <div v-if="stats?.my_upcoming_leaves?.length" class="space-y-3">
              <div v-for="leave in stats.my_upcoming_leaves" :key="leave.id" 
                   class="p-3 bg-green-50 border-l-4 border-green-500 rounded">
                <div class="flex items-center justify-between mb-1">
                  <span class="text-sm font-semibold text-gray-900">{{ leave.leave_type?.name }}</span>
                  <span class="px-2 py-1 text-xs font-medium bg-green-200 text-green-800 rounded">Approved</span>
                </div>
                <p class="text-xs text-gray-600">
                  {{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }}
                </p>
                <p class="text-xs text-gray-700 font-medium mt-1">{{ leave.total_days }} day(s)</p>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <p class="text-sm">No upcoming leaves</p>
            </div>
          </div>
        </div>

        <!-- Announcements & Work Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
          <!-- Announcements -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-2 mb-5">
              <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
              </svg>
              <h3 class="text-lg font-bold text-gray-900">Announcements</h3>
            </div>
            <div v-if="stats?.announcements?.length" class="space-y-3">
              <div v-for="announcement in stats.announcements" :key="announcement.id" 
                   class="p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                <div class="flex items-center justify-between mb-1">
                  <h4 class="text-sm font-semibold text-gray-900">{{ announcement.title }}</h4>
                  <span v-if="announcement.priority === 'high'" 
                        class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded">
                    High Priority
                  </span>
                </div>
                <p class="text-xs text-gray-600 line-clamp-2">{{ announcement.content }}</p>
                <p class="text-xs text-gray-500 mt-2">{{ formatDate(announcement.created_at) }}</p>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
              </svg>
              <p class="text-sm font-medium">No announcements</p>
            </div>
          </div>

          <!-- Work Summary Card -->
          <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
            <h3 class="text-lg font-bold mb-5">This Month Summary</h3>
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-white bg-opacity-20 rounded-lg p-4">
                <div class="text-indigo-100 text-sm mb-1">Total Hours</div>
                <div class="text-3xl font-bold">{{ stats?.my_attendance_summary?.total_hours || 0 }}h</div>
              </div>
              <div class="bg-white bg-opacity-20 rounded-lg p-4">
                <div class="text-indigo-100 text-sm mb-1">Overtime Hours</div>
                <div class="text-3xl font-bold">{{ stats?.my_attendance_summary?.overtime_hours || 0 }}h</div>
              </div>
              <div class="bg-white bg-opacity-20 rounded-lg p-4">
                <div class="text-indigo-100 text-sm mb-1">Present Days</div>
                <div class="text-3xl font-bold">{{ stats?.my_attendance_summary?.present_days || 0 }}</div>
              </div>
              <div class="bg-white bg-opacity-20 rounded-lg p-4">
                <div class="text-indigo-100 text-sm mb-1">Leave Days</div>
                <div class="text-3xl font-bold">{{ stats?.my_attendance_summary?.leave_days || 0 }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Payslips -->
        <div v-if="stats?.my_recent_payslips?.length" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
          <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-900">Recent Payslips</h3>
            <router-link to="/payroll" class="text-sm font-medium text-blue-600 hover:text-blue-700">
              View All →
            </router-link>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div v-for="payslip in stats.my_recent_payslips" :key="payslip.id" 
                 class="p-4 rounded-lg border border-gray-200 hover:border-green-300 hover:shadow-md transition-all cursor-pointer">
              <div class="text-xs text-gray-600 mb-1">{{ months[payslip.month - 1] }} {{ payslip.year }}</div>
              <div class="text-2xl font-bold text-green-600">Rs. {{ formatNumber(payslip.net_salary) }}</div>
              <div class="flex items-center justify-between mt-3 text-xs">
                <span class="text-gray-600">Net Salary</span>
                <span class="px-2 py-1 bg-green-100 text-green-700 rounded font-medium">Paid</span>
              </div>
            </div>
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

const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

const isAdmin = computed(() => authStore.isAdmin);
const isManager = computed(() => authStore.isManager);

const attendanceRate = computed(() => {
  if (!stats.value?.total_employees || stats.value?.total_employees === 0) return 0;
  return Math.round((stats.value.present_today / stats.value.total_employees) * 100);
});

const maxHiring = computed(() => {
  if (!stats.value?.hiring_trend?.length) return 1;
  return Math.max(...stats.value.hiring_trend.map(m => m.count), 1);
});

const departmentColors = [
  'bg-blue-500',
  'bg-green-500',
  'bg-purple-500',
  'bg-pink-500',
  'bg-indigo-500',
  'bg-yellow-500',
  'bg-red-500',
  'bg-teal-500',
];

const getDeptColor = (index) => {
  return departmentColors[index % departmentColors.length];
};

const getGenderColor = (gender) => {
  const colors = {
    'male': 'bg-blue-500',
    'female': 'bg-pink-500',
    'other': 'bg-purple-500'
  };
  return colors[gender?.toLowerCase()] || 'bg-gray-500';
};

const getEmploymentTypeColor = (index) => {
  const colors = ['bg-indigo-500', 'bg-teal-500', 'bg-orange-500', 'bg-cyan-500'];
  return colors[index % colors.length];
};

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-PK').format(num);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PK', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });
};

const calculateDuration = (checkInTime) => {
  if (!checkInTime) return '0h 0m';
  
  const now = new Date();
  const [hours, minutes] = checkInTime.split(':');
  const checkIn = new Date();
  checkIn.setHours(parseInt(hours), parseInt(minutes), 0);
  
  const diffMs = now - checkIn;
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
  const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
  
  return `${diffHours}h ${diffMinutes}m`;
};

const handleCheckIn = async () => {
  processingAttendance.value = true;
  try {
    const response = await axios.post('/attendance/check-in');
    showSuccess('Check-in successful!');
    
    // Refresh dashboard to update attendance display
    await refreshDashboard();
  } catch (err) {
    console.error('Check-in error:', err);
    const errorMessage = err.response?.data?.message || 'Failed to check in. Please try again.';
    showError(errorMessage);
  } finally {
    processingAttendance.value = false;
  }
};

const handleCheckOut = async () => {
  processingAttendance.value = true;
  try {
    const response = await axios.post('/attendance/check-out');
    showSuccess('Check-out successful!');
    
    // Refresh dashboard to update attendance display
    await refreshDashboard();
  } catch (err) {
    console.error('Check-out error:', err);
    const errorMessage = err.response?.data?.message || 'Failed to check out. Please try again.';
    showError(errorMessage);
  } finally {
    processingAttendance.value = false;
  }
};

const refreshDashboard = async () => {
  loading.value = true;
  error.value = null;
  try {
    // Force fresh data by clearing cache
    dashboardStore.$reset();
    stats.value = await dashboardStore.fetchDashboardData();
    console.log('Dashboard refreshed with fresh data:', stats.value);
  } catch (err) {
    error.value = 'Failed to load dashboard data. Please try again.';
    console.error('Dashboard error:', err);
  } finally {
    loading.value = false;
  }
};

// Watch for access denied notifications from router
watch(() => route.query.denied, (deniedModule) => {
  if (deniedModule) {
    const message = route.query.message || 'You do not have permission to access this module';
    showError(message);
    
    // Clean up query params
    const router = route.router || route.route;
    if (router) {
      router.replace({ query: {} });
    }
  }
}, { immediate: true });

onMounted(async () => {
  await refreshDashboard();
});
</script>

<style scoped>
/* Add any custom styles here if needed */
</style>
