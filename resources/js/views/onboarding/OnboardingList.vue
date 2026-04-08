<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Employee Onboarding</h1>
        <p class="text-sm text-gray-500 mt-1">Track new hire onboarding progress and manage templates</p>
      </div>
      <div class="flex items-center space-x-3">
        <button @click="$router.push('/onboarding/templates')" class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors shadow-sm">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" /></svg>
          Templates
        </button>
        <button @click="openStartModal" class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          Start Onboarding
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ statsData.total }}</h3>
          </div>
          <div class="bg-gray-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">In Progress</p>
            <h3 class="text-2xl font-bold text-blue-600">{{ statsData.inProgress }}</h3>
          </div>
          <div class="bg-blue-50 rounded-lg p-3">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Completed</p>
            <h3 class="text-2xl font-bold text-green-600">{{ statsData.completed }}</h3>
          </div>
          <div class="bg-green-50 rounded-lg p-3">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Not Started</p>
            <h3 class="text-2xl font-bold text-yellow-600">{{ statsData.notStarted }}</h3>
          </div>
          <div class="bg-yellow-50 rounded-lg p-3">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Search Employee</label>
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input v-model="filters.search" type="text" placeholder="Search by employee name..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
              @input="debouncedSearch" />
          </div>
        </div>
        <div class="min-w-[160px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" @change="fetchOnboardings()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            <option value="">All Status</option>
            <option value="not_started">Not Started</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div>
          <button @click="resetFilters" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Reset</button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
      <p class="font-medium">{{ error }}</p>
      <button @click="fetchOnboardings()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <!-- Empty State -->
    <div v-else-if="onboardings.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Onboarding Records</h3>
      <p class="text-gray-500">Click "Start Onboarding" to begin a new employee's onboarding process.</p>
    </div>

    <!-- Onboarding Cards -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="ob in onboardings" :key="ob.id"
        class="bg-white rounded-lg shadow border border-gray-200 hover:shadow-lg transition-shadow overflow-hidden">
        <!-- Header -->
        <div class="p-5 border-b border-gray-200">
          <div class="flex items-start justify-between mb-3">
            <div class="flex items-center space-x-3">
              <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-bold text-gray-600">{{ getInitials(ob.employee) }}</span>
              </div>
              <div>
                <h3 class="text-base font-bold text-gray-900">{{ getEmployeeName(ob.employee) }}</h3>
                <p class="text-sm text-gray-500">{{ ob.employee?.employee_code }}</p>
              </div>
            </div>
            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full whitespace-nowrap" :class="statusBadge(ob.status)">
              {{ formatStatus(ob.status) }}
            </span>
          </div>
          <p class="text-sm text-gray-600">{{ ob.template?.name || 'No template' }}</p>
        </div>

        <!-- Info -->
        <div class="p-5 bg-gray-50 space-y-2.5">
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500 flex items-center">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
              Start Date
            </span>
            <span class="font-medium text-gray-900">{{ formatDate(ob.start_date) }}</span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500 flex items-center">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
              Buddy
            </span>
            <span class="font-medium text-gray-900">{{ getBuddyName(ob.buddy) || 'None' }}</span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500 flex items-center">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
              Tasks
            </span>
            <span class="font-medium text-gray-900">{{ ob.tasks?.length || 0 }} tasks</span>
          </div>
          <div class="pt-2">
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs font-semibold text-gray-600">Progress</span>
              <span class="text-xs font-bold text-gray-900">{{ ob.completion_percentage || 0 }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="h-2 rounded-full transition-all duration-500"
                :class="progressBarColor(ob.completion_percentage)"
                :style="`width: ${ob.completion_percentage || 0}%`"></div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="p-4 border-t border-gray-200 flex items-center gap-2">
          <button @click="viewDetails(ob)" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
            View
          </button>
          <button @click="editOnboarding(ob)" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            Edit
          </button>
          <button @click="deleteOnboardingConfirm(ob)" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="onboardings.length > 0 && pagination && pagination.last_page > 1" class="flex items-center justify-between bg-white rounded-lg shadow border border-gray-200 px-6 py-4 mt-6">
      <div class="text-sm text-gray-600">Showing <span class="font-semibold text-gray-900">{{ pagination.total }}</span> records</div>
      <div class="flex items-center space-x-2">
        <button @click="fetchOnboardings(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :class="pagination.current_page === 1 ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'">Previous</button>
        <span class="text-sm text-gray-600">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
        <button @click="fetchOnboardings(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :class="pagination.current_page === pagination.last_page ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'">Next</button>
      </div>
    </div>

    <!-- Start Onboarding Modal -->
    <div v-if="showStartModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingOnboarding ? 'Edit' : 'Start New' }} Onboarding</h3>
          <button @click="showStartModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
            <SearchableSelect
              v-model="form.employee_id"
              :options="employeeOptions"
              placeholder="Select employee..."
              search-placeholder="Search by name or code..."
            />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Onboarding Template <span class="text-red-500">*</span></label>
            <select v-model="form.template_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
              <option value="">Select Template</option>
              <option v-for="tmpl in templates" :key="tmpl.id" :value="tmpl.id">
                {{ tmpl.name }} <span v-if="tmpl.department">({{ tmpl.department?.name }})</span> — {{ tmpl.duration_days }} days
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date <span class="text-red-500">*</span></label>
            <input v-model="form.start_date" type="date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Onboarding Buddy</label>
            <SearchableSelect
              v-model="form.buddy_id"
              :options="buddyOptions"
              placeholder="Select buddy (optional)..."
              search-placeholder="Search by name or code..."
            />
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showStartModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="startOnboarding" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50">
            {{ saving ? 'Starting...' : 'Start Onboarding' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Details Slideout Modal -->
    <div v-if="selectedOnboarding" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4 overflow-hidden max-h-[85vh] flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center flex-shrink-0">
          <h3 class="text-lg font-bold text-gray-900">Onboarding Details</h3>
          <button @click="selectedOnboarding = null" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>

        <!-- Scrollable content -->
        <div class="overflow-y-auto flex-1">
          <!-- Employee Info Card -->
          <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Employee</p>
                <p class="font-semibold text-gray-900">{{ getEmployeeName(selectedOnboarding.employee) }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Buddy</p>
                <p class="font-semibold text-gray-900">{{ getBuddyName(selectedOnboarding.buddy) || 'None' }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Start Date</p>
                <p class="font-semibold text-gray-900">{{ formatDate(selectedOnboarding.start_date) }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Expected Completion</p>
                <p class="font-semibold text-gray-900">{{ formatDate(selectedOnboarding.expected_completion_date) }}</p>
              </div>
            </div>
            <!-- Progress Bar -->
            <div class="mt-4">
              <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-semibold text-gray-700">Overall Progress</span>
                <span class="text-sm font-bold text-gray-900">{{ selectedOnboarding.completion_percentage || 0 }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="h-3 rounded-full transition-all duration-500"
                  :class="progressBarColor(selectedOnboarding.completion_percentage)"
                  :style="`width: ${selectedOnboarding.completion_percentage || 0}%`"></div>
              </div>
            </div>
          </div>

          <!-- Tasks List -->
          <div class="px-6 py-4">
            <h4 class="text-base font-bold text-gray-900 mb-3">Onboarding Tasks</h4>
            <div v-if="!selectedOnboarding.tasks || selectedOnboarding.tasks.length === 0" class="text-sm text-gray-500 text-center py-6">No tasks found for this onboarding.</div>
            <div v-else class="space-y-2">
              <div v-for="task in sortedTasks" :key="task.id"
                class="border border-gray-200 rounded-lg p-4 flex items-start justify-between gap-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start space-x-3 flex-1 min-w-0">
                  <!-- Status Icon -->
                  <div class="flex-shrink-0 mt-0.5">
                    <svg v-if="task.status === 'completed'" class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <svg v-else-if="task.status === 'skipped'" class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/></svg>
                    <div v-else-if="task.status === 'in_progress'" class="w-5 h-5 rounded-full border-2 border-blue-500 flex items-center justify-center">
                      <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    </div>
                    <div v-else class="w-5 h-5 rounded-full border-2 border-gray-300"></div>
                  </div>
                  <div class="min-w-0">
                    <p class="font-semibold text-gray-900" :class="{ 'line-through text-gray-400': task.status === 'skipped' }">{{ task.title }}</p>
                    <p v-if="task.description" class="text-sm text-gray-500 mt-0.5">{{ task.description }}</p>
                    <div class="flex items-center space-x-3 mt-1.5">
                      <span class="text-xs text-gray-400">Due: {{ formatDate(task.due_date) }}</span>
                      <span v-if="task.completed_date" class="text-xs text-green-600">Completed: {{ formatDate(task.completed_date) }}</span>
                      <span v-if="task.notes" class="text-xs text-gray-400 italic">{{ task.notes }}</span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center space-x-2 flex-shrink-0">
                  <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full" :class="taskStatusBadge(task.status)">{{ formatTaskStatus(task.status) }}</span>
                  <button v-if="task.status === 'pending' || task.status === 'in_progress'" @click="completeTask(task.id)" class="px-2.5 py-1 text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 rounded-md transition-colors">Complete</button>
                  <button v-if="task.status === 'pending' || task.status === 'in_progress'" @click="openSkipModal(task)" class="px-2.5 py-1 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">Skip</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Skip Task Modal -->
    <div v-if="showSkipModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-bold text-gray-900">Skip Task</h3>
          <p class="text-sm text-gray-500 mt-1">{{ skippingTask?.title }}</p>
        </div>
        <div class="px-6 py-5">
          <label class="block text-sm font-semibold text-gray-700 mb-1">Reason for Skipping <span class="text-red-500">*</span></label>
          <textarea v-model="skipNotes" rows="3" required placeholder="Provide a reason..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"></textarea>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showSkipModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="skipTask" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">Skip Task</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useNotification } from '@/composables/useNotification';
import SearchableSelect from '@/components/SearchableSelect.vue';

const { success, error: showError } = useNotification();

const onboardings = ref([]);
const employees = ref([]);
const templates = ref([]);
const loading = ref(false);
const error = ref(null);
const pagination = ref(null);
const saving = ref(false);
const formError = ref(null);

const showStartModal = ref(false);
const editingOnboarding = ref(null);
const buddyEmployees = ref([]);
const selectedOnboarding = ref(null);
const showSkipModal = ref(false);
const skippingTask = ref(null);
const skipNotes = ref('');

const filters = ref({ search: '', status: '' });
let searchTimer = null;

const employeeOptions = computed(() => {
  return employees.value.map(emp => ({
    value: emp.id,
    label: `${emp.employee_code} - ${emp.first_name} ${emp.last_name} (${emp.department?.name || 'N/A'})`
  }));
});

const buddyOptions = computed(() => {
  return [
    { value: '', label: 'No Buddy (optional)' },
    ...buddyEmployees.value.map(emp => ({
      value: emp.id,
      label: `${emp.employee_code} - ${emp.first_name} ${emp.last_name} (${emp.department?.name || 'N/A'})`
    }))
  ];
});

const form = ref({
  employee_id: '',
  template_id: '',
  start_date: new Date().toISOString().split('T')[0],
  buddy_id: '',
});

const statsData = computed(() => {
  const list = onboardings.value || [];
  return {
    total: pagination.value?.total || list.length,
    inProgress: list.filter(o => o.status === 'in_progress').length,
    completed: list.filter(o => o.status === 'completed').length,
    notStarted: list.filter(o => o.status === 'not_started').length,
  };
});

const sortedTasks = computed(() => {
  if (!selectedOnboarding.value?.tasks) return [];
  return [...selectedOnboarding.value.tasks].sort((a, b) => {
    const order = { pending: 0, in_progress: 1, completed: 2, skipped: 3 };
    return (order[a.status] ?? 4) - (order[b.status] ?? 4);
  });
});

const fetchOnboardings = async (page = 1) => {
  loading.value = true;
  error.value = null;
  try {
    const params = { page };
    if (filters.value.search) params.search = filters.value.search;
    if (filters.value.status) params.status = filters.value.status;
    const res = await axios.get('/onboarding', { params });
    onboardings.value = res.data.data || [];
    pagination.value = {
      current_page: res.data.current_page,
      last_page: res.data.last_page,
      per_page: res.data.per_page,
      total: res.data.total,
    };
  } catch (err) {
    error.value = 'Failed to load onboarding records';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const fetchEmployees = async (search = '') => {
  try {
    const params = search ? { search } : {};
    const res = await axios.get('/employees/dropdown', { params });
    employees.value = res.data || [];
  } catch (err) {
    console.error('Failed to load employees:', err);
  }
};

const fetchBuddyEmployees = async (search = '') => {
  try {
    const params = search ? { search } : {};
    const res = await axios.get('/employees/dropdown', { params });
    buddyEmployees.value = res.data || [];
  } catch (err) {
    console.error('Failed to load buddy employees:', err);
  }
};

const fetchTemplates = async () => {
  try {
    const res = await axios.get('/onboarding/templates');
    templates.value = res.data.data || res.data;
  } catch (err) {
    console.error('Failed to load templates:', err);
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => fetchOnboardings(), 400);
};

const resetFilters = () => {
  filters.value = { search: '', status: '' };
  fetchOnboardings();
};

const openStartModal = () => {
  editingOnboarding.value = null;
  formError.value = null;
  form.value = {
    employee_id: '',
    template_id: '',
    start_date: new Date().toISOString().split('T')[0],
    buddy_id: '',
  };
  showStartModal.value = true;
  fetchEmployees();
  fetchBuddyEmployees();
};

const editOnboarding = (onboarding) => {
  editingOnboarding.value = onboarding;
  formError.value = null;
  form.value = {
    employee_id: onboarding.employee_id,
    template_id: onboarding.template_id,
    start_date: onboarding.start_date,
    buddy_id: onboarding.buddy_id || '',
  };
  showStartModal.value = true;
  fetchEmployees();
  fetchBuddyEmployees();
};

const deleteOnboardingConfirm = (onboarding) => {
  if (confirm(`Are you sure you want to delete the onboarding for ${getEmployeeName(onboarding.employee)}? This action cannot be undone.`)) {
    deleteOnboardingRecord(onboarding.id);
  }
};

const deleteOnboardingRecord = async (id) => {
  try {
    await axios.delete(`/onboarding/${id}`);
    fetchOnboardings(pagination.value?.current_page || 1);
    success('Onboarding deleted successfully');
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to delete onboarding');
  }
};

const startOnboarding = async () => {
  formError.value = null;
  if (!form.value.employee_id || !form.value.template_id || !form.value.start_date) {
    formError.value = 'Please fill in all required fields';
    return;
  }
  saving.value = true;
  try {
    const payload = { ...form.value };
    if (!payload.buddy_id) delete payload.buddy_id;
    
    if (editingOnboarding.value) {
      await axios.put(`/onboarding/${editingOnboarding.value.id}`, payload);
      success('Onboarding updated successfully');
    } else {
      await axios.post('/onboarding/start', payload);
      success('Onboarding started successfully');
    }
    
    showStartModal.value = false;
    editingOnboarding.value = null;
    fetchOnboardings(pagination.value?.current_page || 1);
  } catch (err) {
    formError.value = err.response?.data?.message || `Failed to ${editingOnboarding.value ? 'update' : 'start'} onboarding`;
  } finally {
    saving.value = false;
  }
};

const viewDetails = async (ob) => {
  try {
    const res = await axios.get(`/onboarding/${ob.id}`);
    selectedOnboarding.value = res.data;
  } catch (err) {
    console.error('Failed to load details:', err);
    showError('Failed to load onboarding details');
  }
};

const completeTask = async (taskId) => {
  try {
    await axios.post(`/onboarding/tasks/${taskId}/complete`, { notes: 'Completed' });
    // Refresh the detail view
    if (selectedOnboarding.value) {
      const res = await axios.get(`/onboarding/${selectedOnboarding.value.id}`);
      selectedOnboarding.value = res.data;
    }
    fetchOnboardings(pagination.value?.current_page || 1);
    success('Task completed successfully');
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to complete task');
  }
};

const openSkipModal = (task) => {
  skippingTask.value = task;
  skipNotes.value = '';
  showSkipModal.value = true;
};

const skipTask = async () => {
  if (!skipNotes.value.trim()) return;
  try {
    await axios.post(`/onboarding/tasks/${skippingTask.value.id}/skip`, { notes: skipNotes.value });
    showSkipModal.value = false;
    if (selectedOnboarding.value) {
      const res = await axios.get(`/onboarding/${selectedOnboarding.value.id}`);
      selectedOnboarding.value = res.data;
    }
    fetchOnboardings(pagination.value?.current_page || 1);
    success('Task skipped');
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to skip task');
  }
};

const getEmployeeName = (emp) => {
  if (!emp) return 'N/A';
  return emp.user?.name || `${emp.first_name || ''} ${emp.last_name || ''}`.trim() || 'N/A';
};

const getBuddyName = (buddy) => {
  if (!buddy) return '';
  return buddy.user?.name || `${buddy.first_name || ''} ${buddy.last_name || ''}`.trim() || '';
};

const getInitials = (emp) => getEmployeeName(emp).split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

const formatDate = (d) => {
  if (!d) return '—';
  try { return new Date(d).toLocaleDateString('en-PK', { year: 'numeric', month: 'short', day: 'numeric' }); } catch { return d; }
};

const formatStatus = (s) => {
  if (!s) return '';
  return s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
};

const formatTaskStatus = (s) => {
  const map = { pending: 'Pending', in_progress: 'In Progress', completed: 'Completed', skipped: 'Skipped' };
  return map[s] || s;
};

const statusBadge = (s) => ({
  not_started: 'bg-yellow-100 text-yellow-800',
  in_progress: 'bg-blue-100 text-blue-800',
  completed: 'bg-green-100 text-green-800',
}[s] || 'bg-gray-100 text-gray-600');

const taskStatusBadge = (s) => ({
  pending: 'bg-yellow-100 text-yellow-800',
  in_progress: 'bg-blue-100 text-blue-800',
  completed: 'bg-green-100 text-green-800',
  skipped: 'bg-gray-100 text-gray-600',
}[s] || 'bg-gray-100 text-gray-600');

const progressBarColor = (pct) => {
  if (pct >= 100) return 'bg-green-500';
  if (pct >= 60) return 'bg-blue-500';
  if (pct >= 30) return 'bg-yellow-500';
  return 'bg-red-400';
};

onMounted(() => {
  fetchOnboardings();
  fetchEmployees();
  fetchBuddyEmployees();
  fetchTemplates();
});
</script>
