<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center">
        <router-link to="/employees" class="mr-3 p-2 hover:bg-gray-100 rounded-lg transition-colors">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </router-link>
        <h1 class="text-2xl font-bold text-gray-900">Employee Details</h1>
      </div>
      <div v-if="employee" class="flex items-center gap-2">
        <router-link v-if="isAdminOrManager" :to="`/employees/${employee.id}/salary`"
          class="inline-flex items-center px-4 py-2 bg-gray-900 text-white font-medium rounded-lg hover:bg-gray-800 transition-colors">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Manage Salary
        </router-link>
        <router-link :to="`/employees/${employee.id}/edit`"
          class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          Edit
        </router-link>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <div class="text-gray-500">Loading employee details...</div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
      {{ error }}
    </div>

    <!-- Employee Profile -->
    <div v-else-if="employee" class="space-y-6">

      <!-- Profile Header Card -->
      <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
          <!-- Avatar -->
          <div class="w-20 h-20 rounded-full bg-gray-900 flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
            {{ initials }}
          </div>
          <!-- Info -->
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-1">
              <h2 class="text-2xl font-bold text-gray-900">{{ employee.first_name }} {{ employee.last_name }}</h2>
              <span :class="statusClass" class="inline-flex px-2.5 py-0.5 text-xs font-semibold rounded-full">
                {{ formatStatus(employee.employment_status) }}
              </span>
            </div>
            <p class="text-gray-500 text-sm mb-2">{{ employee.designation?.title || 'No Designation' }} · {{ employee.department?.name || 'No Department' }}</p>
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                {{ employee.employee_code }}
              </span>
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                {{ employee.user?.email }}
              </span>
              <span v-if="employee.phone" class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                {{ employee.phone }}
              </span>
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                {{ employee.user?.role }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Info Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Personal Information -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Personal Information
          </h3>
          <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b border-gray-100">
              <dt class="text-sm text-gray-500">Full Name</dt>
              <dd class="text-sm font-medium text-gray-900">{{ employee.first_name }} {{ employee.last_name }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <dt class="text-sm text-gray-500">Gender</dt>
              <dd class="text-sm font-medium text-gray-900 capitalize">{{ employee.gender || '—' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <dt class="text-sm text-gray-500">Date of Birth</dt>
              <dd class="text-sm font-medium text-gray-900">{{ formatDate(employee.date_of_birth) }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <dt class="text-sm text-gray-500">National ID</dt>
              <dd class="text-sm font-medium text-gray-900">{{ employee.national_id || '—' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <dt class="text-sm text-gray-500">Phone</dt>
              <dd class="text-sm font-medium text-gray-900">{{ employee.phone || '—' }}</dd>
            </div>
            <div class="flex justify-between py-2">
              <dt class="text-sm text-gray-500">Emergency Contact</dt>
              <dd class="text-sm font-medium text-gray-900">{{ employee.emergency_contact || '—' }}</dd>
            </div>
          </dl>
        </div>

        <!-- Employment Information -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Employment Information
          </h3>
          <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b border-gray-100">
              <dt class="text-sm text-gray-500">Employee Code</dt>
              <dd class="text-sm font-medium text-gray-900">{{ employee.employee_code }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <dt class="text-sm text-gray-500">Department</dt>
              <dd class="text-sm font-medium text-gray-900">{{ employee.department?.name || '—' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <dt class="text-sm text-gray-500">Designation</dt>
              <dd class="text-sm font-medium text-gray-900">{{ employee.designation?.title || '—' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <dt class="text-sm text-gray-500">Employment Type</dt>
              <dd class="text-sm font-medium text-gray-900 capitalize">{{ employee.employment_type?.replace('_', ' ') || '—' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <dt class="text-sm text-gray-500">Joining Date</dt>
              <dd class="text-sm font-medium text-gray-900">{{ formatDate(employee.joining_date) }}</dd>
            </div>
            <div class="flex justify-between py-2">
              <dt class="text-sm text-gray-500">Reporting Manager</dt>
              <dd class="text-sm font-medium text-gray-900">{{ employee.manager?.name || '—' }}</dd>
            </div>
          </dl>
        </div>

        <!-- Address -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Address
          </h3>
          <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b border-gray-100">
              <dt class="text-sm text-gray-500">Address</dt>
              <dd class="text-sm font-medium text-gray-900 text-right max-w-[60%]">{{ employee.address || '—' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <dt class="text-sm text-gray-500">City</dt>
              <dd class="text-sm font-medium text-gray-900">{{ employee.city || '—' }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
              <dt class="text-sm text-gray-500">State / Province</dt>
              <dd class="text-sm font-medium text-gray-900">{{ employee.state || '—' }}</dd>
            </div>
            <div class="flex justify-between py-2">
              <dt class="text-sm text-gray-500">Postal Code</dt>
              <dd class="text-sm font-medium text-gray-900">{{ employee.postal_code || '—' }}</dd>
            </div>
          </dl>
        </div>

        <!-- Leave Balances -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
          <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Leave Balances
          </h3>
          <div v-if="employee.leave_balances && employee.leave_balances.length > 0">
            <div class="overflow-hidden rounded-lg border border-gray-200">
              <table class="w-full text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Leave Type</th>
                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase">Total</th>
                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase">Used</th>
                    <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase">Remaining</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr v-for="bal in employee.leave_balances" :key="bal.id" class="hover:bg-gray-50">
                    <td class="px-4 py-2.5 font-medium text-gray-900">{{ bal.leave_type?.name || 'Unknown' }}</td>
                    <td class="px-4 py-2.5 text-center text-gray-600">{{ bal.total_days }}</td>
                    <td class="px-4 py-2.5 text-center text-gray-600">{{ bal.used_days }}</td>
                    <td class="px-4 py-2.5 text-center">
                      <span :class="bal.remaining_days > 0 ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'">
                        {{ bal.remaining_days }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div v-else class="text-sm text-gray-400 text-center py-4">No leave balance records</div>
        </div>
      </div>

      <!-- Documents -->
      <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Documents
        </h3>
        <div v-if="employee.documents && employee.documents.length > 0">
          <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Document</th>
                  <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                  <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Uploaded</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="doc in employee.documents" :key="doc.id" class="hover:bg-gray-50">
                  <td class="px-4 py-2.5 font-medium text-gray-900">{{ doc.title || doc.file_name }}</td>
                  <td class="px-4 py-2.5 text-gray-600 capitalize">{{ doc.document_type?.replace('_', ' ') || '—' }}</td>
                  <td class="px-4 py-2.5 text-gray-600">{{ formatDate(doc.created_at) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-else class="text-sm text-gray-400 text-center py-4">No documents uploaded</div>
      </div>

      <!-- Contracts -->
      <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
          </svg>
          Contracts
        </h3>
        <div v-if="employee.contracts && employee.contracts.length > 0">
          <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                  <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Start Date</th>
                  <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">End Date</th>
                  <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="contract in employee.contracts" :key="contract.id" class="hover:bg-gray-50">
                  <td class="px-4 py-2.5 font-medium text-gray-900 capitalize">{{ contract.contract_type?.replace('_', ' ') || '—' }}</td>
                  <td class="px-4 py-2.5 text-gray-600">{{ formatDate(contract.start_date) }}</td>
                  <td class="px-4 py-2.5 text-gray-600">{{ formatDate(contract.end_date) }}</td>
                  <td class="px-4 py-2.5 text-center">
                    <span :class="{
                      'inline-flex px-2 py-0.5 text-xs font-semibold rounded-full': true,
                      'bg-green-100 text-green-700': contract.status === 'active',
                      'bg-gray-100 text-gray-600': contract.status === 'expired',
                      'bg-red-100 text-red-700': contract.status === 'terminated',
                    }">
                      {{ contract.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-else class="text-sm text-gray-400 text-center py-4">No contract records</div>
      </div>

      <!-- Salary History -->
      <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            Salary History & Increments
          </h3>
          <span class="text-xs text-gray-500">{{ incrementHistory.length }} record(s)</span>
        </div>
        <div v-if="!loadingIncrements && incrementHistory.length > 0">
          <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Effective Date</th>
                  <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Basic Salary</th>
                  <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Gross Salary</th>
                  <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Increment</th>
                  <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">% Change</th>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Remarks</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="(inc, index) in incrementHistory" :key="index" 
                    :class="inc.effective_to === null ? 'bg-green-50 border-l-4 border-l-green-500' : 'hover:bg-gray-50'">
                  <td class="px-4 py-3">
                    <span v-if="inc.effective_to === null" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500 text-white shadow-sm">
                      <span class="w-1.5 h-1.5 bg-white rounded-full mr-1.5 animate-pulse"></span>
                      ACTIVE
                    </span>
                    <span v-else class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
                      Inactive
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <div class="text-gray-900 font-medium">{{ formatDate(inc.effective_from) }}</div>
                    <div v-if="inc.effective_to" class="text-xs text-gray-500 mt-0.5">
                      Ended: {{ formatDate(inc.effective_to) }}
                    </div>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <div class="font-semibold text-gray-900">{{ formatCurrency(inc.basic_salary) }}</div>
                    <div v-if="inc.previous_salary" class="text-xs text-gray-500 mt-0.5">
                      From: {{ formatCurrency(inc.previous_salary) }}
                    </div>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <div class="font-semibold text-blue-600">{{ formatCurrency(inc.gross_salary) }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Total with components</div>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <span v-if="inc.increment_amount > 0" class="inline-flex items-center justify-end gap-1 text-green-600 font-semibold">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                      </svg>
                      {{ formatCurrency(inc.increment_amount) }}
                    </span>
                    <span v-else-if="inc.increment_amount < 0" class="inline-flex items-center justify-end gap-1 text-red-600 font-semibold">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                      </svg>
                      {{ formatCurrency(Math.abs(inc.increment_amount)) }}
                    </span>
                    <span v-else class="text-gray-400 text-xs italic">Initial salary</span>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <span v-if="inc.increment_percentage > 0" class="inline-flex items-center px-2 py-0.5 rounded-md bg-green-100 text-green-700 font-semibold text-xs">
                      +{{ inc.increment_percentage.toFixed(1) }}%
                    </span>
                    <span v-else-if="inc.increment_percentage < 0" class="inline-flex items-center px-2 py-0.5 rounded-md bg-red-100 text-red-700 font-semibold text-xs">
                      {{ inc.increment_percentage.toFixed(1) }}%
                    </span>
                    <span v-else class="text-gray-400 text-xs">—</span>
                  </td>
                  <td class="px-4 py-3 text-gray-600 text-xs max-w-xs truncate">
                    {{ inc.remarks || '—' }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-else-if="loadingIncrements" class="text-sm text-gray-400 text-center py-4">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900 mx-auto"></div>
          <p class="mt-2">Loading increment history...</p>
        </div>
        <div v-else class="text-sm text-gray-400 text-center py-4">No increment history</div>
      </div>

      <!-- Salary Information -->
      <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Salary History
        </h3>
        <div v-if="employee.salaries && employee.salaries.length > 0">
          <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Basic Salary</th>
                  <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Effective From</th>
                  <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Effective To</th>
                  <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Components</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="sal in employee.salaries" :key="sal.id" class="hover:bg-gray-50">
                  <td class="px-4 py-2.5 font-semibold text-gray-900">{{ formatCurrency(sal.basic_salary) }}</td>
                  <td class="px-4 py-2.5 text-gray-600">{{ formatDate(sal.effective_from) }}</td>
                  <td class="px-4 py-2.5 text-gray-600">{{ formatDate(sal.effective_to) }}</td>
                  <td class="px-4 py-2.5">
                    <div v-if="sal.components && sal.components.length > 0" class="space-y-1">
                      <div v-for="comp in sal.components" :key="comp.id" class="flex justify-between text-xs">
                        <span class="text-gray-500">{{ comp.salary_component?.name || 'Component' }}</span>
                        <span :class="comp.salary_component?.type === 'deduction' ? 'text-red-600' : 'text-green-600'" class="font-medium">
                          {{ comp.salary_component?.type === 'deduction' ? '-' : '+' }}{{ formatCurrency(comp.amount) }}
                        </span>
                      </div>
                    </div>
                    <span v-else class="text-gray-400 text-xs">—</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div v-else class="text-sm text-gray-400 text-center py-4">No salary records</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const employee = ref(null);
const loading = ref(true);
const error = ref(null);
const incrementHistory = ref([]);
const loadingIncrements = ref(false);

const user = JSON.parse(localStorage.getItem('user') || '{}');
const isAdminOrManager = computed(() => user.role === 'admin' || user.role === 'manager');

const initials = computed(() => {
  if (!employee.value) return '';
  return ((employee.value.first_name?.[0] || '') + (employee.value.last_name?.[0] || '')).toUpperCase();
});

const statusClass = computed(() => {
  const status = employee.value?.employment_status;
  if (status === 'active') return 'bg-green-100 text-green-700';
  if (status === 'on_leave') return 'bg-amber-100 text-amber-700';
  if (status === 'terminated') return 'bg-red-100 text-red-700';
  if (status === 'resigned') return 'bg-gray-100 text-gray-600';
  return 'bg-gray-100 text-gray-600';
});

const formatDate = (date) => {
  if (!date) return '—';
  try {
    return new Date(date).toLocaleDateString('en-PK', { year: 'numeric', month: 'short', day: 'numeric' });
  } catch {
    return date;
  }
};

const formatStatus = (status) => {
  if (!status) return '—';
  return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const formatCurrency = (amount) => {
  if (amount == null) return '—';
  return 'Rs. ' + Number(amount).toLocaleString('en-PK');
};

const fetchIncrementHistory = async () => {
  loadingIncrements.value = true;
  try {
    const res = await axios.get(`/salary-components/employees/${route.params.id}/increment-history`);
    incrementHistory.value = res.data || [];
  } catch (err) {
    console.error('Failed to load increment history:', err);
  } finally {
    loadingIncrements.value = false;
  }
};

onMounted(async () => {
  try {
    const res = await axios.get(`/employees/${route.params.id}`);
    employee.value = res.data;
    
    // Fetch increment history
    fetchIncrementHistory();
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load employee details';
  } finally {
    loading.value = false;
  }
});
</script>
