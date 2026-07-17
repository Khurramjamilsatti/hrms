<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Recruitment</h1>
      <div class="flex items-center gap-2">
        <button
          v-if="can('recruitment.create') && activeTab === 'applications'"
          @click="openApplicationModal()"
          class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-900 font-medium rounded-lg transition-colors shadow-sm"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          Add Application
        </button>
        <button
          v-if="can('recruitment.create') && activeTab === 'interviews'"
          @click="openInterviewModal()"
          class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-900 font-medium rounded-lg transition-colors shadow-sm"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          Schedule Interview
        </button>
        <button
          v-if="can('recruitment.create')"
          @click="openPositionModal()"
          class="inline-flex items-center px-5 py-2.5 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors shadow"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          Post New Job
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Open Positions</p>
        <h3 class="text-2xl font-bold text-green-600">{{ stats.openPositions }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Positions</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.totalPositions }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Pending Applications</p>
        <h3 class="text-2xl font-bold text-amber-600">{{ stats.pendingApplications }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">In Interview</p>
        <h3 class="text-2xl font-bold text-blue-600">{{ stats.interviewCount }}</h3>
      </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow border border-gray-200 mb-6">
      <div class="border-b border-gray-200 px-4">
        <nav class="flex gap-1 -mb-px">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            class="px-4 py-3 text-sm font-semibold border-b-2 transition-colors"
            :class="activeTab === tab.id ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700'"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>

      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
      </div>

      <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 m-4 rounded-lg">
        <p class="font-medium">{{ error }}</p>
        <button @click="loadData()" class="mt-2 text-sm underline">Try again</button>
      </div>

      <!-- Positions -->
      <div v-else-if="activeTab === 'positions'" class="overflow-x-auto">
        <div v-if="positions.length === 0" class="p-12 text-center text-gray-500">No job positions yet.</div>
        <table v-else class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Title</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Department</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Openings</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
              <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="position in positions" :key="position.id" class="hover:bg-gray-50">
              <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ position.title }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ position.department?.name || '—' }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ formatEmploymentType(position.employment_type) }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ position.positions_available ?? position.vacancies ?? '—' }}</td>
              <td class="px-5 py-4">
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold" :class="positionStatusClass(position.status)">{{ position.status }}</span>
              </td>
              <td class="px-5 py-4 text-right space-x-2">
                <button @click="openViewPosition(position)" class="text-sm text-gray-600 hover:text-gray-900 font-medium">View</button>
                <button v-if="can('recruitment.update')" @click="openPositionModal(position)" class="text-sm text-gray-600 hover:text-gray-900 font-medium">Edit</button>
                <button v-if="can('recruitment.delete')" @click="openDeletePosition(position)" class="text-sm text-red-600 hover:text-red-700 font-medium">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Applications -->
      <div v-else-if="activeTab === 'applications'" class="overflow-x-auto">
        <div v-if="applications.length === 0" class="p-12 text-center text-gray-500">No applications yet.</div>
        <table v-else class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Applicant</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Position</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Phone</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
              <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="app in applications" :key="app.id" class="hover:bg-gray-50">
              <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ applicantName(app) }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ positionTitle(app) }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ app.email }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ app.phone }}</td>
              <td class="px-5 py-4">
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold" :class="applicationStatusClass(app.status)">{{ app.status }}</span>
              </td>
              <td class="px-5 py-4 text-right">
                <button v-if="can('recruitment.manage')" @click="openReviewModal(app)" class="text-sm text-gray-900 hover:underline font-medium">Review</button>
                <button v-else @click="openReviewModal(app)" class="text-sm text-gray-600 hover:underline font-medium">View</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Interviews -->
      <div v-else-if="activeTab === 'interviews'" class="overflow-x-auto">
        <div v-if="interviews.length === 0" class="p-12 text-center text-gray-500">No interviews scheduled yet.</div>
        <table v-else class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Title</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Applicant</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Scheduled</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Interviewer</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Location</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
              <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="interview in interviews" :key="interview.id" class="hover:bg-gray-50">
              <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ interview.title }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">
                {{ applicantName(interview.job_application || interview.jobApplication) }}
                <div class="text-xs text-gray-400">{{ positionTitle(interview.job_application || interview.jobApplication) }}</div>
              </td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ formatDateTime(interview.scheduled_at) }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ interview.interviewer?.name || '—' }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ interview.location || interview.meeting_link || '—' }}</td>
              <td class="px-5 py-4">
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold" :class="interviewStatusClass(interview.status)">
                  {{ interview.status || 'scheduled' }}
                </span>
              </td>
              <td class="px-5 py-4 text-right">
                <button v-if="can('recruitment.update') || can('recruitment.create') || can('recruitment.manage')" @click="openInterviewModal(interview)" class="text-sm text-gray-900 hover:underline font-medium">Edit</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="interviewMeta.last_page > 1" class="px-5 py-3 border-t border-gray-200 flex items-center justify-between bg-gray-50">
          <p class="text-sm text-gray-500">Page {{ interviewMeta.current_page }} of {{ interviewMeta.last_page }}</p>
          <div class="flex gap-2">
            <button :disabled="interviewMeta.current_page <= 1" @click="loadInterviews(interviewMeta.current_page - 1)" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg disabled:opacity-40 hover:bg-white">Previous</button>
            <button :disabled="interviewMeta.current_page >= interviewMeta.last_page" @click="loadInterviews(interviewMeta.current_page + 1)" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg disabled:opacity-40 hover:bg-white">Next</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Schedule Interview Modal -->
    <div v-if="showInterviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingInterview ? 'Edit Interview' : 'Schedule Interview' }}</h3>
          <button @click="showInterviewModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4 overflow-y-auto">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Application *</label>
            <select v-model="interviewForm.job_application_id" :disabled="!!editingInterview" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent disabled:bg-gray-100">
              <option value="">Select application</option>
              <option v-for="app in applications" :key="app.id" :value="app.id">
                {{ applicantName(app) }} — {{ positionTitle(app) }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title *</label>
            <input v-model="interviewForm.title" type="text" placeholder="e.g. Technical Round 1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Scheduled At *</label>
            <input v-model="interviewForm.scheduled_at" type="datetime-local" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Interviewer *</label>
            <select v-model="interviewForm.interviewer_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
              <option value="">Select interviewer</option>
              <option v-for="emp in interviewers" :key="emp.user_id || emp.id" :value="emp.user_id || emp.user?.id">
                {{ emp.full_name || `${emp.first_name || ''} ${emp.last_name || ''}`.trim() || emp.user?.name || emp.name }}
              </option>
            </select>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Location</label>
              <input v-model="interviewForm.location" type="text" placeholder="Office / Room" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
              <select v-model="interviewForm.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                <option value="scheduled">Scheduled</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="rescheduled">Rescheduled</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Meeting Link</label>
            <input v-model="interviewForm.meeting_link" type="url" placeholder="https://..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Agenda</label>
            <textarea v-model="interviewForm.agenda" rows="3" placeholder="Interview agenda..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showInterviewModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveInterview" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">
            {{ saving ? 'Saving...' : (editingInterview ? 'Update' : 'Schedule') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Position Modal -->
    <div v-if="showPositionModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingPosition ? 'Edit Position' : 'Post New Job' }}</h3>
          <button @click="showPositionModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4 overflow-y-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Job Title *</label>
              <input v-model="positionForm.title" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" placeholder="e.g. Senior Developer" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Department *</label>
              <select v-model="positionForm.department_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                <option value="">Select department</option>
                <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Employment Type *</label>
              <select v-model="positionForm.employment_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                <option value="full_time">Full Time</option>
                <option value="part_time">Part Time</option>
                <option value="contract">Contract</option>
                <option value="internship">Internship</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Positions Available *</label>
              <input v-model.number="positionForm.positions_available" type="number" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
              <select v-model="positionForm.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                <option value="draft">Draft</option>
                <option value="open">Open</option>
                <option value="closed">Closed</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Salary Min</label>
              <input v-model.number="positionForm.salary_range_min" type="number" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Salary Max</label>
              <input v-model.number="positionForm.salary_range_max" type="number" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Description *</label>
              <textarea v-model="positionForm.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Role overview..."></textarea>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Requirements</label>
              <textarea v-model="positionForm.requirements" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Responsibilities</label>
              <textarea v-model="positionForm.responsibilities" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
            </div>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showPositionModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="savePosition" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">
            {{ saving ? 'Saving...' : (editingPosition ? 'Update' : 'Create') }}
          </button>
        </div>
      </div>
    </div>

    <!-- View Position Modal -->
    <div v-if="showViewPosition" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ viewingPosition?.title }}</h3>
          <button @click="showViewPosition = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div v-if="viewingPosition" class="px-6 py-5 space-y-4 overflow-y-auto">
          <div class="flex flex-wrap gap-2">
            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold" :class="positionStatusClass(viewingPosition.status)">{{ viewingPosition.status }}</span>
            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ formatEmploymentType(viewingPosition.employment_type) }}</span>
          </div>
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div><p class="text-gray-500">Department</p><p class="font-medium text-gray-900">{{ viewingPosition.department?.name || '—' }}</p></div>
            <div><p class="text-gray-500">Openings</p><p class="font-medium text-gray-900">{{ viewingPosition.positions_available ?? viewingPosition.vacancies ?? '—' }}</p></div>
            <div><p class="text-gray-500">Salary Range</p><p class="font-medium text-gray-900">{{ formatSalaryRange(viewingPosition) }}</p></div>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-700 mb-1">Description</p>
            <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ viewingPosition.description || '—' }}</p>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-700 mb-1">Requirements</p>
            <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ viewingPosition.requirements || '—' }}</p>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-700 mb-1">Responsibilities</p>
            <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ viewingPosition.responsibilities || '—' }}</p>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button v-if="can('recruitment.update')" @click="openPositionModal(viewingPosition); showViewPosition = false" class="px-4 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark">Edit</button>
          <button @click="showViewPosition = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Close</button>
        </div>
      </div>
    </div>

    <!-- Add Application Modal -->
    <div v-if="showApplicationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">Add Application</h3>
          <button @click="showApplicationModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Job Position *</label>
            <select v-model="applicationForm.job_position_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
              <option value="">Select position</option>
              <option v-for="p in openPositions" :key="p.id" :value="p.id">{{ p.title }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Applicant Name *</label>
            <input v-model="applicationForm.applicant_name" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Email *</label>
              <input v-model="applicationForm.email" type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Phone *</label>
              <input v-model="applicationForm.phone" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Cover Letter</label>
            <textarea v-model="applicationForm.cover_letter" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
            <select v-model="applicationForm.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
              <option value="applied">Applied</option>
              <option value="screening">Screening</option>
              <option value="interview">Interview</option>
              <option value="offered">Offered</option>
              <option value="hired">Hired</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showApplicationModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveApplication" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Create' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Review Application Modal -->
    <div v-if="showReviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">Review Application</h3>
          <button @click="showReviewModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div v-if="reviewingApplication" class="px-6 py-5 space-y-4">
          <div class="bg-gray-50 rounded-lg p-4 text-sm space-y-1">
            <p><span class="text-gray-500">Applicant:</span> <span class="font-medium text-gray-900">{{ applicantName(reviewingApplication) }}</span></p>
            <p><span class="text-gray-500">Position:</span> <span class="font-medium text-gray-900">{{ positionTitle(reviewingApplication) }}</span></p>
            <p><span class="text-gray-500">Email:</span> {{ reviewingApplication.email }}</p>
            <p><span class="text-gray-500">Phone:</span> {{ reviewingApplication.phone }}</p>
            <p v-if="reviewingApplication.cover_letter" class="pt-2"><span class="text-gray-500 block mb-1">Cover Letter:</span> <span class="whitespace-pre-wrap text-gray-700">{{ reviewingApplication.cover_letter }}</span></p>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
            <select v-model="reviewForm.status" :disabled="!can('recruitment.manage')" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent disabled:bg-gray-100">
              <option value="applied">Applied</option>
              <option value="screening">Screening</option>
              <option value="interview">Interview</option>
              <option value="offered">Offered</option>
              <option value="hired">Hired</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Notes / Interview Schedule</label>
            <textarea v-model="reviewForm.notes" rows="3" :disabled="!can('recruitment.manage')" placeholder="e.g. Interview on Monday 10am via Zoom..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent disabled:bg-gray-100"></textarea>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showReviewModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Close</button>
          <button v-if="can('recruitment.manage')" @click="saveReview" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Update Status' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Position Confirmation -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
        <div class="px-6 py-5 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
          </div>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Position</h3>
          <p class="text-sm text-gray-600">Delete <span class="font-semibold">{{ deletingPosition?.title }}</span>? This cannot be undone.</p>
          <div v-if="formError" class="mt-3 text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="deletePosition" :disabled="deleting" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePermissions } from '@/composables/usePermissions';
import axios from 'axios';

const { can } = usePermissions();

const tabs = [
  { id: 'positions', label: 'Positions' },
  { id: 'applications', label: 'Applications' },
  { id: 'interviews', label: 'Interviews' },
];

const activeTab = ref('positions');
const positions = ref([]);
const applications = ref([]);
const interviews = ref([]);
const interviewers = ref([]);
const departments = ref([]);
const loading = ref(false);
const error = ref(null);
const saving = ref(false);
const deleting = ref(false);
const formError = ref(null);

const showPositionModal = ref(false);
const showViewPosition = ref(false);
const showApplicationModal = ref(false);
const showReviewModal = ref(false);
const showDeleteModal = ref(false);
const showInterviewModal = ref(false);

const editingPosition = ref(null);
const viewingPosition = ref(null);
const reviewingApplication = ref(null);
const deletingPosition = ref(null);
const editingInterview = ref(null);

const interviewMeta = ref({ current_page: 1, last_page: 1 });

const emptyPositionForm = () => ({
  title: '',
  department_id: '',
  description: '',
  requirements: '',
  responsibilities: '',
  employment_type: 'full_time',
  salary_range_min: null,
  salary_range_max: null,
  positions_available: 1,
  status: 'draft',
});

const emptyInterviewForm = () => ({
  job_application_id: '',
  title: '',
  scheduled_at: '',
  location: '',
  meeting_link: '',
  agenda: '',
  interviewer_id: '',
  status: 'scheduled',
});

const positionForm = ref(emptyPositionForm());
const applicationForm = ref({
  job_position_id: '',
  applicant_name: '',
  email: '',
  phone: '',
  cover_letter: '',
  status: 'applied',
});
const reviewForm = ref({ status: 'applied', notes: '' });
const interviewForm = ref(emptyInterviewForm());

const extractList = (payload) => {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  return [];
};

const stats = computed(() => ({
  openPositions: positions.value.filter(p => p.status === 'open').length,
  totalPositions: positions.value.length,
  pendingApplications: applications.value.filter(a => a.status === 'applied').length,
  interviewCount: interviews.value.length || applications.value.filter(a => a.status === 'interview').length,
}));

const openPositions = computed(() =>
  positions.value.filter(p => p.status === 'open' || p.status === 'draft')
);

const applicantName = (app) =>
  app?.applicant_name || app?.full_name || `${app?.first_name || ''} ${app?.last_name || ''}`.trim() || '—';

const positionTitle = (app) =>
  app?.job_position?.title || app?.jobPosition?.title || '—';

const formatEmploymentType = (type) => {
  const map = { full_time: 'Full Time', part_time: 'Part Time', contract: 'Contract', internship: 'Internship', intern: 'Internship' };
  return map[type] || type || '—';
};

const formatSalaryRange = (pos) => {
  const min = pos.salary_range_min ?? pos.min_salary;
  const max = pos.salary_range_max ?? pos.max_salary;
  if (min == null && max == null) return '—';
  const fmt = (n) => Number(n).toLocaleString('en-PK');
  if (min != null && max != null) return `Rs. ${fmt(min)} – ${fmt(max)}`;
  if (min != null) return `From Rs. ${fmt(min)}`;
  return `Up to Rs. ${fmt(max)}`;
};

const formatDateTime = (value) => {
  if (!value) return '—';
  const d = new Date(value);
  if (Number.isNaN(d.getTime())) return value;
  return d.toLocaleString();
};

const toDatetimeLocal = (value) => {
  if (!value) return '';
  const d = new Date(value);
  if (Number.isNaN(d.getTime())) return '';
  const pad = (n) => String(n).padStart(2, '0');
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
};

const positionStatusClass = (status) => ({
  draft: 'bg-gray-100 text-gray-700',
  open: 'bg-green-100 text-green-800',
  closed: 'bg-red-100 text-red-700',
}[status] || 'bg-gray-100 text-gray-700');

const applicationStatusClass = (status) => ({
  applied: 'bg-blue-100 text-blue-800',
  screening: 'bg-amber-100 text-amber-800',
  interview: 'bg-indigo-100 text-indigo-800',
  offered: 'bg-green-100 text-green-800',
  hired: 'bg-emerald-100 text-emerald-800',
  rejected: 'bg-red-100 text-red-700',
}[status] || 'bg-gray-100 text-gray-700');

const interviewStatusClass = (status) => ({
  scheduled: 'bg-blue-100 text-blue-800',
  completed: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-700',
  rescheduled: 'bg-amber-100 text-amber-800',
}[status] || 'bg-gray-100 text-gray-700');

const loadInterviews = async (page = 1) => {
  try {
    const response = await axios.get('/recruitment/interviews', { params: { page, per_page: 15 } });
    const data = response.data;
    interviews.value = data.data || extractList(data);
    interviewMeta.value = {
      current_page: data.current_page || 1,
      last_page: data.last_page || 1,
    };
  } catch (err) {
    console.error('Failed to load interviews', err);
    interviews.value = [];
  }
};

const loadInterviewers = async () => {
  try {
    const response = await axios.get('/employees/dropdown');
    interviewers.value = extractList(response.data).filter(e => e.user_id || e.user?.id);
  } catch (err) {
    console.error('Failed to load interviewers', err);
    interviewers.value = [];
  }
};

const loadData = async () => {
  loading.value = true;
  error.value = null;
  try {
    const [posRes, appRes, deptRes] = await Promise.all([
      axios.get('/recruitment/positions'),
      axios.get('/recruitment/applications'),
      axios.get('/departments'),
    ]);
    positions.value = extractList(posRes.data);
    applications.value = extractList(appRes.data);
    departments.value = extractList(deptRes.data);
    await Promise.all([loadInterviews(), loadInterviewers()]);
  } catch (err) {
    error.value = 'Failed to load recruitment data';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const openPositionModal = (position = null) => {
  editingPosition.value = position;
  formError.value = null;
  if (position) {
    positionForm.value = {
      title: position.title || '',
      department_id: position.department_id || '',
      description: position.description || '',
      requirements: position.requirements || '',
      responsibilities: position.responsibilities || '',
      employment_type: position.employment_type === 'intern' ? 'internship' : (position.employment_type || 'full_time'),
      salary_range_min: position.salary_range_min ?? position.min_salary ?? null,
      salary_range_max: position.salary_range_max ?? position.max_salary ?? null,
      positions_available: position.positions_available ?? position.vacancies ?? 1,
      status: ['draft', 'open', 'closed'].includes(position.status) ? position.status : 'draft',
    };
  } else {
    positionForm.value = emptyPositionForm();
  }
  showPositionModal.value = true;
};

const openViewPosition = (position) => {
  viewingPosition.value = position;
  showViewPosition.value = true;
};

const savePosition = async () => {
  formError.value = null;
  if (!positionForm.value.title.trim()) { formError.value = 'Job title is required'; return; }
  if (!positionForm.value.department_id) { formError.value = 'Department is required'; return; }
  if (!positionForm.value.description.trim()) { formError.value = 'Description is required'; return; }
  saving.value = true;
  try {
    const payload = { ...positionForm.value };
    if (editingPosition.value) {
      await axios.put(`/recruitment/positions/${editingPosition.value.id}`, payload);
    } else {
      await axios.post('/recruitment/positions', payload);
    }
    showPositionModal.value = false;
    await loadData();
  } catch (err) {
    formError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {}).flat().join(' ') || 'Failed to save position';
  } finally {
    saving.value = false;
  }
};

const openDeletePosition = (position) => {
  deletingPosition.value = position;
  formError.value = null;
  showDeleteModal.value = true;
};

const deletePosition = async () => {
  deleting.value = true;
  formError.value = null;
  try {
    await axios.delete(`/recruitment/positions/${deletingPosition.value.id}`);
    showDeleteModal.value = false;
    await loadData();
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to delete position';
  } finally {
    deleting.value = false;
  }
};

const openApplicationModal = () => {
  formError.value = null;
  applicationForm.value = {
    job_position_id: '',
    applicant_name: '',
    email: '',
    phone: '',
    cover_letter: '',
    status: 'applied',
  };
  showApplicationModal.value = true;
};

const saveApplication = async () => {
  formError.value = null;
  const f = applicationForm.value;
  if (!f.job_position_id) { formError.value = 'Please select a position'; return; }
  if (!f.applicant_name.trim()) { formError.value = 'Applicant name is required'; return; }
  if (!f.email.trim()) { formError.value = 'Email is required'; return; }
  if (!f.phone.trim()) { formError.value = 'Phone is required'; return; }
  saving.value = true;
  try {
    await axios.post('/recruitment/applications', f);
    showApplicationModal.value = false;
    activeTab.value = 'applications';
    await loadData();
  } catch (err) {
    formError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {}).flat().join(' ') || 'Failed to create application';
  } finally {
    saving.value = false;
  }
};

const openReviewModal = (app) => {
  reviewingApplication.value = app;
  formError.value = null;
  reviewForm.value = {
    status: app.status || 'applied',
    notes: app.notes || '',
  };
  showReviewModal.value = true;
};

const saveReview = async () => {
  formError.value = null;
  if (!reviewForm.value.status) { formError.value = 'Status is required'; return; }
  saving.value = true;
  try {
    await axios.post(`/recruitment/applications/${reviewingApplication.value.id}/status`, {
      status: reviewForm.value.status,
      notes: reviewForm.value.notes || null,
    });
    showReviewModal.value = false;
    await loadData();
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to update application status';
  } finally {
    saving.value = false;
  }
};

const openInterviewModal = (interview = null) => {
  editingInterview.value = interview;
  formError.value = null;
  if (interview) {
    interviewForm.value = {
      job_application_id: interview.job_application_id || '',
      title: interview.title || '',
      scheduled_at: toDatetimeLocal(interview.scheduled_at),
      location: interview.location || '',
      meeting_link: interview.meeting_link || '',
      agenda: interview.agenda || '',
      interviewer_id: interview.interviewer_id || '',
      status: interview.status || 'scheduled',
    };
  } else {
    interviewForm.value = emptyInterviewForm();
  }
  showInterviewModal.value = true;
};

const saveInterview = async () => {
  formError.value = null;
  const f = interviewForm.value;
  if (!f.job_application_id) { formError.value = 'Application is required'; return; }
  if (!f.title.trim()) { formError.value = 'Title is required'; return; }
  if (!f.scheduled_at) { formError.value = 'Scheduled time is required'; return; }
  if (!f.interviewer_id) { formError.value = 'Interviewer is required'; return; }
  saving.value = true;
  try {
    const payload = {
      job_application_id: f.job_application_id,
      title: f.title,
      scheduled_at: f.scheduled_at,
      location: f.location || null,
      meeting_link: f.meeting_link || null,
      agenda: f.agenda || null,
      interviewer_id: f.interviewer_id,
      status: f.status || 'scheduled',
    };
    if (editingInterview.value) {
      await axios.put(`/recruitment/interviews/${editingInterview.value.id}`, payload);
    } else {
      await axios.post('/recruitment/interviews', payload);
    }
    showInterviewModal.value = false;
    activeTab.value = 'interviews';
    await loadInterviews();
  } catch (err) {
    formError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {}).flat().join(' ') || 'Failed to save interview';
  } finally {
    saving.value = false;
  }
};

onMounted(() => { loadData(); });
</script>
