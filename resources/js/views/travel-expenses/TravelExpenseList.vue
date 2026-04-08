<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Travel & Expense Management</h1>
      <div class="flex space-x-3">
        <button @click="activeTab = 'policies'" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
          View Policies
        </button>
        <button @click="openModal(activeTab)" class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          New {{ getTabLabel() }}
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Travel Requests</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.travel }}</h3>
          </div>
          <div class="bg-blue-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Expense Claims</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.expenses }}</h3>
          </div>
          <div class="bg-purple-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Advance Requests</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.advances }}</h3>
          </div>
          <div class="bg-green-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Mileage Claims</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.mileage }}</h3>
          </div>
          <div class="bg-orange-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Pending Amount</p>
            <h3 class="text-xl font-bold text-red-600">{{ formatCurrency(stats.pendingAmount) }}</h3>
          </div>
          <div class="bg-red-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
      <div class="flex space-x-2 border-b border-gray-200">
        <button 
          v-for="tab in tabs" 
          :key="tab.key"
          @click="activeTab = tab.key"
          class="px-6 py-3 font-semibold transition-all relative"
          :class="activeTab === tab.key ? 'text-gray-900 border-b-2 border-gray-900' : 'text-gray-500 hover:text-gray-700'"
        >
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" @change="loadData()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="submitted">Submitted</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="paid">Paid</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Date From</label>
          <input type="date" v-model="filters.dateFrom" @change="loadData()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">Date To</label>
          <input type="date" v-model="filters.dateTo" @change="loadData()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
        </div>
        <div class="flex items-end">
          <button @click="resetFilters" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors w-full">Reset Filters</button>
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
      <button @click="loadData()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <!-- Travel Requests Tab -->
    <div v-else-if="activeTab === 'travel'" class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div v-if="travelRequests.length === 0" class="p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">No Travel Requests</h3>
        <p class="text-gray-500">Click "New Travel Request" to create one.</p>
      </div>
      <div v-else>
        <!-- Pagination Info -->
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
          <span class="text-sm text-gray-600">
            Showing {{ travelRequests.length }} of {{ pagination.travel.total }} travel requests
          </span>
          <span class="text-sm text-gray-500">50 per page</span>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 border-b border-gray-300">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Request #</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Purpose</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Route</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Travel Dates</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Estimated Cost</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="travel in travelRequests" :key="travel.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ travel.request_number }}</span>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900">{{ travel.purpose }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">{{ travel.from_location }}</div>
                  <div class="text-xs text-gray-500">→ {{ travel.to_location }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ formatDate(travel.departure_date) }}<br>
                  <span class="text-xs text-gray-500">to {{ formatDate(travel.return_date) }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(travel.estimated_cost) }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(travel.status)">
                    {{ travel.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center space-x-2">
                    <button v-if="travel.status === 'draft'" @click="submitRecord(travel.id, 'travel')" class="p-2 hover:bg-green-100 rounded-lg transition-colors" title="Submit">
                      <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </button>
                    <button @click="viewDetails(travel)" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="View">
                      <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </button>
                    <button v-if="travel.status === 'draft'" @click="editRecord(travel)" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Edit">
                      <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Controls -->
        <div v-if="pagination.travel.lastPage > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
          <button 
            @click="changePage('travel', pagination.travel.currentPage - 1)"
            :disabled="pagination.travel.currentPage === 1"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Previous
          </button>
          <span class="text-sm text-gray-600">
            Page {{ pagination.travel.currentPage }} of {{ pagination.travel.lastPage }}
          </span>
          <button 
            @click="changePage('travel', pagination.travel.currentPage + 1)"
            :disabled="pagination.travel.currentPage === pagination.travel.lastPage"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
            Next
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Expense Claims Tab -->
    <div v-else-if="activeTab === 'expenses'" class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div v-if="expenseClaims.length === 0" class="p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">No Expense Claims</h3>
        <p class="text-gray-500">Click "New Expense Claim" to create one.</p>
      </div>
      <div v-else>
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
          <span class="text-sm text-gray-600">Showing {{ expenseClaims.length }} of {{ pagination.expenses.total }} expense claims</span>
          <span class="text-sm text-gray-500">50 per page</span>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 border-b border-gray-300">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Claim #</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Category</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="claim in expenseClaims" :key="claim.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ claim.claim_number }}</span>
                </td>
                <td class="px-6 py-4">
                  <span class="text-sm text-gray-900">{{ claim.category?.name || 'N/A' }}</span>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">{{ claim.description }}</div>
                  <div class="text-xs text-gray-500" v-if="claim.merchant_name">{{ claim.merchant_name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatDate(claim.expense_date) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(claim.amount) }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(claim.status)">{{ claim.status }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center space-x-2">
                    <button v-if="claim.status === 'draft'" @click="submitRecord(claim.id, 'expense')" class="p-2 hover:bg-green-100 rounded-lg transition-colors" title="Submit">
                      <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </button>
                    <button @click="viewDetails(claim)" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="View">
                      <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="pagination.expenses.lastPage > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
          <button @click="changePage('expenses', pagination.expenses.currentPage - 1)" :disabled="pagination.expenses.currentPage === 1" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Previous
          </button>
          <span class="text-sm text-gray-600">Page {{ pagination.expenses.currentPage }} of {{ pagination.expenses.lastPage }}</span>
          <button @click="changePage('expenses', pagination.expenses.currentPage + 1)" :disabled="pagination.expenses.currentPage === pagination.expenses.lastPage" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
            Next
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Advance Requests Tab -->
    <div v-else-if="activeTab === 'advances'" class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div v-if="advanceRequests.length === 0" class="p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">No Advance Requests</h3>
        <p class="text-gray-500">Click "New Advance Request" to create one.</p>
      </div>
      <div v-else>
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
          <span class="text-sm text-gray-600">Showing {{ advanceRequests.length }} of {{ pagination.advances.total }} advance requests</span>
          <span class="text-sm text-gray-500">50 per page</span>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 border-b border-gray-300">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Request #</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Purpose</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Required Date</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="advance in advanceRequests" :key="advance.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ advance.request_number }}</span>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">{{ advance.purpose }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(advance.amount) }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatDate(advance.required_date) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(advance.status)">{{ advance.status }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center space-x-2">
                    <button @click="viewDetails(advance)" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="View">
                      <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="pagination.advances.lastPage > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
          <button @click="changePage('advances', pagination.advances.currentPage - 1)" :disabled="pagination.advances.currentPage === 1" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Previous
          </button>
          <span class="text-sm text-gray-600">Page {{ pagination.advances.currentPage }} of {{ pagination.advances.lastPage }}</span>
          <button @click="changePage('advances', pagination.advances.currentPage + 1)" :disabled="pagination.advances.currentPage === pagination.advances.lastPage" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
            Next
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mileage Claims Tab -->
    <div v-else-if="activeTab === 'mileage'" class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div v-if="mileageClaims.length === 0" class="p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">No Mileage Claims</h3>
        <p class="text-gray-500">Click "New Mileage Claim" to create one.</p>
      </div>
      <div v-else>
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
          <span class="text-sm text-gray-600">Showing {{ mileageClaims.length }} of {{ pagination.mileage.total }} mileage claims</span>
          <span class="text-sm text-gray-500">50 per page</span>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 border-b border-gray-300">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Claim #</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Route</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Distance</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Vehicle</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="claim in mileageClaims" :key="claim.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ claim.claim_number }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ formatDate(claim.travel_date) }}</td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">{{ claim.from_location }}</div>
                  <div class="text-xs text-gray-500">→ {{ claim.to_location }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ claim.distance_km }} km</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ claim.vehicle_type }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-semibold text-gray-900">{{ formatCurrency(claim.total_amount) }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full" :class="getStatusClass(claim.status)">{{ claim.status }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center space-x-2">
                    <button @click="viewDetails(claim)" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="View">
                      <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </button>
                    <button v-if="claim.status === 'draft'" @click="editMileageClaim(claim)" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Edit">
                      <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="pagination.mileage.lastPage > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
          <button @click="changePage('mileage', pagination.mileage.currentPage - 1)" :disabled="pagination.mileage.currentPage === 1" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Previous
          </button>
          <span class="text-sm text-gray-600">Page {{ pagination.mileage.currentPage }} of {{ pagination.mileage.lastPage }}</span>
          <button @click="changePage('mileage', pagination.mileage.currentPage + 1)" :disabled="pagination.mileage.currentPage === pagination.mileage.lastPage" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
            Next
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Travel Policies Tab -->
    <div v-else-if="activeTab === 'policies'" class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div v-if="policies.length === 0" class="p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-1">No Travel Policies</h3>
        <p class="text-gray-500">Click "New Policy" to create one.</p>
      </div>
      <div v-else>
        <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
          <span class="text-sm text-gray-600">{{ policies.length }} travel policies</span>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 border-b border-gray-300">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Policy Name</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Travel Type</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Per Diem</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Mileage Rate</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Max Advance</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="policy in policies" :key="policy.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                  <div class="text-sm font-semibold text-gray-900">{{ policy.policy_name }}</div>
                  <div class="text-xs text-gray-500">{{ policy.description }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm text-gray-900 capitalize">{{ policy.travel_type }}</span><br>
                  <span class="text-xs text-gray-500 capitalize">{{ policy.designation_level }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(policy.per_diem_rate) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(policy.mileage_rate_per_km) }}/km</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ policy.max_advance_percentage }}%</td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full" :class="policy.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'">
                    {{ policy.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center space-x-2">
                    <button @click="viewDetails(policy)" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="View">
                      <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                    </button>
                    <button @click="editPolicy(policy)" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Edit">
                      <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Travel Request Modal -->
    <div v-if="showTravelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-bold text-gray-900">{{ editingTravel ? 'Edit Travel Request' : 'New Travel Request' }}</h3>
          <button @click="closeTravelModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <form @submit.prevent="submitTravelRequest" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Employee Search -->
            <div class="md:col-span-2 relative">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
              <input
                v-model="employeeSearch"
                @input="filterEmployees"
                @focus="showEmployeeDropdown = true"
                type="text"
                required
                :disabled="isEmployee"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:bg-gray-100 disabled:cursor-not-allowed"
                placeholder="Search employee by name or ID..."
              />
              <div
                v-if="showEmployeeDropdown && filteredEmployees.length > 0"
                v-click-outside="() => showEmployeeDropdown = false"
                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
              >
                <button
                  v-for="emp in filteredEmployees"
                  :key="emp.id"
                  type="button"
                  @click="selectEmployee(emp)"
                  class="w-full px-4 py-2 text-left hover:bg-gray-50 flex justify-between items-center border-b border-gray-100 last:border-0"
                >
                  <span class="font-medium">{{ emp.first_name }} {{ emp.last_name }}</span>
                  <span class="text-sm text-gray-500">{{ emp.employee_id }} - {{ emp.department?.name }}</span>
                </button>
              </div>
              <p v-if="selectedEmployeeName" class="text-sm text-green-600 mt-1">
                ✓ {{ selectedEmployeeName }}
              </p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Purpose <span class="text-red-500">*</span></label>
              <input v-model="travelForm.purpose" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="e.g., Client Meeting, Conference Attendance">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">From Location <span class="text-red-500">*</span></label>
              <input v-model="travelForm.from_location" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="e.g., Karachi">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">To Location <span class="text-red-500">*</span></label>
              <input v-model="travelForm.to_location" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="e.g., Lahore">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Departure Date <span class="text-red-500">*</span></label>
              <input v-model="travelForm.departure_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Return Date <span class="text-red-500">*</span></label>
              <input v-model="travelForm.return_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Travel Mode <span class="text-red-500">*</span></label>
              <select v-model="travelForm.travel_mode" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="">Select Travel Mode</option>
                <option value="flight">Flight</option>
                <option value="train">Train</option>
                <option value="bus">Bus</option>
                <option value="car">Car</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Estimated Cost (PKR) <span class="text-red-500">*</span></label>
              <input v-model="travelForm.estimated_cost" type="number" step="0.01" min="0" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="0.00">
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
              <textarea v-model="travelForm.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="Additional details about the travel..."></textarea>
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button type="button" @click="closeTravelModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
              Cancel
            </button>
            <button type="submit" :disabled="submitting" class="px-6 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
              {{ submitting ? 'Saving...' : (editingTravel ? 'Update' : 'Create') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Expense Claim Modal -->
    <div v-if="showExpenseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-bold text-gray-900">{{ editingExpense ? 'Edit Expense Claim' : 'New Expense Claim' }}</h3>
          <button @click="closeExpenseModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <form @submit.prevent="submitExpenseClaim" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Employee Search -->
            <div class="md:col-span-2 relative">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
              <input
                v-model="employeeSearch"
                @input="filterEmployees"
                @focus="showEmployeeDropdown = true"
                type="text"
                required
                :disabled="isEmployee"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:bg-gray-100 disabled:cursor-not-allowed"
                placeholder="Search employee by name or ID..."
              />
              <div
                v-if="showEmployeeDropdown && filteredEmployees.length > 0"
                v-click-outside="() => showEmployeeDropdown = false"
                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
              >
                <button
                  v-for="emp in filteredEmployees"
                  :key="emp.id"
                  type="button"
                  @click="selectEmployee(emp)"
                  class="w-full px-4 py-2 text-left hover:bg-gray-50 flex justify-between items-center border-b border-gray-100 last:border-0"
                >
                  <span class="font-medium">{{ emp.first_name }} {{ emp.last_name }}</span>
                  <span class="text-sm text-gray-500">{{ emp.employee_id }} - {{ emp.department?.name }}</span>
                </button>
              </div>
              <p v-if="selectedEmployeeName" class="text-sm text-green-600 mt-1">
                ✓ {{ selectedEmployeeName }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Expense Date <span class="text-red-500">*</span></label>
              <input v-model="expenseForm.expense_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
              <select v-model="expenseForm.category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="">Select Category</option>
                <option value="1">Transportation</option>
                <option value="2">Accommodation</option>
                <option value="3">Meals</option>
                <option value="4">Other</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (PKR) <span class="text-red-500">*</span></label>
              <input v-model="expenseForm.amount" type="number" step="0.01" min="0" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="0.00">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Merchant Name</label>
              <input v-model="expenseForm.merchant_name" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="e.g., Hotel Name, Restaurant">
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
              <textarea v-model="expenseForm.description" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="Describe the expense..."></textarea>
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button type="button" @click="closeExpenseModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
              Cancel
            </button>
            <button type="submit" :disabled="submitting" class="px-6 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
              {{ submitting ? 'Saving...' : (editingExpense ? 'Update' : 'Create') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Advance Request Modal -->
    <div v-if="showAdvanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-bold text-gray-900">New Advance Request</h3>
          <button @click="closeAdvanceModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <form @submit.prevent="submitAdvanceRequest" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Employee Search -->
            <div class="md:col-span-2 relative">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
              <input
                v-model="employeeSearch"
                @input="filterEmployees"
                @focus="showEmployeeDropdown = true"
                type="text"
                required
                :disabled="isEmployee"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:bg-gray-100 disabled:cursor-not-allowed"
                placeholder="Search employee by name or ID..."
              />
              <div
                v-if="showEmployeeDropdown && filteredEmployees.length > 0"
                v-click-outside="() => showEmployeeDropdown = false"
                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
              >
                <button
                  v-for="emp in filteredEmployees"
                  :key="emp.id"
                  type="button"
                  @click="selectEmployee(emp)"
                  class="w-full px-4 py-2 text-left hover:bg-gray-50 flex justify-between items-center border-b border-gray-100 last:border-0"
                >
                  <span class="font-medium">{{ emp.first_name }} {{ emp.last_name }}</span>
                  <span class="text-sm text-gray-500">{{ emp.employee_id }} - {{ emp.department?.name }}</span>
                </button>
              </div>
              <p v-if="selectedEmployeeName" class="text-sm text-green-600 mt-1">
                ✓ {{ selectedEmployeeName }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (PKR) <span class="text-red-500">*</span></label>
              <input v-model="advanceForm.amount" type="number" step="0.01" min="0" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="0.00">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Required Date <span class="text-red-500">*</span></label>
              <input v-model="advanceForm.required_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Purpose <span class="text-red-500">*</span></label>
              <textarea v-model="advanceForm.purpose" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="Explain why you need this advance..."></textarea>
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button type="button" @click="closeAdvanceModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
              Cancel
            </button>
            <button type="submit" :disabled="submitting" class="px-6 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
              {{ submitting ? 'Saving...' : 'Create' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Mileage Claim Modal -->
    <div v-if="showMileageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-bold text-gray-900">{{ editingMileage ? 'Edit Mileage Claim' : 'New Mileage Claim' }}</h3>
          <button @click="closeMileageModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <form @submit.prevent="submitMileageClaim" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Employee Search -->
            <div class="md:col-span-2 relative">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Employee <span class="text-red-500">*</span></label>
              <input
                v-model="employeeSearch"
                @input="filterEmployees"
                @focus="showEmployeeDropdown = true"
                type="text"
                required
                :disabled="isEmployee"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:bg-gray-100 disabled:cursor-not-allowed"
                placeholder="Search employee by name or ID..."
              />
              <div
                v-if="showEmployeeDropdown && filteredEmployees.length > 0"
                v-click-outside="() => showEmployeeDropdown = false"
                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
              >
                <button
                  v-for="emp in filteredEmployees"
                  :key="emp.id"
                  type="button"
                  @click="selectEmployee(emp)"
                  class="w-full px-4 py-2 text-left hover:bg-gray-50 flex justify-between items-center border-b border-gray-100 last:border-0"
                >
                  <span class="font-medium">{{ emp.first_name }} {{ emp.last_name }}</span>
                  <span class="text-sm text-gray-500">{{ emp.employee_id }} - {{ emp.department?.name }}</span>
                </button>
              </div>
              <p v-if="selectedEmployeeName" class="text-sm text-green-600 mt-1">
                ✓ {{ selectedEmployeeName }}
              </p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Travel Date <span class="text-red-500">*</span></label>
              <input v-model="mileageForm.travel_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Vehicle Type <span class="text-red-500">*</span></label>
              <select v-model="mileageForm.vehicle_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="">Select Vehicle</option>
                <option value="car">Car</option>
                <option value="motorcycle">Motorcycle</option>
                <option value="bicycle">Bicycle</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">From Location <span class="text-red-500">*</span></label>
              <input v-model="mileageForm.from_location" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="e.g., Office">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">To Location <span class="text-red-500">*</span></label>
              <input v-model="mileageForm.to_location" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="e.g., Client Site">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Distance (KM) <span class="text-red-500">*</span></label>
              <input v-model="mileageForm.distance_km" type="number" step="0.01" min="0" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="0.00">
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Purpose <span class="text-red-500">*</span></label>
              <textarea v-model="mileageForm.purpose" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="Describe the purpose of travel..."></textarea>
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button type="button" @click="closeMileageModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
              Cancel
            </button>
            <button type="submit" :disabled="submitting" class="px-6 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
              {{ submitting ? 'Saving...' : (editingMileage ? 'Update' : 'Create') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Travel Policy Modal -->
    <div v-if="showPolicyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-bold text-gray-900">{{ editingPolicy ? 'Edit Travel Policy' : 'New Travel Policy' }}</h3>
          <button @click="closePolicyModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <form @submit.prevent="submitPolicy" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Policy Name <span class="text-red-500">*</span></label>
              <input v-model="policyForm.policy_name" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="e.g., Standard Travel Policy">
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
              <textarea v-model="policyForm.description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="Describe the policy..."></textarea>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Travel Type</label>
              <select v-model="policyForm.travel_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="domestic">Domestic</option>
                <option value="international">International</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Designation Level</label>
              <select v-model="policyForm.designation_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option value="all">All Levels</option>
                <option value="executive">Executive</option>
                <option value="senior">Senior</option>
                <option value="mid">Mid-Level</option>
                <option value="junior">Junior</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Max Flight Cost (PKR)</label>
              <input v-model="policyForm.max_flight_cost" type="number" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="0.00">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Max Hotel Per Night (PKR)</label>
              <input v-model="policyForm.max_hotel_per_night" type="number" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="0.00">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Per Diem Rate (PKR)</label>
              <input v-model="policyForm.per_diem_rate" type="number" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="0.00">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Mileage Rate (PKR/KM)</label>
              <input v-model="policyForm.mileage_rate_per_km" type="number" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="0.00">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Max Advance (%)</label>
              <input v-model="policyForm.max_advance_percentage" type="number" step="1" min="0" max="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="80">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Advance Days Before Travel</label>
              <input v-model="policyForm.advance_days_before_travel" type="number" step="1" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="7">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Settlement Days After Return</label>
              <input v-model="policyForm.settlement_days_after_return" type="number" step="1" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="15">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Finance Approval Threshold (PKR)</label>
              <input v-model="policyForm.finance_approval_threshold" type="number" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" placeholder="0.00">
            </div>

            <div class="md:col-span-2 space-y-2">
              <label class="flex items-center space-x-2">
                <input v-model="policyForm.advance_allowed" type="checkbox" class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900">
                <span class="text-sm font-semibold text-gray-700">Advance Allowed</span>
              </label>
              <label class="flex items-center space-x-2">
                <input v-model="policyForm.requires_manager_approval" type="checkbox" class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900">
                <span class="text-sm font-semibold text-gray-700">Requires Manager Approval</span>
              </label>
              <label class="flex items-center space-x-2">
                <input v-model="policyForm.requires_finance_approval" type="checkbox" class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900">
                <span class="text-sm font-semibold text-gray-700">Requires Finance Approval</span>
              </label>
              <label class="flex items-center space-x-2">
                <input v-model="policyForm.is_active" type="checkbox" class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900">
                <span class="text-sm font-semibold text-gray-700">Active Policy</span>
              </label>
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button type="button" @click="closePolicyModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
              Cancel
            </button>
            <button type="submit" :disabled="submitting" class="px-6 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
              {{ submitting ? 'Saving...' : (editingPolicy ? 'Update' : 'Create') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Details Modal -->
    <div v-if="showDetailsModal && selectedRecord" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-6 border w-full max-w-4xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-2xl font-bold text-gray-900">
            {{ activeTab === 'travel' ? 'Travel Request Details' : 
               activeTab === 'expenses' ? 'Expense Claim Details' : 
               activeTab === 'advances' ? 'Advance Request Details' : 
               activeTab === 'mileage' ? 'Mileage Claim Details' : 
               'Travel Policy Details' }}
          </h3>
          <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <!-- Travel Request Details -->
        <div v-if="activeTab === 'travel'" class="space-y-6">
          <div class="grid grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
              <label class="text-xs font-semibold text-gray-500 uppercase">Request Number</label>
              <p class="mt-1 text-lg font-bold text-gray-900">{{ selectedRecord.request_number }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
              <p class="mt-1"><span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" :class="getStatusClass(selectedRecord.status)">{{ selectedRecord.status }}</span></p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="text-sm font-semibold text-gray-700">Employee</label>
              <p class="mt-1 text-gray-900">{{ selectedRecord.employee?.first_name }} {{ selectedRecord.employee?.last_name }} ({{ selectedRecord.employee?.employee_id }})</p>
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-700">Estimated Cost</label>
              <p class="mt-1 text-lg font-bold text-gray-900">{{ formatCurrency(selectedRecord.estimated_cost) }}</p>
            </div>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700">Purpose</label>
            <p class="mt-1 text-gray-900">{{ selectedRecord.purpose }}</p>
          </div>

          <div v-if="selectedRecord.description">
            <label class="text-sm font-semibold text-gray-700">Description</label>
            <p class="mt-1 text-gray-900">{{ selectedRecord.description }}</p>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="text-sm font-semibold text-gray-700">From Location</label>
              <p class="mt-1 text-gray-900">{{ selectedRecord.from_location }}</p>
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-700">To Location</label>
              <p class="mt-1 text-gray-900">{{ selectedRecord.to_location }}</p>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-6">
            <div>
              <label class="text-sm font-semibold text-gray-700">Departure Date</label>
              <p class="mt-1 text-gray-900">{{ formatDate(selectedRecord.departure_date) }}</p>
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-700">Return Date</label>
              <p class="mt-1 text-gray-900">{{ formatDate(selectedRecord.return_date) }}</p>
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-700">Travel Mode</label>
              <p class="mt-1 text-gray-900 capitalize">{{ selectedRecord.travel_mode }}</p>
            </div>
          </div>

          <div v-if="selectedRecord.approved_by" class="border-t pt-4">
            <label class="text-sm font-semibold text-gray-700">Approval Information</label>
            <div class="mt-2 grid grid-cols-2 gap-4">
              <div>
                <label class="text-xs text-gray-500">Approved By</label>
                <p class="text-gray-900">{{ selectedRecord.approver?.name || 'N/A' }}</p>
              </div>
              <div>
                <label class="text-xs text-gray-500">Approved At</label>
                <p class="text-gray-900">{{ formatDate(selectedRecord.approved_at) }}</p>
              </div>
            </div>
          </div>

          <div v-if="selectedRecord.rejection_reason" class="bg-red-50 border border-red-200 p-4 rounded-lg">
            <label class="text-sm font-semibold text-red-700">Rejection Reason</label>
            <p class="mt-1 text-red-900">{{ selectedRecord.rejection_reason }}</p>
          </div>
        </div>

        <!-- Expense Claim Details -->
        <div v-else-if="activeTab === 'expenses'" class="space-y-6">
          <div class="grid grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
              <label class="text-xs font-semibold text-gray-500 uppercase">Claim Number</label>
              <p class="mt-1 text-lg font-bold text-gray-900">{{ selectedRecord.claim_number }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
              <p class="mt-1"><span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" :class="getStatusClass(selectedRecord.status)">{{ selectedRecord.status }}</span></p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="text-sm font-semibold text-gray-700">Employee</label>
              <p class="mt-1 text-gray-900">{{ selectedRecord.employee?.first_name }} {{ selectedRecord.employee?.last_name }}</p>
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-700">Amount</label>
              <p class="mt-1 text-lg font-bold text-gray-900">{{ formatCurrency(selectedRecord.amount) }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="text-sm font-semibold text-gray-700">Expense Date</label>
              <p class="mt-1 text-gray-900">{{ formatDate(selectedRecord.expense_date) }}</p>
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-700">Category</label>
              <p class="mt-1 text-gray-900">{{ selectedRecord.category?.name || 'N/A' }}</p>
            </div>
          </div>

          <div v-if="selectedRecord.merchant_name">
            <label class="text-sm font-semibold text-gray-700">Merchant Name</label>
            <p class="mt-1 text-gray-900">{{ selectedRecord.merchant_name }}</p>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700">Description</label>
            <p class="mt-1 text-gray-900">{{ selectedRecord.description }}</p>
          </div>

          <div v-if="selectedRecord.approved_by" class="border-t pt-4">
            <label class="text-sm font-semibold text-gray-700">Approval Information</label>
            <div class="mt-2 grid grid-cols-2 gap-4">
              <div>
                <label class="text-xs text-gray-500">Approved By</label>
                <p class="text-gray-900">{{ selectedRecord.approver?.name || 'N/A' }}</p>
              </div>
              <div>
                <label class="text-xs text-gray-500">Approved At</label>
                <p class="text-gray-900">{{ formatDate(selectedRecord.approved_at) }}</p>
              </div>
            </div>
          </div>

          <div v-if="selectedRecord.payment_date" class="bg-green-50 border border-green-200 p-4 rounded-lg">
            <label class="text-sm font-semibold text-green-700">Payment Date</label>
            <p class="mt-1 text-green-900">{{ formatDate(selectedRecord.payment_date) }}</p>
          </div>
        </div>

        <!-- Advance Request Details -->
        <div v-else-if="activeTab === 'advances'" class="space-y-6">
          <div class="grid grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
              <label class="text-xs font-semibold text-gray-500 uppercase">Request Number</label>
              <p class="mt-1 text-lg font-bold text-gray-900">{{ selectedRecord.request_number }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
              <p class="mt-1"><span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" :class="getStatusClass(selectedRecord.status)">{{ selectedRecord.status }}</span></p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="text-sm font-semibold text-gray-700">Employee</label>
              <p class="mt-1 text-gray-900">{{ selectedRecord.employee?.first_name }} {{ selectedRecord.employee?.last_name }}</p>
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-700">Amount</label>
              <p class="mt-1 text-lg font-bold text-gray-900">{{ formatCurrency(selectedRecord.amount) }}</p>
            </div>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700">Purpose</label>
            <p class="mt-1 text-gray-900">{{ selectedRecord.purpose }}</p>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700">Required Date</label>
            <p class="mt-1 text-gray-900">{{ formatDate(selectedRecord.required_date) }}</p>
          </div>

          <div v-if="selectedRecord.payment_date" class="bg-green-50 border border-green-200 p-4 rounded-lg">
            <label class="text-sm font-semibold text-green-700">Payment Date</label>
            <p class="mt-1 text-green-900">{{ formatDate(selectedRecord.payment_date) }}</p>
          </div>

          <div v-if="selectedRecord.settlement_date || selectedRecord.settled_amount" class="border-t pt-4">
            <label class="text-sm font-semibold text-gray-700">Settlement Information</label>
            <div class="mt-2 grid grid-cols-2 gap-4">
              <div v-if="selectedRecord.settlement_date">
                <label class="text-xs text-gray-500">Settlement Date</label>
                <p class="text-gray-900">{{ formatDate(selectedRecord.settlement_date) }}</p>
              </div>
              <div v-if="selectedRecord.settled_amount">
                <label class="text-xs text-gray-500">Settled Amount</label>
                <p class="text-gray-900">{{ formatCurrency(selectedRecord.settled_amount) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Mileage Claim Details -->
        <div v-else-if="activeTab === 'mileage'" class="space-y-6">
          <div class="grid grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
              <label class="text-xs font-semibold text-gray-500 uppercase">Claim Number</label>
              <p class="mt-1 text-lg font-bold text-gray-900">{{ selectedRecord.claim_number }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
              <p class="mt-1"><span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" :class="getStatusClass(selectedRecord.status)">{{ selectedRecord.status }}</span></p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="text-sm font-semibold text-gray-700">Employee</label>
              <p class="mt-1 text-gray-900">{{ selectedRecord.employee?.first_name }} {{ selectedRecord.employee?.last_name }}</p>
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-700">Travel Date</label>
              <p class="mt-1 text-gray-900">{{ formatDate(selectedRecord.travel_date) }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="text-sm font-semibold text-gray-700">From Location</label>
              <p class="mt-1 text-gray-900">{{ selectedRecord.from_location }}</p>
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-700">To Location</label>
              <p class="mt-1 text-gray-900">{{ selectedRecord.to_location }}</p>
            </div>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700">Purpose</label>
            <p class="mt-1 text-gray-900">{{ selectedRecord.purpose }}</p>
          </div>

          <div class="grid grid-cols-3 gap-6 bg-gray-50 p-4 rounded-lg">
            <div>
              <label class="text-xs font-semibold text-gray-500 uppercase">Distance</label>
              <p class="mt-1 text-lg font-bold text-gray-900">{{ selectedRecord.distance_km }} km</p>
            </div>
            <div>
              <label class="text-xs font-semibold text-gray-500 uppercase">Rate per KM</label>
              <p class="mt-1 text-lg font-bold text-gray-900">{{ formatCurrency(selectedRecord.rate_per_km) }}</p>
            </div>
            <div>
              <label class="text-xs font-semibold text-gray-500 uppercase">Total Amount</label>
              <p class="mt-1 text-lg font-bold text-green-600">{{ formatCurrency(selectedRecord.total_amount) }}</p>
            </div>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700">Vehicle Type</label>
            <p class="mt-1 text-gray-900 capitalize">{{ selectedRecord.vehicle_type }}</p>
          </div>

          <div v-if="selectedRecord.approved_by" class="border-t pt-4">
            <label class="text-sm font-semibold text-gray-700">Approval Information</label>
            <div class="mt-2 grid grid-cols-2 gap-4">
              <div>
                <label class="text-xs text-gray-500">Approved By</label>
                <p class="text-gray-900">{{ selectedRecord.approver?.name || 'N/A' }}</p>
              </div>
              <div>
                <label class="text-xs text-gray-500">Approved At</label>
                <p class="text-gray-900">{{ formatDate(selectedRecord.approved_at) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Travel Policy Details -->
        <div v-else-if="activeTab === 'policies'" class="space-y-6">
          <div class="bg-gray-50 p-4 rounded-lg">
            <label class="text-xs font-semibold text-gray-500 uppercase">Policy Name</label>
            <p class="mt-1 text-xl font-bold text-gray-900">{{ selectedRecord.policy_name }}</p>
          </div>

          <div v-if="selectedRecord.description">
            <label class="text-sm font-semibold text-gray-700">Description</label>
            <p class="mt-1 text-gray-900">{{ selectedRecord.description }}</p>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="text-sm font-semibold text-gray-700">Travel Type</label>
              <p class="mt-1 text-gray-900 capitalize">{{ selectedRecord.travel_type }}</p>
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-700">Designation Level</label>
              <p class="mt-1 text-gray-900 capitalize">{{ selectedRecord.designation_level }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="text-sm font-semibold text-gray-700">Max Flight Cost</label>
              <p class="mt-1 text-gray-900">{{ selectedRecord.max_flight_cost ? formatCurrency(selectedRecord.max_flight_cost) : 'N/A' }}</p>
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-700">Max Hotel per Night</label>
              <p class="mt-1 text-gray-900">{{ selectedRecord.max_hotel_per_night ? formatCurrency(selectedRecord.max_hotel_per_night) : 'N/A' }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="text-sm font-semibold text-gray-700">Per Diem Rate</label>
              <p class="mt-1 text-gray-900">{{ selectedRecord.per_diem_rate ? formatCurrency(selectedRecord.per_diem_rate) : 'N/A' }}</p>
            </div>
            <div>
              <label class="text-sm font-semibold text-gray-700">Mileage Rate per KM</label>
              <p class="mt-1 text-gray-900">{{ selectedRecord.mileage_rate_per_km ? formatCurrency(selectedRecord.mileage_rate_per_km) : 'N/A' }}</p>
            </div>
          </div>

          <div class="border-t pt-4">
            <label class="text-sm font-semibold text-gray-700 mb-3 block">Advance Settings</label>
            <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="text-xs text-gray-500">Advance Allowed</label>
                <p class="text-gray-900">{{ selectedRecord.advance_allowed ? 'Yes' : 'No' }}</p>
              </div>
              <div>
                <label class="text-xs text-gray-500">Max Advance %</label>
                <p class="text-gray-900">{{ selectedRecord.max_advance_percentage }}%</p>
              </div>
              <div>
                <label class="text-xs text-gray-500">Days Before Travel</label>
                <p class="text-gray-900">{{ selectedRecord.advance_days_before_travel }} days</p>
              </div>
              <div>
                <label class="text-xs text-gray-500">Settlement Days After Return</label>
                <p class="text-gray-900">{{ selectedRecord.settlement_days_after_return }} days</p>
              </div>
            </div>
          </div>

          <div class="border-t pt-4">
            <label class="text-sm font-semibold text-gray-700 mb-3 block">Approval Requirements</label>
            <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="text-xs text-gray-500">Manager Approval</label>
                <p class="text-gray-900">{{ selectedRecord.requires_manager_approval ? 'Required' : 'Not Required' }}</p>
              </div>
              <div>
                <label class="text-xs text-gray-500">Finance Approval</label>
                <p class="text-gray-900">{{ selectedRecord.requires_finance_approval ? 'Required' : 'Not Required' }}</p>
              </div>
              <div v-if="selectedRecord.finance_approval_threshold">
                <label class="text-xs text-gray-500">Finance Approval Threshold</label>
                <p class="text-gray-900">{{ formatCurrency(selectedRecord.finance_approval_threshold) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 p-4 rounded-lg">
            <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
            <p class="mt-1"><span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" :class="selectedRecord.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'">{{ selectedRecord.is_active ? 'Active' : 'Inactive' }}</span></p>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-6 border-t mt-6">
          <button @click="showDetailsModal = false" class="px-6 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import { useNotification } from '@/composables/useNotification';

const authStore = useAuthStore();
const { success, error: showError } = useNotification();
const isEmployee = computed(() => authStore.user?.role === 'employee');
const currentEmployeeId = computed(() => authStore.user?.employee?.id || null);
const activeTab = ref('travel');
const loading = ref(false);
const error = ref(null);
const submitting = ref(false);

const travelRequests = ref([]);
const expenseClaims = ref([]);
const advanceRequests = ref([]);
const mileageClaims = ref([]);
const policies = ref([]);

// Modal states
const showTravelModal = ref(false);
const showExpenseModal = ref(false);
const showAdvanceModal = ref(false);
const showMileageModal = ref(false);
const showPolicyModal = ref(false);
const showDetailsModal = ref(false);
const editingTravel = ref(null);
const editingExpense = ref(null);
const editingMileage = ref(null);
const editingPolicy = ref(null);
const selectedRecord = ref(null);

// Employee search
const employees = ref([]);
const filteredEmployees = ref([]);
const employeeSearch = ref('');
const selectedEmployeeName = ref('');
const showEmployeeDropdown = ref(false);

// Form data
const travelForm = reactive({
  employee_id: '',
  purpose: '',
  description: '',
  from_location: '',
  to_location: '',
  departure_date: '',
  return_date: '',
  travel_mode: '',
  estimated_cost: ''
});

const expenseForm = reactive({
  employee_id: '',
  expense_date: '',
  category_id: '',
  amount: '',
  merchant_name: '',
  description: ''
});

const advanceForm = reactive({
  employee_id: '',
  amount: '',
  required_date: '',
  purpose: ''
});

const mileageForm = reactive({
  employee_id: '',
  travel_date: '',
  from_location: '',
  to_location: '',
  distance_km: '',
  purpose: '',
  vehicle_type: ''
});

const policyForm = reactive({
  policy_name: '',
  description: '',
  travel_type: 'domestic',
  designation_level: 'all',
  max_flight_cost: '',
  max_hotel_per_night: '',
  per_diem_rate: '',
  mileage_rate_per_km: '',
  advance_allowed: true,
  max_advance_percentage: 80,
  advance_days_before_travel: 7,
  settlement_days_after_return: 15,
  requires_manager_approval: true,
  requires_finance_approval: false,
  finance_approval_threshold: '',
  is_active: true
});

const stats = reactive({
  travel: 0,
  expenses: 0,
  advances: 0,
  mileage: 0,
  pendingAmount: 0
});

const filters = reactive({
  status: '',
  dateFrom: '',
  dateTo: ''
});

const tabs = [
  { key: 'travel', label: 'Travel Requests' },
  { key: 'expenses', label: 'Expense Claims' },
  { key: 'advances', label: 'Advance Requests' },
  { key: 'mileage', label: 'Mileage Claims' },
  { key: 'policies', label: 'Travel Policies' }
];

const pagination = reactive({
  travel: { currentPage: 1, lastPage: 1, total: 0 },
  expenses: { currentPage: 1, lastPage: 1, total: 0 },
  advances: { currentPage: 1, lastPage: 1, total: 0 },
  mileage: { currentPage: 1, lastPage: 1, total: 0 },
  policies: { currentPage: 1, lastPage: 1, total: 0 }
});

const loadData = async () => {
  loading.value = true;
  error.value = null;
  try {
    // Only include non-empty filters
    const params = {};
    if (filters.status) params.status = filters.status;
    if (filters.dateFrom) params.dateFrom = filters.dateFrom;
    if (filters.dateTo) params.dateTo = filters.dateTo;
    
    const [travelRes, expenseRes, advanceRes, mileageRes] = await Promise.all([
      axios.get('/travel-expenses/travel-requests', { params }),
      axios.get('/travel-expenses/expense-claims', { params }),
      axios.get('/travel-expenses/advance-requests', { params }),
      axios.get('/travel-expenses/mileage-claims', { params })
    ]);
    
    // Handle paginated responses correctly
    travelRequests.value = travelRes.data.data || [];
    expenseClaims.value = expenseRes.data.data || [];
    advanceRequests.value = advanceRes.data.data || [];
    mileageClaims.value = mileageRes.data.data || [];
    
    // Update pagination data
    if (travelRes.data.meta || travelRes.data.total !== undefined) {
      pagination.travel.currentPage = travelRes.data.current_page || 1;
      pagination.travel.lastPage = travelRes.data.last_page || 1;
      pagination.travel.total = travelRes.data.total || 0;
    }
    if (expenseRes.data.meta || expenseRes.data.total !== undefined) {
      pagination.expenses.currentPage = expenseRes.data.current_page || 1;
      pagination.expenses.lastPage = expenseRes.data.last_page || 1;
      pagination.expenses.total = expenseRes.data.total || 0;
    }
    if (advanceRes.data.meta || advanceRes.data.total !== undefined) {
      pagination.advances.currentPage = advanceRes.data.current_page || 1;
      pagination.advances.lastPage = advanceRes.data.last_page || 1;
      pagination.advances.total = advanceRes.data.total || 0;
    }
    if (mileageRes.data.meta || mileageRes.data.total !== undefined) {
      pagination.mileage.currentPage = mileageRes.data.current_page || 1;
      pagination.mileage.lastPage = mileageRes.data.last_page || 1;
      pagination.mileage.total = mileageRes.data.total || 0;
    }
    
    // Update stats with totals from pagination
    stats.travel = pagination.travel.total || travelRequests.value.length;
    stats.expenses = pagination.expenses.total || expenseClaims.value.length;
    stats.advances = pagination.advances.total || advanceRequests.value.length;
    stats.mileage = pagination.mileage.total || mileageClaims.value.length;
    
    // Calculate pending amount from current page data
    stats.pendingAmount = [...travelRequests.value, ...expenseClaims.value, ...advanceRequests.value]
      .filter(item => item.status === 'approved' || item.status === 'submitted')
      .reduce((sum, item) => sum + parseFloat(item.estimated_cost || item.amount || 0), 0);
  } catch (err) {
    error.value = 'Failed to load data. Please try again.';
    console.error('Error loading data:', err);
  } finally {
    loading.value = false;
  }
};

const resetFilters = () => {
  filters.status = '';
  filters.dateFrom = '';
  filters.dateTo = '';
  loadData();
};

const changePage = async (type, page) => {
  if (page < 1) return;
  
  loading.value = true;
  try {
    const params = { page };
    if (filters.status) params.status = filters.status;
    if (filters.dateFrom) params.dateFrom = filters.dateFrom;
    if (filters.dateTo) params.dateTo = filters.dateTo;
    
    let endpoint = '';
    if (type === 'travel') endpoint = '/travel-expenses/travel-requests';
    else if (type === 'expenses') endpoint = '/travel-expenses/expense-claims';
    else if (type === 'advances') endpoint = '/travel-expenses/advance-requests';
    else if (type === 'mileage') endpoint = '/travel-expenses/mileage-claims';
    
    const response = await axios.get(endpoint, { params });
    
    if (type === 'travel') {
      travelRequests.value = response.data.data || [];
      pagination.travel.currentPage = response.data.current_page || page;
      pagination.travel.lastPage = response.data.last_page || 1;
      pagination.travel.total = response.data.total || 0;
    } else if (type === 'expenses') {
      expenseClaims.value = response.data.data || [];
      pagination.expenses.currentPage = response.data.current_page || page;
      pagination.expenses.lastPage = response.data.last_page || 1;
      pagination.expenses.total = response.data.total || 0;
    } else if (type === 'advances') {
      advanceRequests.value = response.data.data || [];
      pagination.advances.currentPage = response.data.current_page || page;
      pagination.advances.lastPage = response.data.last_page || 1;
      pagination.advances.total = response.data.total || 0;
    } else if (type === 'mileage') {
      mileageClaims.value = response.data.data || [];
      pagination.mileage.currentPage = response.data.current_page || page;
      pagination.mileage.lastPage = response.data.last_page || 1;
      pagination.mileage.total = response.data.total || 0;
    }
  } catch (err) {
    console.error('Error changing page:', err);
  } finally {
    loading.value = false;
  }
};

const openModal = (type) => {
  if (type === 'travel') {
    resetTravelForm();
    showTravelModal.value = true;
  } else if (type === 'expenses') {
    resetExpenseForm();
    showExpenseModal.value = true;
  } else if (type === 'advances') {
    resetAdvanceForm();
    showAdvanceModal.value = true;
  } else if (type === 'mileage') {
    resetMileageForm();
    showMileageModal.value = true;
  } else if (type === 'policies') {
    resetPolicyForm();
    showPolicyModal.value = true;
  }
};

const closeTravelModal = () => {
  showTravelModal.value = false;
  editingTravel.value = null;
  resetTravelForm();
};

const closeExpenseModal = () => {
  showExpenseModal.value = false;
  editingExpense.value = null;
  resetExpenseForm();
};

const closeAdvanceModal = () => {
  showAdvanceModal.value = false;
  resetAdvanceForm();
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

const selectEmployee = (employee) => {
  if (showTravelModal.value) {
    travelForm.employee_id = employee.id;
  } else if (showExpenseModal.value) {
    expenseForm.employee_id = employee.id;
  } else if (showAdvanceModal.value) {
    advanceForm.employee_id = employee.id;
  } else if (showMileageModal.value) {
    mileageForm.employee_id = employee.id;
  }
  employeeSearch.value = `${employee.first_name} ${employee.last_name}`;
  selectedEmployeeName.value = `${employee.first_name} ${employee.last_name} (${employee.employee_id})`;
  showEmployeeDropdown.value = false;
};

const resetTravelForm = () => {
  travelForm.employee_id = '';
  travelForm.purpose = '';
  travelForm.description = '';
  travelForm.from_location = '';
  travelForm.to_location = '';
  travelForm.departure_date = '';
  travelForm.return_date = '';
  travelForm.travel_mode = '';
  travelForm.estimated_cost = '';
  employeeSearch.value = '';
  selectedEmployeeName.value = '';
};

const resetExpenseForm = () => {
  expenseForm.employee_id = '';
  expenseForm.expense_date = '';
  expenseForm.category_id = '';
  expenseForm.amount = '';
  expenseForm.merchant_name = '';
  expenseForm.description = '';
  employeeSearch.value = '';
  selectedEmployeeName.value = '';
};

const resetAdvanceForm = () => {
  advanceForm.employee_id = '';
  advanceForm.amount = '';
  advanceForm.required_date = '';
  advanceForm.purpose = '';
  employeeSearch.value = '';
  selectedEmployeeName.value = '';
};

const resetMileageForm = () => {
  mileageForm.employee_id = '';
  mileageForm.travel_date = '';
  mileageForm.from_location = '';
  mileageForm.to_location = '';
  mileageForm.distance_km = '';
  mileageForm.purpose = '';
  mileageForm.vehicle_type = '';
  employeeSearch.value = '';
  selectedEmployeeName.value = '';
};

const closeMileageModal = () => {
  showMileageModal.value = false;
  editingMileage.value = null;
  resetMileageForm();
};

const resetPolicyForm = () => {
  policyForm.policy_name = '';
  policyForm.description = '';
  policyForm.travel_type = 'domestic';
  policyForm.designation_level = 'all';
  policyForm.max_flight_cost = '';
  policyForm.max_hotel_per_night = '';
  policyForm.per_diem_rate = '';
  policyForm.mileage_rate_per_km = '';
  policyForm.advance_allowed = true;
  policyForm.max_advance_percentage = 80;
  policyForm.advance_days_before_travel = 7;
  policyForm.settlement_days_after_return = 15;
  policyForm.requires_manager_approval = true;
  policyForm.requires_finance_approval = false;
  policyForm.finance_approval_threshold = '';
  policyForm.is_active = true;
};

const closePolicyModal = () => {
  showPolicyModal.value = false;
  editingPolicy.value = null;
  resetPolicyForm();
};

const submitTravelRequest = async () => {
  submitting.value = true;
  try {
    const payload = { ...travelForm };

    if (editingTravel.value) {
      await axios.put(`/travel-expenses/travel-requests/${editingTravel.value.id}`, payload);
      success('Travel request updated successfully!');
    } else {
      await axios.post('/travel-expenses/travel-requests', payload);
      success('Travel request created successfully!');
    }
    
    closeTravelModal();
    loadData();
  } catch (err) {
    showError('Failed to save travel request: ' + (err.response?.data?.message || 'Unknown error'));
    console.error('Submit error:', err);
  } finally {
    submitting.value = false;
  }
};

const submitExpenseClaim = async () => {
  submitting.value = true;
  try {
    const payload = { ...expenseForm };

    if (editingExpense.value) {
      await axios.put(`/travel-expenses/expense-claims/${editingExpense.value.id}`, payload);
      success('Expense claim updated successfully!');
    } else {
      await axios.post('/travel-expenses/expense-claims', payload);
      success('Expense claim created successfully!');
    }
    
    closeExpenseModal();
    loadData();
  } catch (err) {
    showError('Failed to save expense claim: ' + (err.response?.data?.message || 'Unknown error'));
    console.error('Submit error:', err);
  } finally {
    submitting.value = false;
  }
};

const submitAdvanceRequest = async () => {
  submitting.value = true;
  try {
    const payload = { ...advanceForm };

    await axios.post('/travel-expenses/advance-requests', payload);
    success('Advance request created successfully!');
    
    closeAdvanceModal();
    loadData();
  } catch (err) {
    showError('Failed to save advance request: ' + (err.response?.data?.message || 'Unknown error'));
    console.error('Submit error:', err);
  } finally {
    submitting.value = false;
  }
};

const submitMileageClaim = async () => {
  submitting.value = true;
  try {
    const payload = { ...mileageForm };

    if (editingMileage.value) {
      await axios.put(`/travel-expenses/mileage-claims/${editingMileage.value.id}`, payload);
      success('Mileage claim updated successfully!');
    } else {
      await axios.post('/travel-expenses/mileage-claims', payload);
      success('Mileage claim created successfully!');
    }
    
    closeMileageModal();
    loadData();
  } catch (err) {
    showError('Failed to save mileage claim: ' + (err.response?.data?.message || 'Unknown error'));
    console.error('Submit error:', err);
  } finally {
    submitting.value = false;
  }
};

const submitPolicy = async () => {
  submitting.value = true;
  try {
    const payload = { ...policyForm };

    if (editingPolicy.value) {
      await axios.put(`/travel-expenses/travel-policies/${editingPolicy.value.id}`, payload);
      success('Travel policy updated successfully!');
    } else {
      await axios.post('/travel-expenses/travel-policies', payload);
      success('Travel policy created successfully!');
    }
    
    closePolicyModal();
    loadPolicies();
  } catch (err) {
    showError('Failed to save travel policy: ' + (err.response?.data?.message || 'Unknown error'));
    console.error('Submit error:', err);
  } finally {
    submitting.value = false;
  }
};

const loadPolicies = async () => {
  try {
    const response = await axios.get('/travel-expenses/travel-policies');
    policies.value = response.data.data || response.data;
    pagination.policies.total = response.data.total || policies.value.length;
  } catch (err) {
    console.error('Failed to load policies:', err);
  }
};

const submitRecord = async (id, type) => {
  if (!confirm(`Submit this ${type}?`)) return;
  try {
    let endpoint = '';
    if (type === 'travel') endpoint = `/travel-expenses/travel-requests/${id}/submit`;
    else if (type === 'expense') endpoint = `/travel-expenses/expense-claims/${id}/submit`;
    
    await axios.post(endpoint);
    success('Submitted successfully!');
    loadData();
  } catch (err) {
    showError('Failed to submit: ' + (err.response?.data?.message || 'Unknown error'));
    console.error('Submit error:', err);
  }
};

const viewDetails = (record) => {
  selectedRecord.value = record;
  showDetailsModal.value = true;
};

const editRecord = (record) => {
  if (activeTab.value === 'travel') {
    editingTravel.value = record;
    travelForm.employee_id = record.employee_id;
    travelForm.purpose = record.purpose;
    travelForm.description = record.description || '';
    travelForm.from_location = record.from_location;
    travelForm.to_location = record.to_location;
    travelForm.departure_date = record.departure_date;
    travelForm.return_date = record.return_date;
    travelForm.travel_mode = record.travel_mode;
    travelForm.estimated_cost = record.estimated_cost;
    
    // Pre-fill employee search
    if (record.employee) {
      employeeSearch.value = `${record.employee.first_name} ${record.employee.last_name}`;
      selectedEmployeeName.value = `${record.employee.first_name} ${record.employee.last_name} (${record.employee.employee_id})`;
    }
    
    showTravelModal.value = true;
  } else if (activeTab.value === 'expenses') {
    editingExpense.value = record;
    expenseForm.employee_id = record.employee_id;
    expenseForm.expense_date = record.expense_date;
    expenseForm.category_id = record.category_id;
    expenseForm.amount = record.amount;
    expenseForm.merchant_name = record.merchant_name || '';
    expenseForm.description = record.description;
    
    // Pre-fill employee search
    if (record.employee) {
      employeeSearch.value = `${record.employee.first_name} ${record.employee.last_name}`;
      selectedEmployeeName.value = `${record.employee.first_name} ${record.employee.last_name} (${record.employee.employee_id})`;
    }
    
    showExpenseModal.value = true;
  }
};

const editMileageClaim = (claim) => {
  editingMileage.value = claim;
  mileageForm.employee_id = claim.employee_id;
  mileageForm.travel_date = claim.travel_date;
  mileageForm.from_location = claim.from_location;
  mileageForm.to_location = claim.to_location;
  mileageForm.distance_km = claim.distance_km;
  mileageForm.purpose = claim.purpose;
  mileageForm.vehicle_type = claim.vehicle_type;
  
  if (claim.employee) {
    employeeSearch.value = `${claim.employee.first_name} ${claim.employee.last_name}`;
    selectedEmployeeName.value = `${claim.employee.first_name} ${claim.employee.last_name} (${claim.employee.employee_id})`;
  }
  
  showMileageModal.value = true;
};

const editPolicy = (policy) => {
  editingPolicy.value = policy;
  policyForm.policy_name = policy.policy_name;
  policyForm.description = policy.description || '';
  policyForm.travel_type = policy.travel_type || 'domestic';
  policyForm.designation_level = policy.designation_level || 'all';
  policyForm.max_flight_cost = policy.max_flight_cost || '';
  policyForm.max_hotel_per_night = policy.max_hotel_per_night || '';
  policyForm.per_diem_rate = policy.per_diem_rate || '';
  policyForm.mileage_rate_per_km = policy.mileage_rate_per_km || '';
  policyForm.advance_allowed = policy.advance_allowed;
  policyForm.max_advance_percentage = policy.max_advance_percentage || 80;
  policyForm.advance_days_before_travel = policy.advance_days_before_travel || 7;
  policyForm.settlement_days_after_return = policy.settlement_days_after_return || 15;
  policyForm.requires_manager_approval = policy.requires_manager_approval;
  policyForm.requires_finance_approval = policy.requires_finance_approval;
  policyForm.finance_approval_threshold = policy.finance_approval_threshold || '';
  policyForm.is_active = policy.is_active;
  showPolicyModal.value = true;
};

const getTabLabel = () => {
  const labels = {
    travel: 'Travel Request',
    expenses: 'Expense Claim',
    advances: 'Advance Request',
    mileage: 'Mileage Claim',
    policies: 'Policy'
  };
  return labels[activeTab.value] || 'Record';
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-PK', { year: 'numeric', month: 'short', day: 'numeric' });
};

const formatCurrency = (amount) => {
  if (!amount) return 'Rs. 0';
  return `Rs. ${parseFloat(amount).toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
};

const getStatusClass = (status) => {
  const classes = {
    draft: 'bg-gray-100 text-gray-700',
    submitted: 'bg-yellow-100 text-yellow-700',
    approved: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700',
    paid: 'bg-blue-100 text-blue-700'
  };
  return classes[status?.toLowerCase()] || 'bg-gray-100 text-gray-700';
};

onMounted(async () => {
  loadData();
  await fetchEmployees();
  loadPolicies();
  
  // Pre-fill employee_id for employee role
  if (isEmployee.value && currentEmployeeId.value) {
    // Find the employee in the loaded list
    const employee = employees.value.find(e => e.id === currentEmployeeId.value);
    if (employee) {
      selectEmployee(employee);
    }
  }
});
</script>
