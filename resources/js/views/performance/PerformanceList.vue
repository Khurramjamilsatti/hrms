<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Performance Management</h1>
      <div class="flex items-center gap-2">
        <button
          v-if="can('performance.manage') && activeTab === 'cycles'"
          @click="openCycleModal()"
          class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-900 font-medium rounded-lg transition-colors shadow-sm"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          New Cycle
        </button>
        <button
          v-if="can('performance.create') && activeTab === 'goals'"
          @click="openGoalModal()"
          class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-900 font-medium rounded-lg transition-colors shadow-sm"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          Add Goal
        </button>
        <button
          v-if="can('performance.create')"
          @click="openReviewModal()"
          class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow"
        >
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          Create Review
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Reviews</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.totalReviews }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Active Goals</p>
        <h3 class="text-2xl font-bold text-green-600">{{ stats.activeGoals }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Average Rating</p>
        <h3 class="text-2xl font-bold text-amber-600">{{ stats.avgRating }}</h3>
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

      <!-- Reviews -->
      <div v-else-if="activeTab === 'reviews'" class="overflow-x-auto">
        <div v-if="reviews.length === 0" class="p-12 text-center text-gray-500">No performance reviews yet.</div>
        <table v-else class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employee</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Review Date</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cycle</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Rating</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
              <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="review in reviews" :key="review.id" class="hover:bg-gray-50">
              <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ getEmployeeName(review.employee) }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ formatDate(review.review_date) }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ cycleName(review) }}</td>
              <td class="px-5 py-4 text-sm text-gray-900">
                <span v-if="review.overall_rating != null || review.rating != null" class="inline-flex items-center">
                  <span class="text-amber-500 mr-1">★</span>
                  {{ Number(review.overall_rating ?? review.rating).toFixed(1) }}/5
                </span>
                <span v-else class="text-gray-400">—</span>
              </td>
              <td class="px-5 py-4">
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold" :class="reviewStatusClass(review.status)">{{ review.status }}</span>
              </td>
              <td class="px-5 py-4 text-right space-x-2">
                <button @click="openViewReview(review)" class="text-sm text-gray-600 hover:text-gray-900 font-medium">View</button>
                <button v-if="can('performance.update')" @click="openReviewModal(review)" class="text-sm text-gray-600 hover:text-gray-900 font-medium">Edit</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Goals -->
      <div v-else-if="activeTab === 'goals'" class="overflow-x-auto">
        <div v-if="goals.length === 0" class="p-12 text-center text-gray-500">No goals yet.</div>
        <table v-else class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Employee</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Title</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Target Date</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Progress</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
              <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="goal in goals" :key="goal.id" class="hover:bg-gray-50">
              <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ getEmployeeName(goal.employee) }}</td>
              <td class="px-5 py-4 text-sm text-gray-900">{{ goal.title }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ formatDate(goal.target_date) }}</td>
              <td class="px-5 py-4 min-w-[140px]">
                <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                  <div class="bg-gray-900 h-2 rounded-full" :style="{ width: Math.min(100, goalProgress(goal)) + '%' }"></div>
                </div>
                <span class="text-xs text-gray-500">{{ goalProgress(goal) }}%</span>
              </td>
              <td class="px-5 py-4">
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold" :class="goalStatusClass(goal.status)">{{ formatGoalStatus(goal.status) }}</span>
              </td>
              <td class="px-5 py-4 text-right space-x-2">
                <button @click="openViewGoal(goal)" class="text-sm text-gray-600 hover:text-gray-900 font-medium">View</button>
                <button v-if="can('performance.update')" @click="openGoalModal(goal)" class="text-sm text-gray-600 hover:text-gray-900 font-medium">Edit</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Cycles -->
      <div v-else-if="activeTab === 'cycles'" class="overflow-x-auto">
        <div v-if="cycles.length === 0" class="p-12 text-center text-gray-500">No review cycles yet.</div>
        <table v-else class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Start</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">End</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
              <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="cycle in cycles" :key="cycle.id" class="hover:bg-gray-50">
              <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ cycle.name || cycle.title }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ formatDate(cycle.start_date) }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ formatDate(cycle.end_date) }}</td>
              <td class="px-5 py-4">
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold" :class="cycleStatusClass(cycle.status)">{{ cycle.status }}</span>
              </td>
              <td class="px-5 py-4 text-right">
                <button @click="openViewCycle(cycle)" class="text-sm text-gray-600 hover:text-gray-900 font-medium">View</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Review Modal -->
    <div v-if="showReviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingReview ? 'Edit Review' : 'Create Review' }}</h3>
          <button @click="showReviewModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4 overflow-y-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Employee *</label>
              <select v-model="reviewForm.employee_id" :disabled="!!editingReview" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:bg-gray-100">
                <option value="">Select employee</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ getEmployeeName(emp) }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Cycle *</label>
              <select v-model="reviewForm.cycle_id" :disabled="!!editingReview" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:bg-gray-100">
                <option value="">Select cycle</option>
                <option v-for="c in cycles" :key="c.id" :value="c.id">{{ c.name || c.title }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Review Date *</label>
              <input v-model="reviewForm.review_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Overall Rating (1–5) *</label>
              <input v-model.number="reviewForm.overall_rating" type="number" min="1" max="5" step="0.1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
              <select v-model="reviewForm.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="draft">Draft</option>
                <option value="submitted">Submitted</option>
                <option value="acknowledged">Acknowledged</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Strengths</label>
              <textarea v-model="reviewForm.strengths" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"></textarea>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Areas for Improvement</label>
              <textarea v-model="reviewForm.areas_for_improvement" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"></textarea>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Goals for Next Period</label>
              <textarea v-model="reviewForm.goals_for_next_period" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"></textarea>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Reviewer Comments</label>
              <textarea v-model="reviewForm.reviewer_comments" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"></textarea>
            </div>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showReviewModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveReview" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50">
            {{ saving ? 'Saving...' : (editingReview ? 'Update' : 'Create') }}
          </button>
        </div>
      </div>
    </div>

    <!-- View Review Modal -->
    <div v-if="showViewReview" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">Performance Review</h3>
          <button @click="showViewReview = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div v-if="viewingReview" class="px-6 py-5 space-y-4 overflow-y-auto text-sm">
          <div class="grid grid-cols-2 gap-4">
            <div><p class="text-gray-500">Employee</p><p class="font-medium text-gray-900">{{ getEmployeeName(viewingReview.employee) }}</p></div>
            <div><p class="text-gray-500">Cycle</p><p class="font-medium text-gray-900">{{ cycleName(viewingReview) }}</p></div>
            <div><p class="text-gray-500">Review Date</p><p class="font-medium text-gray-900">{{ formatDate(viewingReview.review_date) }}</p></div>
            <div>
              <p class="text-gray-500">Rating</p>
              <p class="font-medium text-gray-900">
                <template v-if="viewingReview.overall_rating != null || viewingReview.rating != null">
                  {{ Number(viewingReview.overall_rating ?? viewingReview.rating).toFixed(1) }}/5
                </template>
                <template v-else>—</template>
              </p>
            </div>
            <div>
              <p class="text-gray-500">Status</p>
              <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold" :class="reviewStatusClass(viewingReview.status)">{{ viewingReview.status }}</span>
            </div>
          </div>
          <div><p class="font-semibold text-gray-700 mb-1">Strengths</p><p class="text-gray-600 whitespace-pre-wrap">{{ viewingReview.strengths || '—' }}</p></div>
          <div><p class="font-semibold text-gray-700 mb-1">Areas for Improvement</p><p class="text-gray-600 whitespace-pre-wrap">{{ viewingReview.areas_for_improvement || viewingReview.areas_of_improvement || '—' }}</p></div>
          <div><p class="font-semibold text-gray-700 mb-1">Goals for Next Period</p><p class="text-gray-600 whitespace-pre-wrap">{{ viewingReview.goals_for_next_period || viewingReview.goals_achieved || '—' }}</p></div>
          <div><p class="font-semibold text-gray-700 mb-1">Reviewer Comments</p><p class="text-gray-600 whitespace-pre-wrap">{{ viewingReview.reviewer_comments || viewingReview.comments || '—' }}</p></div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button v-if="can('performance.update')" @click="openReviewModal(viewingReview); showViewReview = false" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">Edit</button>
          <button @click="showViewReview = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Close</button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Goal Modal -->
    <div v-if="showGoalModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingGoal ? 'Edit Goal' : 'Add Goal' }}</h3>
          <button @click="showGoalModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Employee *</label>
            <select v-model="goalForm.employee_id" :disabled="!!editingGoal" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:bg-gray-100">
              <option value="">Select employee</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ getEmployeeName(emp) }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title *</label>
            <input v-model="goalForm.title" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea v-model="goalForm.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Target Date *</label>
              <input v-model="goalForm.target_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Progress %</label>
              <input v-model.number="goalForm.progress_percentage" type="number" min="0" max="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
            <select v-model="goalForm.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
              <option value="not_started">Not Started</option>
              <option value="in_progress">In Progress</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showGoalModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveGoal" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50">
            {{ saving ? 'Saving...' : (editingGoal ? 'Update' : 'Create') }}
          </button>
        </div>
      </div>
    </div>

    <!-- View Goal Modal -->
    <div v-if="showViewGoal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ viewingGoal?.title }}</h3>
          <button @click="showViewGoal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div v-if="viewingGoal" class="px-6 py-5 space-y-3 text-sm">
          <div class="grid grid-cols-2 gap-3">
            <div><p class="text-gray-500">Employee</p><p class="font-medium text-gray-900">{{ getEmployeeName(viewingGoal.employee) }}</p></div>
            <div><p class="text-gray-500">Target Date</p><p class="font-medium text-gray-900">{{ formatDate(viewingGoal.target_date) }}</p></div>
            <div>
              <p class="text-gray-500">Status</p>
              <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold" :class="goalStatusClass(viewingGoal.status)">{{ formatGoalStatus(viewingGoal.status) }}</span>
            </div>
            <div><p class="text-gray-500">Progress</p><p class="font-medium text-gray-900">{{ goalProgress(viewingGoal) }}%</p></div>
          </div>
          <div><p class="font-semibold text-gray-700 mb-1">Description</p><p class="text-gray-600 whitespace-pre-wrap">{{ viewingGoal.description || '—' }}</p></div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button v-if="can('performance.update')" @click="openGoalModal(viewingGoal); showViewGoal = false" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">Edit</button>
          <button @click="showViewGoal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Close</button>
        </div>
      </div>
    </div>

    <!-- Create Cycle Modal -->
    <div v-if="showCycleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">New Review Cycle</h3>
          <button @click="showCycleModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Name *</label>
            <input v-model="cycleForm.name" type="text" placeholder="e.g. Q1 2026" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date *</label>
              <input v-model="cycleForm.start_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">End Date *</label>
              <input v-model="cycleForm.end_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
            <select v-model="cycleForm.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
              <option value="upcoming">Upcoming</option>
              <option value="active">Active</option>
              <option value="completed">Completed</option>
            </select>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showCycleModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveCycle" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Create' }}
          </button>
        </div>
      </div>
    </div>

    <!-- View Cycle Modal -->
    <div v-if="showViewCycle" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ viewingCycle?.name || viewingCycle?.title }}</h3>
          <button @click="showViewCycle = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div v-if="viewingCycle" class="px-6 py-5 space-y-3 text-sm">
          <div><p class="text-gray-500">Start</p><p class="font-medium text-gray-900">{{ formatDate(viewingCycle.start_date) }}</p></div>
          <div><p class="text-gray-500">End</p><p class="font-medium text-gray-900">{{ formatDate(viewingCycle.end_date) }}</p></div>
          <div>
            <p class="text-gray-500">Status</p>
            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold" :class="cycleStatusClass(viewingCycle.status)">{{ viewingCycle.status }}</span>
          </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end bg-gray-50">
          <button @click="showViewCycle = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Close</button>
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
  { id: 'reviews', label: 'Reviews' },
  { id: 'goals', label: 'Goals' },
  { id: 'cycles', label: 'Cycles' },
];

const activeTab = ref('reviews');
const reviews = ref([]);
const goals = ref([]);
const cycles = ref([]);
const employees = ref([]);
const loading = ref(false);
const error = ref(null);
const saving = ref(false);
const formError = ref(null);

const showReviewModal = ref(false);
const showViewReview = ref(false);
const showGoalModal = ref(false);
const showViewGoal = ref(false);
const showCycleModal = ref(false);
const showViewCycle = ref(false);

const editingReview = ref(null);
const viewingReview = ref(null);
const editingGoal = ref(null);
const viewingGoal = ref(null);
const viewingCycle = ref(null);

const today = () => new Date().toISOString().split('T')[0];

const emptyReviewForm = () => ({
  employee_id: '',
  cycle_id: '',
  review_date: today(),
  overall_rating: 3,
  strengths: '',
  areas_for_improvement: '',
  goals_for_next_period: '',
  reviewer_comments: '',
  status: 'draft',
});

const emptyGoalForm = () => ({
  employee_id: '',
  title: '',
  description: '',
  target_date: today(),
  status: 'not_started',
  progress_percentage: 0,
});

const emptyCycleForm = () => ({
  name: '',
  start_date: today(),
  end_date: '',
  status: 'upcoming',
});

const reviewForm = ref(emptyReviewForm());
const goalForm = ref(emptyGoalForm());
const cycleForm = ref(emptyCycleForm());

const extractList = (payload) => {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  return [];
};

const getEmployeeName = (emp) =>
  emp?.user?.name || `${emp?.first_name || ''} ${emp?.last_name || ''}`.trim() || 'N/A';

const formatDate = (date) => {
  if (!date) return '—';
  return new Date(date).toLocaleDateString('en-PK');
};

const cycleName = (review) =>
  review?.cycle?.name ||
  review?.cycle?.title ||
  review?.review_cycle?.title ||
  review?.reviewCycle?.title ||
  '—';

const goalProgress = (goal) => Number(goal?.progress_percentage ?? goal?.progress ?? 0);

const formatGoalStatus = (status) =>
  ({ not_started: 'Not Started', in_progress: 'In Progress', completed: 'Completed', cancelled: 'Cancelled' }[status] || status);

const reviewStatusClass = (status) => ({
  draft: 'bg-gray-100 text-gray-700',
  submitted: 'bg-amber-100 text-amber-800',
  acknowledged: 'bg-green-100 text-green-800',
}[status] || 'bg-gray-100 text-gray-700');

const goalStatusClass = (status) => ({
  not_started: 'bg-gray-100 text-gray-700',
  in_progress: 'bg-blue-100 text-blue-800',
  completed: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-700',
}[status] || 'bg-gray-100 text-gray-700');

const cycleStatusClass = (status) => ({
  upcoming: 'bg-gray-100 text-gray-700',
  active: 'bg-green-100 text-green-800',
  completed: 'bg-blue-100 text-blue-800',
}[status] || 'bg-gray-100 text-gray-700');

const stats = computed(() => {
  const rated = reviews.value.filter(r => (r.overall_rating ?? r.rating) != null);
  const sum = rated.reduce((acc, r) => acc + Number(r.overall_rating ?? r.rating), 0);
  return {
    totalReviews: reviews.value.length,
    activeGoals: goals.value.filter(g => g.status === 'in_progress').length,
    avgRating: rated.length ? (sum / rated.length).toFixed(1) : '—',
  };
});

const loadData = async () => {
  loading.value = true;
  error.value = null;
  try {
    const [reviewsRes, goalsRes, cyclesRes, empRes] = await Promise.all([
      axios.get('/performance/reviews'),
      axios.get('/performance/goals'),
      axios.get('/performance/cycles'),
      axios.get('/employees/dropdown'),
    ]);
    reviews.value = extractList(reviewsRes.data);
    goals.value = extractList(goalsRes.data);
    cycles.value = extractList(cyclesRes.data);
    employees.value = extractList(empRes.data);
  } catch (err) {
    error.value = 'Failed to load performance data';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const openReviewModal = (review = null) => {
  editingReview.value = review;
  formError.value = null;
  if (review) {
    reviewForm.value = {
      employee_id: review.employee_id || '',
      cycle_id: review.cycle_id || review.review_cycle_id || '',
      review_date: review.review_date ? String(review.review_date).substring(0, 10) : today(),
      overall_rating: review.overall_rating ?? review.rating ?? 3,
      strengths: review.strengths || '',
      areas_for_improvement: review.areas_for_improvement || review.areas_of_improvement || '',
      goals_for_next_period: review.goals_for_next_period || '',
      reviewer_comments: review.reviewer_comments || review.comments || '',
      status: review.status || 'draft',
    };
  } else {
    reviewForm.value = emptyReviewForm();
  }
  showReviewModal.value = true;
};

const openViewReview = (review) => {
  viewingReview.value = review;
  showViewReview.value = true;
};

const saveReview = async () => {
  formError.value = null;
  const f = reviewForm.value;
  if (!editingReview.value) {
    if (!f.employee_id) { formError.value = 'Employee is required'; return; }
    if (!f.cycle_id) { formError.value = 'Cycle is required'; return; }
  }
  if (!f.review_date) { formError.value = 'Review date is required'; return; }
  if (f.overall_rating == null || f.overall_rating < 1 || f.overall_rating > 5) {
    formError.value = 'Overall rating must be between 1 and 5';
    return;
  }
  saving.value = true;
  try {
    if (editingReview.value) {
      await axios.put(`/performance/reviews/${editingReview.value.id}`, {
        review_date: f.review_date,
        overall_rating: f.overall_rating,
        strengths: f.strengths || null,
        areas_for_improvement: f.areas_for_improvement || null,
        goals_for_next_period: f.goals_for_next_period || null,
        reviewer_comments: f.reviewer_comments || null,
        status: f.status,
      });
    } else {
      await axios.post('/performance/reviews', f);
    }
    showReviewModal.value = false;
    activeTab.value = 'reviews';
    await loadData();
  } catch (err) {
    formError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {}).flat().join(' ') || 'Failed to save review';
  } finally {
    saving.value = false;
  }
};

const openGoalModal = (goal = null) => {
  editingGoal.value = goal;
  formError.value = null;
  if (goal) {
    goalForm.value = {
      employee_id: goal.employee_id || '',
      title: goal.title || '',
      description: goal.description || '',
      target_date: goal.target_date ? String(goal.target_date).substring(0, 10) : today(),
      status: goal.status || 'not_started',
      progress_percentage: goalProgress(goal),
    };
  } else {
    goalForm.value = emptyGoalForm();
  }
  showGoalModal.value = true;
};

const openViewGoal = (goal) => {
  viewingGoal.value = goal;
  showViewGoal.value = true;
};

const saveGoal = async () => {
  formError.value = null;
  const f = goalForm.value;
  if (!editingGoal.value && !f.employee_id) { formError.value = 'Employee is required'; return; }
  if (!f.title.trim()) { formError.value = 'Title is required'; return; }
  if (!f.target_date) { formError.value = 'Target date is required'; return; }
  saving.value = true;
  try {
    if (editingGoal.value) {
      await axios.put(`/performance/goals/${editingGoal.value.id}`, {
        title: f.title,
        description: f.description || null,
        target_date: f.target_date,
        status: f.status,
        progress_percentage: f.progress_percentage ?? 0,
      });
    } else {
      await axios.post('/performance/goals', f);
    }
    showGoalModal.value = false;
    activeTab.value = 'goals';
    await loadData();
  } catch (err) {
    formError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {}).flat().join(' ') || 'Failed to save goal';
  } finally {
    saving.value = false;
  }
};

const openCycleModal = () => {
  formError.value = null;
  cycleForm.value = emptyCycleForm();
  showCycleModal.value = true;
};

const openViewCycle = (cycle) => {
  viewingCycle.value = cycle;
  showViewCycle.value = true;
};

const saveCycle = async () => {
  formError.value = null;
  const f = cycleForm.value;
  if (!f.name.trim()) { formError.value = 'Name is required'; return; }
  if (!f.start_date || !f.end_date) { formError.value = 'Start and end dates are required'; return; }
  if (f.end_date <= f.start_date) { formError.value = 'End date must be after start date'; return; }
  saving.value = true;
  try {
    await axios.post('/performance/cycles', f);
    showCycleModal.value = false;
    activeTab.value = 'cycles';
    await loadData();
  } catch (err) {
    formError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {}).flat().join(' ') || 'Failed to create cycle';
  } finally {
    saving.value = false;
  }
};

onMounted(() => { loadData(); });
</script>
