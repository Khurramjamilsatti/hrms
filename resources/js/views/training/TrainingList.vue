<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Training Management</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your training courses and enrollments</p>
      </div>
      <button @click="openEnrollModal" class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Enrollment
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Search Course</label>
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input v-model="filters.search" type="text" placeholder="Search by course name..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
              @input="debounceSearch" />
          </div>
        </div>
        <div class="min-w-[160px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" @change="fetchEnrollments()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            <option value="">All Status</option>
            <option value="enrolled">Enrolled</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div class="min-w-[160px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
          <select v-model="filters.type" @change="fetchEnrollments()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            <option value="">All Types</option>
            <option value="technical">Technical</option>
            <option value="soft_skills">Soft Skills</option>
            <option value="leadership">Leadership</option>
            <option value="compliance">Compliance</option>
            <option value="other">Other</option>
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

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
      <p class="font-medium">{{ error }}</p>
      <button @click="fetchEnrollments()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <!-- Empty State -->
    <div v-else-if="enrollments.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
      </svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Training Enrollments</h3>
      <p class="text-gray-500">You haven't enrolled in any training courses yet.</p>
    </div>

    <!-- Enrollments Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="enrollment in enrollments" :key="enrollment.id" 
        class="bg-white rounded-lg shadow border border-gray-200 hover:shadow-lg transition-shadow overflow-hidden">
        <!-- Header -->
        <div class="p-5 border-b border-gray-200">
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1 pr-2">
              <h3 class="text-base font-bold text-gray-900 mb-1">{{ enrollment.employee?.first_name }} {{ enrollment.employee?.last_name }}</h3>
              <p class="text-sm text-gray-600 font-medium mb-0.5">{{ enrollment.session?.course?.name || 'N/A' }}</p>
              <p class="text-xs text-gray-500">{{ enrollment.session?.session_name || 'N/A' }}</p>
            </div>
            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full whitespace-nowrap flex-shrink-0" :class="getStatusClass(enrollment.status)">
              {{ formatStatus(enrollment.status) }}
            </span>
          </div>
          
          <!-- Course Type Badge -->
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
            {{ formatType(enrollment.session?.course?.type) }}
          </span>
        </div>

        <!-- Info -->
        <div class="p-5 bg-gray-50 space-y-2.5">
          <!-- Dates -->
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500 flex items-center">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Dates
            </span>
            <span class="font-medium text-gray-900">{{ formatDateShort(enrollment.session?.start_date) }} - {{ formatDateShort(enrollment.session?.end_date) }}</span>
          </div>

          <!-- Duration -->
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500 flex items-center">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Duration
            </span>
            <span class="font-medium text-gray-900">{{ enrollment.session?.course?.duration_hours || 0 }} hours</span>
          </div>

          <!-- Delivery Mode -->
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500 flex items-center">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              Mode
            </span>
            <span class="font-medium text-gray-900">{{ formatDeliveryMode(enrollment.session?.course?.delivery_mode) }}</span>
          </div>

          <!-- Instructor -->
          <div v-if="enrollment.session?.course?.instructor" class="flex items-center justify-between text-sm">
            <span class="text-gray-500 flex items-center">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Instructor
            </span>
            <span class="font-medium text-gray-900">{{ enrollment.session?.course?.instructor?.first_name }} {{ enrollment.session?.course?.instructor?.last_name }}</span>
          </div>

          <!-- Progress Bar (for in_progress) -->
          <div v-if="enrollment.status === 'in_progress' && enrollment.attendance_percentage" class="pt-2">
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs font-semibold text-gray-600">Progress</span>
              <span class="text-xs font-bold text-gray-900">{{ enrollment.attendance_percentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" :style="`width: ${enrollment.attendance_percentage}%`"></div>
            </div>
          </div>

          <!-- Score (for completed) -->
          <div v-if="enrollment.status === 'completed'" class="pt-2 border-t border-gray-200">
            <div class="flex items-center justify-between text-sm mb-2">
              <span class="text-gray-500">Final Score</span>
              <span class="text-lg font-bold" :class="enrollment.score >= 70 ? 'text-green-600' : 'text-red-600'">
                {{ enrollment.score || 0 }}%
              </span>
            </div>
            <div v-if="enrollment.certificate_issued" class="flex items-center text-sm text-green-600">
              <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              Certificate Issued
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="p-4 border-t border-gray-200 flex items-center gap-2">
          <button @click="viewDetails(enrollment)" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            View
          </button>
          <button v-if="authStore.user?.role !== 'employee'" @click="editEnrollment(enrollment)" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit
          </button>
          <button v-if="authStore.user?.role !== 'employee' && !enrollment.certificate_issued" @click="confirmDelete(enrollment)" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="enrollments.length > 0 && pagination && pagination.last_page > 1" class="flex items-center justify-between bg-white rounded-lg shadow border border-gray-200 px-6 py-4 mt-6">
      <div class="text-sm text-gray-600">Showing <span class="font-semibold text-gray-900">{{ pagination.total }}</span> records</div>
      <div class="flex items-center space-x-2">
        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :class="pagination.current_page === 1 ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'">Previous</button>
        <span class="text-sm text-gray-600">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
        <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :class="pagination.current_page === pagination.last_page ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'">Next</button>
      </div>
    </div>

    <!-- Enrollment Form Modal (Create/Edit) -->
    <div v-if="showEnrollModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingEnrollment ? 'Edit Enrollment' : 'New Enrollment' }}</h3>
          <button @click="closeEnrollModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
          </button>
        </div>
        <form @submit.prevent="saveEnrollment" class="p-6 space-y-4">
          <div v-if="!editingEnrollment">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Employee *</label>
            <div class="relative" @click.outside="showEmployeeDropdown = false">
              <input 
                v-model="employeeSearch" 
                @input="filterEmployees"
                @focus="showEmployeeDropdown = true"
                type="text" 
                placeholder="Search employee by name or ID..." 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
                required
              />
              <div v-if="showEmployeeDropdown && filteredEmployees.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                <button 
                  v-for="emp in filteredEmployees" 
                  :key="emp.id" 
                  type="button"
                  @click="selectEmployee(emp)"
                  class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center justify-between"
                >
                  <span>{{ emp.first_name }} {{ emp.last_name }}</span>
                  <span class="text-sm text-gray-500">{{ emp.employee_id }} - {{ emp.department?.name }}</span>
                </button>
              </div>
            </div>
            <p v-if="enrollmentForm.employee_id" class="mt-1 text-sm text-gray-600">
              Selected: {{ selectedEmployeeName }}
            </p>
          </div>

          <div v-if="!editingEnrollment && enrollmentForm.employee_id">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Training Session *</label>
            <select v-model="enrollmentForm.session_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
              <option value="">Select Session</option>
              <option v-for="session in filteredSessions" :key="session.id" :value="session.id">
                {{ session.session_name }} ({{ session.course?.name }}) - {{ session.course?.department?.name }}
              </option>
            </select>
            <p v-if="filteredSessions.length === 0" class="mt-1 text-sm text-red-600">
              No training sessions available for this employee's department
            </p>
          </div>

          <div v-if="editingEnrollment">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
            <select v-model="enrollmentForm.status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
              <option value="enrolled">Enrolled</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
              <option value="failed">Failed</option>
            </select>
          </div>

          <div v-if="editingEnrollment && (enrollmentForm.status === 'completed' || enrollmentForm.status === 'failed')">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Score (%)</label>
            <input v-model.number="enrollmentForm.score" type="number" min="0" max="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
          </div>

          <div v-if="editingEnrollment">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Attendance (%)</label>
            <input v-model.number="enrollmentForm.attendance_percentage" type="number" min="0" max="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
          </div>

          <div v-if="editingEnrollment && enrollmentForm.status === 'completed'">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Completion Date</label>
            <input v-model="enrollmentForm.completion_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
          </div>

          <div v-if="editingEnrollment">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Feedback</label>
            <textarea v-model="enrollmentForm.feedback" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="Enter feedback..."></textarea>
          </div>

          <div v-if="editingEnrollment">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Rating (1-5)</label>
            <select v-model.number="enrollmentForm.rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
              <option :value="null">No Rating</option>
              <option :value="1">⭐ 1 - Poor</option>
              <option :value="2">⭐⭐ 2 - Fair</option>
              <option :value="3">⭐⭐⭐ 3 - Good</option>
              <option :value="4">⭐⭐⭐⭐ 4 - Very Good</option>
              <option :value="5">⭐⭐⭐⭐⭐ 5 - Excellent</option>
            </select>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-4">
            <button type="button" @click="closeEnrollModal" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
              Cancel
            </button>
            <button type="submit" :disabled="submitting" class="px-5 py-2.5 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 rounded-lg transition-colors disabled:opacity-50">
              {{ submitting ? 'Saving...' : (editingEnrollment ? 'Update' : 'Enroll') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Details Modal -->
    <div v-if="selectedEnrollment" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">Training Details</h3>
          <button @click="selectedEnrollment = null" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
          </button>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Employee</h4>
            <p class="text-gray-900">{{ selectedEnrollment.employee?.first_name }} {{ selectedEnrollment.employee?.last_name }}</p>
          </div>
          <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Course Name</h4>
            <p class="text-gray-900">{{ selectedEnrollment.session?.course?.name }}</p>
          </div>
          <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Session</h4>
            <p class="text-gray-900">{{ selectedEnrollment.session?.session_name }}</p>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <h4 class="text-sm font-semibold text-gray-700 mb-2">Start Date</h4>
              <p class="text-gray-900">{{ formatDate(selectedEnrollment.session?.start_date) }}</p>
            </div>
            <div>
              <h4 class="text-sm font-semibold text-gray-700 mb-2">End Date</h4>
              <p class="text-gray-900">{{ formatDate(selectedEnrollment.session?.end_date) }}</p>
            </div>
          </div>
          <div v-if="selectedEnrollment.status === 'completed'">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Performance</h4>
            <div class="space-y-2 bg-gray-50 rounded-lg p-4">
              <div class="flex justify-between">
                <span class="text-gray-600">Score:</span>
                <span class="font-semibold text-gray-900">{{ selectedEnrollment.score }}%</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Attendance:</span>
                <span class="font-semibold text-gray-900">{{ selectedEnrollment.attendance_percentage }}%</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Certificate:</span>
                <span class="font-semibold" :class="selectedEnrollment.certificate_issued ? 'text-green-600' : 'text-red-600'">
                  {{ selectedEnrollment.certificate_issued ? 'Issued' : 'Not Issued' }}
                </span>
              </div>
            </div>
          </div>
          <div v-if="selectedEnrollment.feedback">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Feedback</h4>
            <p class="text-gray-900 bg-gray-50 rounded-lg p-4">{{ selectedEnrollment.feedback }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="deletingEnrollment" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="p-6">
          <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Delete Enrollment</h3>
          <p class="text-sm text-gray-600 text-center mb-6">
            Are you sure you want to delete this enrollment? This action cannot be undone.
          </p>
          <div class="flex items-center justify-center space-x-3">
            <button @click="deletingEnrollment = null" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
              Cancel
            </button>
            <button @click="deleteEnrollment" :disabled="submitting" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors disabled:opacity-50">
              {{ submitting ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const enrollments = ref([]);
const employees = ref([]);
const filteredEmployees = ref([]);
const sessions = ref([]);
const filteredSessions = ref([]);
const loading = ref(false);
const error = ref(null);
const showEnrollModal = ref(false);
const showEmployeeDropdown = ref(false);
const employeeSearch = ref('');
const selectedEmployeeName = ref('');
const selectedEnrollment = ref(null);
const editingEnrollment = ref(null);
const deletingEnrollment = ref(null);
const submitting = ref(false);
const pagination = ref(null);

const filters = ref({
  search: '',
  status: '',
  type: ''
});

const enrollmentForm = ref({
  employee_id: '',
  session_id: '',
  status: 'enrolled',
  score: null,
  attendance_percentage: null,
  completion_date: null,
  feedback: '',
  rating: null
});

let searchTimer = null;

const debounceSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    fetchEnrollments();
  }, 500);
};

const fetchEnrollments = async (page = 1) => {
  loading.value = true;
  error.value = null;
  try {
    const params = {
      page
    };
    
    // Only add non-empty filter values
    if (filters.value.search) params.search = filters.value.search;
    if (filters.value.status) params.status = filters.value.status;
    if (filters.value.type) params.type = filters.value.type;
    
    // Only add employee_id if user has an employee record (for employees viewing their own)
    if (authStore.user?.employee?.id) {
      params.employee_id = authStore.user.employee.id;
    }
    
    const response = await axios.get('/training/enrollments', { params });
    enrollments.value = response.data.data || response.data;
    pagination.value = {
      current_page: response.data.current_page || 1,
      last_page: response.data.last_page || 1,
      total: response.data.total || enrollments.value.length
    };
  } catch (err) {
    console.error('Failed to fetch enrollments:', err);
    error.value = 'Failed to load training enrollments. Please try again.';
    enrollments.value = [];
  } finally {
    loading.value = false;
  }
};

const fetchEmployees = async () => {
  try {
    const response = await axios.get('/employees', { params: { per_page: 1000 } });
    employees.value = response.data.data || response.data;
    filteredEmployees.value = employees.value;
  } catch (err) {
    console.error('Failed to fetch employees:', err);
  }
};

const filterEmployees = () => {
  const search = employeeSearch.value.toLowerCase();
  if (!search) {
    filteredEmployees.value = employees.value;
  } else {
    filteredEmployees.value = employees.value.filter(emp => 
      emp.first_name?.toLowerCase().includes(search) ||
      emp.last_name?.toLowerCase().includes(search) ||
      emp.employee_id?.toLowerCase().includes(search)
    );
  }
  showEmployeeDropdown.value = true;
};

const selectEmployee = async (employee) => {
  enrollmentForm.value.employee_id = employee.id;
  employeeSearch.value = `${employee.first_name} ${employee.last_name}`;
  selectedEmployeeName.value = `${employee.first_name} ${employee.last_name} (${employee.employee_id})`;
  showEmployeeDropdown.value = false;
  
  // Fetch sessions for this employee's department
  if (employee.department_id) {
    await fetchSessionsByDepartment(employee.department_id);
  }
};

const fetchSessions = async () => {
  try {
    const response = await axios.get('/training/sessions');
    sessions.value = response.data.data || response.data;
  } catch (err) {
    console.error('Failed to fetch sessions:', err);
  }
};

const fetchSessionsByDepartment = async (departmentId) => {
  try {
    // Get courses for this department
    const coursesResponse = await axios.get('/training/courses', { 
      params: { department_id: departmentId, per_page: 1000 } 
    });
    const departmentCourses = coursesResponse.data.data || coursesResponse.data;
    const courseIds = departmentCourses.map(c => c.id);
    
    // Filter sessions by these courses
    const sessionsResponse = await axios.get('/training/sessions', { params: { per_page: 1000 } });
    const allSessions = sessionsResponse.data.data || sessionsResponse.data;
    filteredSessions.value = allSessions.filter(s => courseIds.includes(s.course_id));
  } catch (err) {
    console.error('Failed to fetch sessions by department:', err);
    filteredSessions.value = [];
  }
};

const openEnrollModal = () => {
  editingEnrollment.value = null;
  employeeSearch.value = '';
  selectedEmployeeName.value = '';
  showEmployeeDropdown.value = false;
  filteredSessions.value = [];
  enrollmentForm.value = {
    employee_id: '',
    session_id: '',
    status: 'enrolled',
    score: null,
    attendance_percentage: null,
    completion_date: null,
    feedback: '',
    rating: null
  };
  showEnrollModal.value = true;
  fetchEmployees();
};

const editEnrollment = (enrollment) => {
  editingEnrollment.value = enrollment;
  enrollmentForm.value = {
    status: enrollment.status,
    score: enrollment.score,
    attendance_percentage: enrollment.attendance_percentage,
    completion_date: enrollment.completion_date,
    feedback: enrollment.feedback || '',
    rating: enrollment.rating
  };
  showEnrollModal.value = true;
};

const closeEnrollModal = () => {
  showEnrollModal.value = false;
  editingEnrollment.value = null;
  employeeSearch.value = '';
  selectedEmployeeName.value = '';
  showEmployeeDropdown.value = false;
  filteredSessions.value = [];
  enrollmentForm.value = {
    employee_id: '',
    session_id: '',
    status: 'enrolled',
    score: null,
    attendance_percentage: null,
    completion_date: null,
    feedback: '',
    rating: null
  };
};

const saveEnrollment = async () => {
  submitting.value = true;
  try {
    if (editingEnrollment.value) {
      // Update existing enrollment
      await axios.put(`/training/enrollments/${editingEnrollment.value.id}`, enrollmentForm.value);
    } else {
      // Create new enrollment
      await axios.post('/training/enrollments', enrollmentForm.value);
    }
    closeEnrollModal();
    fetchEnrollments();
  } catch (err) {
    console.error('Failed to save enrollment:', err);
    alert(err.response?.data?.message || 'Failed to save enrollment');
  } finally {
    submitting.value = false;
  }
};

const confirmDelete = (enrollment) => {
  deletingEnrollment.value = enrollment;
};

const deleteEnrollment = async () => {
  submitting.value = true;
  try {
    await axios.delete(`/training/enrollments/${deletingEnrollment.value.id}`);
    deletingEnrollment.value = null;
    fetchEnrollments();
  } catch (err) {
    console.error('Failed to delete enrollment:', err);
    alert(err.response?.data?.message || 'Failed to delete enrollment');
  } finally {
    submitting.value = false;
  }
};

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchEnrollments(page);
  }
};

const viewDetails = (enrollment) => {
  selectedEnrollment.value = enrollment;
};

const resetFilters = () => {
  filters.value = {
    search: '',
    status: '',
    type: ''
  };
  fetchEnrollments();
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-PK', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
};

const formatDateShort = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-PK', { 
    month: 'short', 
    day: 'numeric'
  });
};

const formatStatus = (status) => {
  const statusMap = {
    enrolled: 'Enrolled',
    in_progress: 'In Progress',
    completed: 'Completed',
    cancelled: 'Cancelled'
  };
  return statusMap[status] || status;
};

const formatType = (type) => {
  const typeMap = {
    technical: 'Technical',
    soft_skills: 'Soft Skills',
    leadership: 'Leadership',
    compliance: 'Compliance',
    other: 'Other'
  };
  return typeMap[type] || type || 'N/A';
};

const formatDeliveryMode = (mode) => {
  const modeMap = {
    online: 'Online',
    classroom: 'Classroom',
    hybrid: 'Hybrid',
    self_paced: 'Self-Paced'
  };
  return modeMap[mode] || mode || 'N/A';
};

const getStatusClass = (status) => {
  const classes = {
    enrolled: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  fetchEnrollments();
});
</script>
