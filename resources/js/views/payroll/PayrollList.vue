<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Payroll Management</h1>
      <button v-if="isAdminOrManager" @click="showGenerateModal = true" class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Generate Payroll
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Payrolls</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
          </div>
          <div class="bg-gray-100 rounded-lg p-3"><svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg></div>
          </div>
        </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Paid</p>
            <h3 class="text-2xl font-bold text-green-600">{{ stats.paid }}</h3>
          </div>
          <div class="bg-green-50 rounded-lg p-3"><svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Processing</p>
            <h3 class="text-2xl font-bold text-blue-600">{{ stats.processed }}</h3>
          </div>
          <div class="bg-blue-50 rounded-lg p-3"><svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg></div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Net Pay</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.totalNet) }}</h3>
          </div>
          <div class="bg-gray-100 rounded-lg p-3"><svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24"><path d="M4 10h12v2H4zm0-4h12v2H4zm0 8h8v2H4zm10 0l4 4 4-4h-3V10h-2v4h-3z"/></svg></div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Search Employee</label>
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
              stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input v-model="searchQuery" @input="handleSearch" type="text"
              placeholder="Search by employee name, code, email..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
          </div>
        </div>
        <div class="min-w-[160px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Month</label>
          <select v-model="filters.month" @change="loadPayrolls()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            <option value="">All Months</option>
            <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
          </select>
        </div>
        <div class="min-w-[120px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Year</label>
          <select v-model="filters.year" @change="loadPayrolls()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            <option value="">All Years</option>
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
        <div class="min-w-[140px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" @change="loadPayrolls()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="processed">Processed</option>
            <option value="paid">Paid</option>
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
      <button @click="loadPayrolls()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <!-- Empty -->
    <div v-else-if="payrolls.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Payroll Records</h3>
      <p class="text-gray-500">Click "Generate Payroll" to create payroll for a month.</p>
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employee</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Period</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Basic Salary</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Earnings</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Deductions</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Net Salary</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="payroll in payrolls" :key="payroll.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-9 w-9 rounded-full bg-gray-200 flex items-center justify-center"><span class="text-sm font-bold text-gray-600">{{ getInitials(payroll.employee) }}</span></div>
                  <div class="ml-3">
                    <div class="text-sm font-semibold text-gray-900">{{ getEmployeeName(payroll.employee) }}</div>
                    <div class="text-xs text-gray-500">{{ payroll.employee?.employee_code }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                <div class="font-medium">{{ months.find(m => m.value == payroll.month)?.label || payroll.month }} {{ payroll.year }}</div>
                <div class="text-xs text-gray-500">{{ payroll.working_days }} working · {{ payroll.present_days }} present</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">{{ formatCurrency(payroll.basic_salary) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-green-700 text-right font-medium">+{{ formatCurrency(payroll.total_earnings) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 text-right font-medium">-{{ formatCurrency(payroll.total_deductions) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <span class="text-sm font-bold text-gray-900">{{ formatCurrency(payroll.net_salary) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" :class="payrollStatusBadge(payroll.status)">{{ capitalise(payroll.status) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <div class="flex items-center space-x-2">
                  <button @click="viewPayrollDetails(payroll)" class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">Details</button>
                  <button v-if="isAdminOrManager && payroll.status === 'draft'" @click="processPayroll(payroll)" class="px-3 py-1 text-xs font-medium text-white bg-gray-900 hover:bg-gray-800 rounded-md transition-colors">Process</button>
                  <button v-if="isAdminOrManager && payroll.status === 'processed'" @click="markPaid(payroll)" class="px-3 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-colors">Mark Paid</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination -->
      <div v-if="pagination" class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="text-sm text-gray-600">Showing <span class="font-semibold text-gray-900">{{ pagination.total }}</span> records</div>
        <div class="flex items-center space-x-2">
          <button @click="loadPayrolls(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :class="pagination.current_page === 1 ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'">Previous</button>
          <span class="text-sm text-gray-600">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
          <button @click="loadPayrolls(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :class="pagination.current_page === pagination.last_page ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'">Next</button>
        </div>
      </div>
    </div>

    <!-- Payroll Details Modal -->
    <div v-if="showDetailsModal && selectedPayroll" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="showDetailsModal = false">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4 overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
          <h3 class="text-lg font-bold text-gray-900">Payroll Details</h3>
          <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        
        <div class="px-6 py-5 space-y-6">
          <!-- Employee Info -->
          <div class="flex items-center space-x-4 pb-4 border-b border-gray-200">
            <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
              <span class="text-lg font-bold text-gray-600">{{ getInitials(selectedPayroll.employee) }}</span>
            </div>
            <div>
              <h4 class="text-base font-semibold text-gray-900">{{ getEmployeeName(selectedPayroll.employee) }}</h4>
              <p class="text-sm text-gray-500">{{ selectedPayroll.employee?.employee_code }} • {{ months.find(m => m.value == selectedPayroll.month)?.label }} {{ selectedPayroll.year }}</p>
            </div>
          </div>

          <!-- Earnings Breakdown -->
          <div>
            <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
              <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
              Earnings
            </h5>
            <div class="space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600">Basic Salary</span>
                <span class="font-medium text-gray-900">{{ formatCurrency(selectedPayroll.basic_salary) }}</span>
              </div>
              <div v-if="payrollDetails.earnings && payrollDetails.earnings.length > 0">
                <div v-for="earning in payrollDetails.earnings" :key="earning.id" class="flex justify-between text-sm">
                  <span class="text-gray-600">{{ earning.salary_component?.name }}</span>
                  <span class="font-medium text-green-600">+{{ formatCurrency(earning.amount) }}</span>
                </div>
              </div>
              <div v-if="selectedPayroll.overtime_amount > 0" class="flex justify-between text-sm">
                <span class="text-gray-600">Overtime ({{ selectedPayroll.overtime_hours }} hrs)</span>
                <span class="font-medium text-green-600">+{{ formatCurrency(selectedPayroll.overtime_amount) }}</span>
              </div>
              <div v-if="selectedPayroll.bonus_amount > 0" class="flex justify-between text-sm">
                <span class="text-gray-600">Bonus</span>
                <span class="font-medium text-green-600">+{{ formatCurrency(selectedPayroll.bonus_amount) }}</span>
              </div>
              <div class="flex justify-between text-sm pt-2 border-t border-gray-200">
                <span class="font-semibold text-gray-900">Total Earnings</span>
                <span class="font-bold text-green-600">{{ formatCurrency(selectedPayroll.total_earnings + selectedPayroll.overtime_amount + selectedPayroll.bonus_amount) }}</span>
              </div>
            </div>
          </div>

          <!-- Deductions Breakdown -->
          <div>
            <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2">
              <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M11 15h2v2h-2zm0-8h2v6h-2zm.99-5C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/></svg>
              Deductions
            </h5>
            <div class="space-y-2">
              <div v-if="selectedPayroll.absent_days > 0" class="flex justify-between text-sm">
                <span class="text-gray-600">Absent Days ({{ selectedPayroll.absent_days }})</span>
                <span class="font-medium text-red-600">-{{ formatCurrency((selectedPayroll.basic_salary / selectedPayroll.working_days) * selectedPayroll.absent_days) }}</span>
              </div>
              <div v-if="payrollDetails.deductions && payrollDetails.deductions.length > 0">
                <div v-for="deduction in payrollDetails.deductions" :key="deduction.id" class="flex justify-between text-sm">
                  <span class="text-gray-600">{{ deduction.salary_component?.name }}</span>
                  <span class="font-medium text-red-600">-{{ formatCurrency(deduction.amount) }}</span>
                </div>
              </div>
              <div v-if="payrollDetails.loanDeductions && payrollDetails.loanDeductions.length > 0">
                <div class="text-xs font-semibold text-gray-700 mt-2 mb-1">Loan Deductions:</div>
                <div v-for="loan in payrollDetails.loanDeductions" :key="loan.id" class="flex justify-between text-sm">
                  <span class="text-gray-600">{{ loan.loan_type }} Loan ({{ loan.loan_number }})</span>
                  <span class="font-medium text-red-600">-{{ formatCurrency(loan.installment_amount) }}</span>
                </div>
              </div>
              <div v-if="payrollDetails.advanceDeductions && payrollDetails.advanceDeductions.length > 0">
                <div class="text-xs font-semibold text-gray-700 mt-2 mb-1">Advance Salary Deductions:</div>
                <div v-for="adv in payrollDetails.advanceDeductions" :key="adv.id" class="flex justify-between text-sm">
                  <span class="text-gray-600">{{ adv.advance_type || 'Advance' }} ({{ adv.request_number }})</span>
                  <span class="font-medium text-red-600">-{{ formatCurrency(adv.deduction_amount) }}</span>
                </div>
              </div>
              <div class="flex justify-between text-sm pt-2 border-t border-gray-200">
                <span class="font-semibold text-gray-900">Total Deductions</span>
                <span class="font-bold text-red-600">{{ formatCurrency(selectedPayroll.total_deductions) }}</span>
              </div>
            </div>
          </div>

          <!-- Net Salary -->
          <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="flex justify-between items-center">
              <span class="text-base font-semibold text-gray-900">Net Salary</span>
              <span class="text-2xl font-bold text-gray-900">{{ formatCurrency(selectedPayroll.net_salary) }}</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs text-gray-600">
              <span>Working Days: {{ selectedPayroll.working_days }}</span>
              <span>Present: {{ selectedPayroll.present_days }}</span>
              <span>Absent: {{ selectedPayroll.absent_days }}</span>
              <span>Leave: {{ selectedPayroll.leave_days }}</span>
            </div>
          </div>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end bg-gray-50">
          <button @click="showDetailsModal = false" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Close</button>
        </div>
      </div>
    </div>

    <!-- Generate Payroll Modal -->
    <div v-if="showGenerateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">Generate Monthly Payroll</h3>
          <button @click="showGenerateModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Month</label>
            <select v-model="generateForm.month" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
              <option value="">Select Month</option>
              <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Year</label>
            <select v-model="generateForm.year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
              <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
            </select>
          </div>
          <div v-if="generateError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ generateError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showGenerateModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="generatePayroll" :disabled="generating" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50">{{ generating ? 'Generating...' : 'Generate' }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const payrolls = ref([]);
const loading = ref(false);
const error = ref(null);
const pagination = ref(null);
const showGenerateModal = ref(false);
const generating = ref(false);
const generateError = ref(null);
const searchQuery = ref('');
const showDetailsModal = ref(false);
const selectedPayroll = ref(null);
const payrollDetails = ref({ earnings: [], deductions: [], loanDeductions: [], advanceDeductions: [] });

const user = JSON.parse(localStorage.getItem('user') || '{}');
const isAdminOrManager = computed(() => user.role === 'admin' || user.role === 'manager');

const currentDate = new Date();
const filters = ref({ month: '', year: '', status: '' });
const generateForm = ref({ month: currentDate.getMonth() + 1, year: currentDate.getFullYear() });

const months = [
  { value: 1, label: 'January' }, { value: 2, label: 'February' }, { value: 3, label: 'March' },
  { value: 4, label: 'April' }, { value: 5, label: 'May' }, { value: 6, label: 'June' },
  { value: 7, label: 'July' }, { value: 8, label: 'August' }, { value: 9, label: 'September' },
  { value: 10, label: 'October' }, { value: 11, label: 'November' }, { value: 12, label: 'December' }
];
const years = Array.from({ length: 5 }, (_, i) => currentDate.getFullYear() - 2 + i);

const stats = computed(() => {
  const list = payrolls.value || [];
  return {
    total: pagination.value?.total || list.length,
    paid: list.filter(p => p.status === 'paid').length,
    processed: list.filter(p => p.status === 'processed').length,
    totalNet: list.reduce((sum, p) => sum + Number(p.net_salary || 0), 0)
  };
});

const loadPayrolls = async (page = 1) => {
  loading.value = true;
  error.value = null;
  try {
    const params = { page };
    if (filters.value.month) params.month = filters.value.month;
    if (filters.value.year) params.year = filters.value.year;
    if (filters.value.status) params.status = filters.value.status;
    if (searchQuery.value) params.search = searchQuery.value;
    const response = await axios.get('/payroll', { params });
    payrolls.value = response.data.data || [];
    pagination.value = { current_page: response.data.current_page, last_page: response.data.last_page, per_page: response.data.per_page, total: response.data.total };
  } catch (err) {
    error.value = 'Failed to load payroll records';
  } finally {
    loading.value = false;
  }
};

const handleSearch = () => {
  loadPayrolls(1);
};

const generatePayroll = async () => {
  generateError.value = null;
  if (!generateForm.value.month || !generateForm.value.year) { generateError.value = 'Please select month and year'; return; }
  generating.value = true;
  try {
    await axios.post('/payroll/generate', generateForm.value);
    showGenerateModal.value = false;
    loadPayrolls();
  } catch (err) {
    generateError.value = err.response?.data?.message || 'Failed to generate payroll';
  } finally { generating.value = false; }
};

const processPayroll = async (payroll) => {
  try { await axios.post(`/payroll/${payroll.id}/process`); loadPayrolls(pagination.value?.current_page || 1); } catch (err) { alert(err.response?.data?.message || 'Failed to process'); }
};

const markPaid = async (payroll) => {
  try { await axios.post(`/payroll/${payroll.id}/mark-paid`, { payment_date: new Date().toISOString().split('T')[0] }); loadPayrolls(pagination.value?.current_page || 1); } catch (err) { alert(err.response?.data?.message || 'Failed to mark as paid'); }
};

const viewPayrollDetails = async (payroll) => {
  selectedPayroll.value = payroll;
  showDetailsModal.value = true;
  
  try {
    // Fetch detailed payroll info
    const response = await axios.get(`/payroll/${payroll.id}`);
    const data = response.data;
    
    // Separate earnings and deductions from payroll details
    payrollDetails.value.earnings = data.details?.filter(d => d.salary_component?.type === 'earning') || [];
    payrollDetails.value.deductions = data.details?.filter(d => d.salary_component?.type === 'deduction') || [];
    
    // Fetch loan deductions for this employee/period
    const loansResponse = await axios.get('/loans', {
      params: { employee_id: payroll.employee_id, status: 'active' }
    });
    payrollDetails.value.loanDeductions = loansResponse.data.data || [];
    
    // Fetch advance deductions
    const advancesResponse = await axios.get('/advance-requests', {
      params: { employee_id: payroll.employee_id, status: 'paid' }
    });
    const advances = advancesResponse.data || [];
    payrollDetails.value.advanceDeductions = advances.filter(a => a.balance_amount > 0).map(a => ({
      ...a,
      deduction_amount: a.installment_amount || (a.balance_amount / (a.installments || 1))
    }));
  } catch (err) {
    console.error('Failed to fetch payroll details:', err);
  }
};

const resetFilters = () => { filters.value = { month: '', year: '', status: '' }; searchQuery.value = ''; loadPayrolls(); };

const getEmployeeName = (emp) => emp?.user?.name || `${emp?.first_name || ''} ${emp?.last_name || ''}`.trim() || 'N/A';
const getInitials = (emp) => getEmployeeName(emp).split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
const formatCurrency = (val) => 'Rs. ' + Number(val || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
const capitalise = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
const payrollStatusBadge = (s) => ({ draft: 'bg-gray-100 text-gray-700', processed: 'bg-blue-100 text-blue-800', paid: 'bg-green-100 text-green-800' }[s] || 'bg-gray-100 text-gray-600');

onMounted(() => { loadPayrolls(); });
</script>
