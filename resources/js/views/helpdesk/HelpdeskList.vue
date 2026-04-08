<template>
    <div class="p-6 space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Helpdesk Tickets</h1>
                <p class="text-gray-600 mt-1">Manage and track support tickets</p>
            </div>
            <button @click="openCreateModal"
                class="flex items-center space-x-2 px-4 py-2 bg-black hover:bg-gray-800 text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Create Ticket</span>
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs font-medium">Total Tickets</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.total_tickets || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                            <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-xs font-medium">Open</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.open_tickets || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-xs font-medium">In Progress</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.in_progress_tickets || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-xs font-medium">Resolved</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.resolved_tickets || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-100 text-xs font-medium">Closed</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.closed_tickets || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-xs font-medium">Urgent</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.high_priority || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input v-model="filters.search" @input="fetchTickets" type="text" 
                        placeholder="Search tickets..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select v-model="filters.status" @change="fetchTickets"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                        <option value="reopened">Reopened</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select v-model="filters.priority" @change="fetchTickets"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select v-model="filters.category_id" @change="fetchTickets"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Categories</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                    <select v-model="filters.per_page" @change="changePerPage"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option :value="15">15 per page</option>
                        <option :value="25">25 per page</option>
                        <option :value="50">50 per page</option>
                        <option :value="100">100 per page</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div v-if="loading" class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>

            <div v-else-if="!tickets || tickets.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No tickets found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new ticket.</p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ticket #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subject
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Priority
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="ticket in tickets" :key="ticket.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ ticket.ticket_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ ticket.subject }}</div>
                                <div class="text-xs text-gray-500">{{ ticket.employee?.first_name }} {{ ticket.employee?.last_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ ticket.category?.name || 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getPriorityClass(ticket.priority)" 
                                    class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ ticket.priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusClass(ticket.status)" 
                                    class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ ticket.status.replace('_', ' ') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(ticket.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button @click="viewTicket(ticket)" 
                                    class="text-blue-600 hover:text-blue-900 transition-colors" 
                                    title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <button @click="editTicket(ticket)" 
                                    class="text-amber-600 hover:text-amber-900 transition-colors" 
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button @click="deleteTicket(ticket)" 
                                    class="text-red-600 hover:text-red-900 transition-colors" 
                                    title="Delete">
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
                            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} tickets
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

        <!-- Create Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                    @click="showCreateModal = false"></div>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Create New Ticket</h3>

                        <form @submit.prevent="createTicket" class="space-y-4">
                            <!-- Debug info -->
                            <div class="text-xs text-gray-500 mb-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                                <strong>Debug:</strong> Categories ref: {{ categories.length }}, Computed: {{ availableCategories.length }}
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                                <select v-model="formData.category_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Category ({{ availableCategories.length }} available)</option>
                                    <option v-for="category in availableCategories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                                <select v-model="formData.priority" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                                <input type="text" v-model="formData.subject" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Brief description of the issue" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                                <textarea v-model="formData.description" rows="5" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Detailed description of the issue"></textarea>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" :disabled="submitting"
                                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-black text-base font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                                    {{ submitting ? 'Creating...' : 'Create Ticket' }}
                                </button>
                                <button type="button" @click="showCreateModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div v-if="selectedTicket" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                    @click="closeModal"></div>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ selectedTicket.ticket_number }}</h3>
                                <p class="text-gray-600 mt-1">{{ selectedTicket.subject }}</p>
                            </div>
                            <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Ticket Info -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-4 grid grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Status:</span>
                                <span :class="getStatusClass(selectedTicket.status)" 
                                    class="ml-2 px-2 py-1 text-xs font-medium rounded-full">
                                    {{ selectedTicket.status.replace('_', ' ') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600">Priority:</span>
                                <span :class="getPriorityClass(selectedTicket.priority)" 
                                    class="ml-2 px-2 py-1 text-xs font-medium rounded-full">
                                    {{ selectedTicket.priority }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600">Category:</span>
                                <span class="ml-2 font-medium">{{ selectedTicket.category?.name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Created:</span>
                                <span class="ml-2 font-medium">{{ formatDate(selectedTicket.created_at) }}</span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Description</h4>
                            <p class="text-gray-700 whitespace-pre-wrap">{{ selectedTicket.description }}</p>
                        </div>

                        <!-- Replies -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Replies ({{ selectedTicket.replies?.length || 0 }})</h4>
                            
                            <div class="space-y-3 max-h-96 overflow-y-auto mb-4">
                                <div v-for="reply in selectedTicket.replies" :key="reply.id" 
                                    :class="[
                                        'border-l-4 pl-4 py-3 rounded',
                                        reply.is_internal ? 'border-yellow-500 bg-yellow-50' : 'border-blue-500 bg-blue-50'
                                    ]">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="font-medium text-sm">{{ reply.user?.name || 'Unknown' }}</span>
                                        <span class="text-xs text-gray-500">{{ formatDateTime(reply.created_at) }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700">{{ reply.message }}</p>
                                </div>
                                <div v-if="!selectedTicket.replies || selectedTicket.replies.length === 0" 
                                    class="text-center py-8 text-gray-500">
                                    No replies yet
                                </div>
                            </div>

                            <!-- Add Reply -->
                            <div class="border-t pt-4">
                                <textarea v-model="replyMessage" rows="3" 
                                    placeholder="Type your reply here..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-2"></textarea>
                                <button @click="addReply" :disabled="submitting || !replyMessage.trim()"
                                    class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 disabled:opacity-50">
                                    {{ submitting ? 'Sending...' : 'Send Reply' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                    @click="closeEditModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Ticket</h3>
                            <button @click="closeEditModal" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="updateTicket" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                                <select v-model="editFormData.category_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Category</option>
                                    <option v-for="category in availableCategories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority *</label>
                                <select v-model="editFormData.priority" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                                <select v-model="editFormData.status" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="open">Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                                <input type="text" v-model="editFormData.subject" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Brief description of the issue" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                                <textarea v-model="editFormData.description" rows="5" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Detailed description of the issue"></textarea>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" :disabled="submitting"
                                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-black text-base font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                                    {{ submitting ? 'Updating...' : 'Update Ticket' }}
                                </button>
                                <button type="button" @click="closeEditModal"
                                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                    @click="showDeleteModal = false"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Ticket</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete ticket <strong>"{{ ticketToDelete?.subject }}"</strong>?
                                    </p>
                                    <p class="text-sm text-red-600 mt-2">This action cannot be undone.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="confirmDelete" type="button"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <button @click="showDeleteModal = false" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import { useNotification } from '@/composables/useNotification';

const authStore = useAuthStore();
const { success, error: showError } = useNotification();

const tickets = ref([]);
const categories = ref([]);
const statistics = ref({});
const loading = ref(false);
const submitting = ref(false);

// Default categories as fallback
const defaultCategories = [
    { id: 1, name: 'Payroll Issues' },
    { id: 2, name: 'Leave & Attendance' },
    { id: 3, name: 'IT Support' },
    { id: 4, name: 'HR Policies' },
    { id: 5, name: 'Benefits' },
    { id: 6, name: 'Training' },
    { id: 7, name: 'Equipment' },
    { id: 8, name: 'Other' }
];
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedTicket = ref(null);
const ticketToDelete = ref(null);
const replyMessage = ref('');

const filters = ref({
    search: '',
    status: '',
    priority: '',
    category_id: '',
    per_page: 15
});

const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0,
    from: 0,
    to: 0
});

const formData = ref({
    category_id: '',
    priority: 'medium',
    subject: '',
    description: ''
});

const editFormData = ref({
    id: null,
    category_id: '',
    priority: 'medium',
    status: 'open',
    subject: '',
    description: ''
});

const availableCategories = computed(() => {
    console.log('Computed: availableCategories called, categories.value:', categories.value);
    return categories.value || [];
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

const fetchTickets = async () => {
    loading.value = true;
    try {
        const params = {
            ...filters.value,
            page: pagination.value.current_page
        };

        const response = await axios.get('/helpdesk/tickets', { params });
        console.log('Tickets API response:', response.data);
        tickets.value = response.data.data || [];
        
        if (response.data.current_page) {
            pagination.value = {
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                per_page: response.data.per_page,
                total: response.data.total,
                from: response.data.from,
                to: response.data.to
            };
        }
    } catch (err) {
        tickets.value = [];
        showError('Failed to fetch tickets');
        console.error('Error fetching tickets:', err);
    } finally {
        loading.value = false;
    }
};

const fetchStatistics = async () => {
    try {
        const response = await axios.get('/helpdesk/statistics');
        statistics.value = response.data;
    } catch (err) {
        console.error('Error fetching statistics:', err);
    }
};

const fetchCategories = async () => {
    try {
        const response = await axios.get('/helpdesk/categories');
        console.log('Categories API response:', response.data);
        
        // API returns array directly, not wrapped in data
        if (Array.isArray(response.data) && response.data.length > 0) {
            categories.value = response.data;
            console.log('✅ Categories loaded from API:', categories.value.length, 'items');
        } else if (response.data.data && Array.isArray(response.data.data)) {
            categories.value = response.data.data;
            console.log('✅ Categories loaded from API (nested):', categories.value.length, 'items');
        } else {
            categories.value = defaultCategories;
            console.log('⚠️ Using default categories as fallback');
        }
    } catch (err) {
        console.error('❌ Error fetching categories, using defaults:', err);
        categories.value = defaultCategories;
    }
};

const openCreateModal = async () => {
    console.log('Opening create modal, categories available:', categories.value.length);
    
    // Ensure categories are loaded
    if (categories.value.length === 0) {
        console.log('Categories empty, fetching now...');
        await fetchCategories();
    }
    
    console.log('Final categories count:', categories.value.length);
    console.log('Categories data:', categories.value);
    showCreateModal.value = true;
};

const createTicket = async () => {
    submitting.value = true;
    try {
        // Get employee_id properly
        let employeeId = null;
        if (authStore.user && authStore.user.employee) {
            employeeId = authStore.user.employee.id;
        } else if (authStore.user && authStore.user.id) {
            // Try to get employee by user_id
            employeeId = authStore.user.id;
        }
        
        console.log('Creating ticket with employee_id:', employeeId);
        
        const data = {
            ...formData.value,
            employee_id: employeeId
        };
        
        await axios.post('/helpdesk/tickets', data);
        success('Ticket created successfully!');
        showCreateModal.value = false;
        formData.value = {
            category_id: '',
            priority: 'medium',
            subject: '',
            description: ''
        };
        fetchTickets();
        fetchStatistics();
    } catch (err) {
        showError(err.response?.data?.message || 'Failed to create ticket');
        console.error('Error creating ticket:', err, err.response?.data);
    } finally {
        submitting.value = false;
    }
};

const viewTicket = async (ticket) => {
    try {
        loading.value = true;
        const response = await axios.get(`/helpdesk/tickets/${ticket.id}`);
        selectedTicket.value = response.data;
    } catch (err) {
        showError('Failed to load ticket details');
        console.error('Error loading ticket:', err);
    } finally {
        loading.value = false;
    }
};

const editTicket = async (ticket) => {
    try {
        loading.value = true;
        const response = await axios.get(`/helpdesk/tickets/${ticket.id}`);
        const ticketData = response.data;
        
        editFormData.value = {
            id: ticketData.id,
            category_id: ticketData.category_id,
            priority: ticketData.priority,
            status: ticketData.status,
            subject: ticketData.subject,
            description: ticketData.description
        };
        
        showEditModal.value = true;
    } catch (err) {
        showError('Failed to load ticket details');
        console.error('Error loading ticket:', err);
    } finally {
        loading.value = false;
    }
};

const updateTicket = async () => {
    submitting.value = true;
    try {
        await axios.put(`/helpdesk/tickets/${editFormData.value.id}`, {
            category_id: editFormData.value.category_id,
            priority: editFormData.value.priority,
            status: editFormData.value.status,
            subject: editFormData.value.subject,
            description: editFormData.value.description
        });
        success('Ticket updated successfully!');
        showEditModal.value = false;
        fetchTickets();
        fetchStatistics();
    } catch (err) {
        showError(err.response?.data?.message || 'Failed to update ticket');
        console.error('Error updating ticket:', err);
    } finally {
        submitting.value = false;
    }
};

const closeEditModal = () => {
    showEditModal.value = false;
    editFormData.value = {
        id: null,
        category_id: '',
        priority: 'medium',
        status: 'open',
        subject: '',
        description: ''
    };
};

const addReply = async () => {
    if (!replyMessage.value.trim() || !selectedTicket.value) return;

    submitting.value = true;
    try {
        await axios.post(`/helpdesk/tickets/${selectedTicket.value.id}/replies`, {
            message: replyMessage.value,
            is_internal: false
        });
        success('Reply added successfully!');
        replyMessage.value = '';
        await viewTicket(selectedTicket.value);
    } catch (err) {
        showError('Failed to add reply');
        console.error('Error adding reply:', err);
    } finally {
        submitting.value = false;
    }
};

const deleteTicket = (ticket) => {
    ticketToDelete.value = ticket;
    showDeleteModal.value = true;
};

const confirmDelete = async () => {
    if (!ticketToDelete.value) return;
    
    try {
        await axios.delete(`/helpdesk/tickets/${ticketToDelete.value.id}`);
        success('Ticket deleted successfully!');
        showDeleteModal.value = false;
        ticketToDelete.value = null;
        fetchTickets();
        fetchStatistics();
    } catch (err) {
        showError(err.response?.data?.message || 'Failed to delete ticket');
        console.error('Error deleting ticket:', err);
    }
};

const closeModal = () => {
    selectedTicket.value = null;
    replyMessage.value = '';
};

const changePage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page && page !== '...') {
        pagination.value.current_page = page;
        fetchTickets();
    }
};

const changePerPage = () => {
    pagination.value.current_page = 1; // Reset to first page when changing items per page
    fetchTickets();
};

const getPriorityClass = (priority) => {
    const classes = {
        low: 'bg-gray-100 text-gray-800',
        medium: 'bg-blue-100 text-blue-800',
        high: 'bg-orange-100 text-orange-800',
        urgent: 'bg-red-100 text-red-800'
    };
    return classes[priority] || 'bg-gray-100 text-gray-800';
};

const getStatusClass = (status) => {
    const classes = {
        open: 'bg-blue-100 text-blue-800',
        in_progress: 'bg-yellow-100 text-yellow-800',
        resolved: 'bg-green-100 text-green-800',
        closed: 'bg-gray-100 text-gray-800',
        reopened: 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-PK', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatDateTime = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleString('en-PK', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

onMounted(() => {
    // Initialize categories with defaults immediately
    categories.value = defaultCategories;
    console.log('🚀 Initialized with default categories:', categories.value.length);
    
    // Then fetch from API
    fetchTickets();
    fetchStatistics();
    fetchCategories();
});
</script>
