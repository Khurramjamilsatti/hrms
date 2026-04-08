<template>
    <div class="p-6 space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Shift Management</h1>
                <p class="text-gray-600 mt-1">Manage work shifts and schedules</p>
            </div>
            <button @click="showCreateModal = true"
                class="flex items-center space-x-2 px-4 py-2 bg-black hover:bg-black-700 text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add New Shift</span>
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Shifts</p>
                        <h3 class="text-3xl font-bold mt-1">{{ statistics.total_shifts || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Active Shifts</p>
                        <h3 class="text-3xl font-bold mt-1">{{ statistics.active_shifts || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-amber-100 text-sm font-medium">Inactive Shifts</p>
                        <h3 class="text-3xl font-bold mt-1">{{ statistics.inactive_shifts || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Total Assignments</p>
                        <h3 class="text-3xl font-bold mt-1">{{ statistics.total_assignments || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-3">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input v-model="filters.search" @input="fetchShifts" type="text" placeholder="Search shifts..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select v-model="filters.is_active" @change="fetchShifts"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Shifts</option>
                        <option value="true">Active Only</option>
                        <option value="false">Inactive Only</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select v-model="filters.sort_by" @change="fetchShifts"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="name">Name</option>
                        <option value="start_time">Start Time</option>
                        <option value="created_at">Created Date</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                    <select v-model="filters.sort_order" @change="fetchShifts"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="asc">Ascending</option>
                        <option value="desc">Descending</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Shifts Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Loading State -->
            <div v-if="loading" class="flex items-center justify-center py-12">
                <div class="text-center">
                    <div
                        class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-gray-200 border-t-blue-600 mb-3">
                    </div>
                    <p class="text-gray-600">Loading shifts...</p>
                </div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="bg-red-50 border-l-4 border-red-500 text-red-700 p-6 m-6 rounded">
                <p class="font-medium">{{ error }}</p>
            </div>

            <!-- Shifts List -->
            <div v-else>
                <div v-if="shifts.length === 0" class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-lg font-medium">No shifts found</p>
                    <p class="text-sm mt-2">Create your first shift to get started</p>
                </div>

                <div v-else>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Shift Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Time</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Duration</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Grace Period</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Employees</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="shift in shifts" :key="shift.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ shift.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ formatTime(shift.start_time) }} - {{
                                        formatTime(shift.end_time) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ calculateDuration(shift.start_time,
                                        shift.end_time) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">{{ shift.grace_period_minutes || 0 }} mins</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full">
                                        {{ shift.employee_shifts_count || 0 }} assigned
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button @click="toggleStatus(shift)" class="focus:outline-none">
                                        <span v-if="shift.is_active"
                                            class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">
                                            Active
                                        </span>
                                        <span v-else
                                            class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">
                                            Inactive
                                        </span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button @click="openAssignEmployeesModal(shift)"
                                        class="flex-inline items-center justify-center w-5 h-5 border-2 border-black rounded-full bg-transparent hover:bg-gray-100 transition"
                                        title="Assign Employees">
                                        <svg class="w-4 h-4" fill="none" stroke="black" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M12 5v14M5 12h14" />
                                        </svg>
                                    </button>

                                    <button @click="viewShift(shift)"
                                        class="text-blue-600 hover:text-blue-900 transition-colors"
                                        title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    <button @click="editShift(shift)"
                                        class="text-amber-600 hover:text-amber-900 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(shift)"
                                        class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="pagination.last_page > 1" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} shifts
                            </div>
                            <div class="flex space-x-2">
                                <button @click="changePage(pagination.current_page - 1)"
                                    :disabled="pagination.current_page === 1"
                                    class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Previous
                                </button>
                                <button v-for="page in visiblePages" :key="page" @click="changePage(page)" :class="[
                                    'px-3 py-1 border rounded',
                                    page === pagination.current_page
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : 'border-gray-300 hover:bg-gray-100'
                                ]">
                                    {{ page }}
                                </button>
                                <button @click="changePage(pagination.current_page + 1)"
                                    :disabled="pagination.current_page === pagination.last_page"
                                    class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showCreateModal || showEditModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal"></div>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="showCreateModal ? createShift() : updateShift()">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                {{ showCreateModal ? 'Create New Shift' : 'Edit Shift' }}
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Shift Name *</label>
                                    <input v-model="formData.name" type="text" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="e.g., Morning Shift, Night Shift">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                                        <input v-model="formData.start_time" type="time" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                                        <input v-model="formData.end_time" type="time" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Grace Period
                                        (minutes)</label>
                                    <input v-model="formData.grace_period_minutes" type="number" min="0" max="60"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="e.g., 15">
                                    <p class="text-xs text-gray-500 mt-1">Late arrival tolerance in minutes</p>
                                </div>

                                <div class="flex items-center">
                                    <input v-model="formData.is_active" type="checkbox"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label class="ml-2 block text-sm text-gray-700">
                                        Active (employees can be assigned to this shift)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="submitting"
                                class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                                {{ submitting ? 'Saving...' : (showCreateModal ? 'Create Shift' : 'Update Shift') }}
                            </button>
                            <button type="button" @click="closeModal"
                                class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div v-if="showViewModal && selectedShift" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal"></div>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-gray-900">{{ selectedShift.name }}</h3>
                            <span :class="[
                                'px-3 py-1 text-sm font-medium rounded-full',
                                selectedShift.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                            ]">
                                {{ selectedShift.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-1">Start Time</p>
                                <p class="text-2xl font-bold text-blue-600">{{ formatTime(selectedShift.start_time) }}
                                </p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-1">End Time</p>
                                <p class="text-2xl font-bold text-purple-600">{{ formatTime(selectedShift.end_time) }}
                                </p>
                            </div>
                            <div class="bg-amber-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-1">Duration</p>
                                <p class="text-2xl font-bold text-amber-600">{{
                                    calculateDuration(selectedShift.start_time, selectedShift.end_time) }}</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-1">Grace Period</p>
                                <p class="text-2xl font-bold text-green-600">{{ selectedShift.grace_period_minutes || 0
                                }} mins</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Assigned Employees</h4>
                            <p class="text-3xl font-bold text-gray-900">{{ selectedShift.employee_shifts_count || 0 }}
                            </p>
                        </div>

                        <div v-if="selectedShift.employee_shifts && selectedShift.employee_shifts.length > 0"
                            class="border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Recent Assignments (Last 10)</h4>
                            <div class="space-y-2">
                                <div v-for="assignment in selectedShift.employee_shifts" :key="assignment.id"
                                    class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ assignment.employee?.first_name }} {{ assignment.employee?.last_name }}
                                    </span>
                                    <span class="text-xs text-gray-600">{{ assignment.employee?.employee_code }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="closeModal"
                            class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                    @click="showDeleteModal = false"></div>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Shift</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete "{{ shiftToDelete?.name }}"? This action cannot
                                        be undone.
                                    </p>
                                    <p v-if="shiftToDelete?.employee_shifts_count > 0"
                                        class="text-sm text-red-600 font-medium mt-2">
                                        Warning: This shift is assigned to {{ shiftToDelete.employee_shifts_count }}
                                        employee(s).
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="deleteShift" :disabled="submitting"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            {{ submitting ? 'Deleting...' : 'Delete' }}
                        </button>
                        <button type="button" @click="showDeleteModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shift Assignments Modal -->
    <ShiftAssignments :show="showAssignmentsModal" :shift="shiftForAssignment" @close="closeAssignmentsModal"
        @assigned="closeAssignmentsModal" />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import ShiftAssignments from './ShiftAssignments.vue';

const shifts = ref([]);
const statistics = ref({});
const loading = ref(false);
const error = ref(null);
const submitting = ref(false);

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const showDeleteModal = ref(false);
const showAssignmentsModal = ref(false);

const selectedShift = ref(null);
const shiftToDelete = ref(null);
const shiftForAssignment = ref(null);

const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: 0,
    to: 0
});

const filters = ref({
    search: '',
    is_active: '',
    sort_by: 'name',
    sort_order: 'asc',
    page: 1
});

const formData = ref({
    name: '',
    start_time: '',
    end_time: '',
    grace_period_minutes: 15,
    is_active: true
});

const visiblePages = computed(() => {
    const current = pagination.value.current_page;
    const last = pagination.value.last_page;
    const delta = 2;
    const range = [];

    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
        range.push(i);
    }

    if (current - delta > 2) {
        range.unshift('...');
    }
    if (current + delta < last - 1) {
        range.push('...');
    }

    range.unshift(1);
    if (last !== 1) {
        range.push(last);
    }

    return range;
});

const fetchShifts = async () => {
    loading.value = true;
    error.value = null;
    try {
        const params = {
            ...filters.value,
            per_page: pagination.value.per_page
        };

        const response = await axios.get('/shifts', { params });
        shifts.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            total: response.data.total,
            from: response.data.from,
            to: response.data.to
        };
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to fetch shifts';
        console.error('Error fetching shifts:', err);
    } finally {
        loading.value = false;
    }
};

const fetchStatistics = async () => {
    try {
        const response = await axios.get('/shifts/statistics');
        statistics.value = response.data;
    } catch (err) {
        console.error('Error fetching statistics:', err);
    }
};

const createShift = async () => {
    submitting.value = true;
    try {
        await axios.post('/shifts', formData.value);
        closeModal();
        fetchShifts();
        fetchStatistics();
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to create shift';
    } finally {
        submitting.value = false;
    }
};

const updateShift = async () => {
    submitting.value = true;
    try {
        await axios.put(`/shifts/${selectedShift.value.id}`, formData.value);
        closeModal();
        fetchShifts();
        fetchStatistics();
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to update shift';
    } finally {
        submitting.value = false;
    }
};

const deleteShift = async () => {
    submitting.value = true;
    try {
        await axios.delete(`/shifts/${shiftToDelete.value.id}`);
        showDeleteModal.value = false;
        shiftToDelete.value = null;
        fetchShifts();
        fetchStatistics();
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to delete shift';
    } finally {
        submitting.value = false;
    }
};

const toggleStatus = async (shift) => {
    try {
        await axios.post(`/shifts/${shift.id}/toggle-status`);
        fetchShifts();
        fetchStatistics();
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to toggle status';
    }
};

const viewShift = async (shift) => {
    try {
        loading.value = true;
        const response = await axios.get(`/shifts/${shift.id}`);
        selectedShift.value = response.data;
        showViewModal.value = true;
        error.value = null;
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to load shift details';
        console.error('Error loading shift details:', err);
    } finally {
        loading.value = false;
    }
};

const editShift = (shift) => {
    selectedShift.value = shift;
    formData.value = {
        name: shift.name,
        start_time: shift.start_time,
        end_time: shift.end_time,
        grace_period_minutes: shift.grace_period_minutes || 15,
        is_active: shift.is_active
    };
    showEditModal.value = true;
};

const confirmDelete = (shift) => {
    shiftToDelete.value = shift;
    showDeleteModal.value = true;
};

const openAssignEmployeesModal = (shift) => {
    shiftForAssignment.value = shift;
    showAssignmentsModal.value = true;
};

const closeAssignmentsModal = () => {
    showAssignmentsModal.value = false;
    shiftForAssignment.value = null;
    // Refresh statistics and shifts list after assignments are made
    fetchStatistics();
    fetchShifts();
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    showViewModal.value = false;
    selectedShift.value = null;
    formData.value = {
        name: '',
        start_time: '',
        end_time: '',
        grace_period_minutes: 15,
        is_active: true
    };
    error.value = null;
};

const changePage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page) {
        filters.value.page = page;
        fetchShifts();
    }
};

const formatTime = (time) => {
    if (!time) return '';
    return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
};

const calculateDuration = (start, end) => {
    if (!start || !end) return '';

    const startDate = new Date(`2000-01-01T${start}`);
    let endDate = new Date(`2000-01-01T${end}`);

    // Handle overnight shifts
    if (endDate < startDate) {
        endDate = new Date(`2000-01-02T${end}`);
    }

    const diff = endDate - startDate;
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

    return `${hours}h ${minutes}m`;
};

onMounted(() => {
    fetchShifts();
    fetchStatistics();
});
</script>
