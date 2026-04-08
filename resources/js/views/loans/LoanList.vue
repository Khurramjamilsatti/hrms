<template>
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Loan Management</h1>
            <p class="text-gray-600 mt-1">View and manage employee loan applications</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Loans</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                    </div>
                    <svg class="w-12 h-12 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z" />
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.pending }}</p>
                    </div>
                    <svg class="w-12 h-12 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Active</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.active }}</p>
                    </div>
                    <svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z" />
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Amount</p>
                        <p class="text-2xl font-bold text-gray-900">Rs. {{ formatCurrency(stats.totalAmount) }}</p>
                    </div>
                    <svg class="w-12 h-12 text-purple-500" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mb-6 flex justify-between items-center">
            <div class="flex gap-4 flex-1 max-w-3xl">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input v-model="searchQuery" @input="handleSearch" type="text"
                        placeholder="Search by employee name, code, email, or loan number..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
                </div>
                <select v-model="filters.status" @change="fetchLoans"
                    class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="disbursed">Disbursed</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="rejected">Rejected</option>
                </select>

                <select v-model="filters.loan_type" @change="fetchLoans"
                    class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Types</option>
                    <option value="personal">Personal</option>
                    <option value="medical">Medical</option>
                    <option value="education">Education</option>
                    <option value="housing">Housing</option>
                    <option value="emergency">Emergency</option>
                </select>
            </div>

            <button @click="showApplyModal = true"
                class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">
                Apply for Loan
            </button>
        </div>

        <!-- Loans Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loan
                            #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="loan in loans" :key="loan.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ loan.loan_number || 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <template v-if="loan.employee">
                                <template v-if="loan.employee.user">
                                    {{ loan.employee.user.name }} <br> <span class="text-gray-400 text-sm">{{ loan.employee.employee_code }}</span>
                                </template>
                                <template v-else>
                                    {{ loan.employee.first_name }} {{ loan.employee.last_name }} <br> <span class="text-gray-400 text-sm">{{ loan.employee.employee_code }}</span>
                                </template>
                            </template>
                            <template v-else>
                                <span class="text-gray-400">N/A</span>
                            </template>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">{{ loan.loan_type }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rs. {{ formatCurrency(loan.amount)
                            }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rs. {{
                            formatCurrency(loan.balance_amount) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="getStatusClass(loan.status)"
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                {{ loan.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <!-- View -->
                            <button @click="viewDetails(loan.id)" class="text-blue-600 hover:text-blue-800 transition"
                                title="View">
                                <!-- Eye Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 
         9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 
         0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>

                            <!-- Edit -->
                            <button v-if="loan.status === 'pending' && isAdminOrManager" @click="openEditModal(loan)"
                                class="text-gray-600 hover:text-gray-900 transition" title="Edit">
                                <!-- Pencil Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>

                            <!-- Approve -->
                            <button v-if="loan.status === 'pending' && isAdminOrManager" @click="openApproveModal(loan)"
                                class="text-green-600 hover:text-green-800 transition" title="Approve">
                                <!-- Check Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </button>

                            <!-- Reject -->
                            <button v-if="loan.status === 'pending' && isAdminOrManager" @click="openRejectModal(loan)"
                                class="text-red-600 hover:text-red-800 transition" title="Reject">
                                <!-- X Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <!-- Disburse -->
                            <button v-if="loan.status === 'approved' && isAdminOrManager"
                                @click="openDisburseModal(loan)"
                                class="text-purple-600 hover:text-purple-800 transition" title="Disburse">
                                <!-- Currency Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="9" stroke-width="2" />
                                    <path d="M12 6v12
           M15 9c0-1.5-1.5-2.5-3-2.5S9 7.5 9 9s1.5 2 3 2 3 1 3 2.5S13.5 16 12 16s-3-1-3-2.5" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                            </button>

                            <!-- Add Payment -->
                            <button v-if="(loan.status === 'active' || loan.status === 'disbursed') && isAdminOrManager" @click="showPaymentModal(loan)"
                                class="text-gray-700 hover:text-black transition" title="Add Payment">
                                <!-- Plus Circle Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v6m3-3H9" />
                                    <circle cx="12" cy="12" r="9" stroke-width="2" />
                                </svg>
                            </button>

                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="loans.length === 0" class="text-center py-12">
                <p class="text-gray-500">No loans found</p>
            </div>
        </div>

        <!-- Pagination -->
        <Pagination v-if="pagination.total > 0 && pagination.last_page > 1" :current-page="pagination.current_page"
            :total-pages="pagination.last_page" :total="pagination.total" :from="pagination.from" :to="pagination.to"
            @page-change="handlePageChange" />

        <!-- Apply Loan Modal -->
        <div v-if="showApplyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            @click.self="showApplyModal = false">
            <div class="relative top-20 mx-auto p-8 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Apply for Loan</h3>
                <form @submit.prevent="submitLoanApplication" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Employee*</label>
                        <SearchableSelect v-model="loanForm.employee_id" :options="employeeOptions"
                            placeholder="Select employee..." search-placeholder="Search by name or code..." :disabled="isEmployee" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loan Type*</label>
                        <select v-model="loanForm.loan_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="personal">Personal</option>
                            <option value="medical">Medical</option>
                            <option value="education">Education</option>
                            <option value="housing">Housing</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount (PKR)*</label>
                            <input v-model="loanForm.amount" type="number" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Interest Rate (%)*</label>
                            <input v-model="loanForm.interest_rate" type="number" step="0.01" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Number of Installments*</label>
                        <input v-model="loanForm.installments" type="number" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date*</label>
                        <input v-model="loanForm.start_date" type="date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose*</label>
                        <textarea v-model="loanForm.purpose" required rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <button type="button" @click="showApplyModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">Submit
                            Application</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Loan Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            @click.self="closeEditModal">
            <div class="relative top-20 mx-auto p-8 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Loan Application</h3>
                <form @submit.prevent="updateLoan" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Employee*</label>
                        <SearchableSelect v-model="loanForm.employee_id" :options="employeeOptions"
                            placeholder="Select employee..." search-placeholder="Search by name or code..." :disabled="isEmployee" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loan Type*</label>
                        <select v-model="loanForm.loan_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="personal">Personal</option>
                            <option value="medical">Medical</option>
                            <option value="education">Education</option>
                            <option value="housing">Housing</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (PKR)*</label>
                        <input v-model="loanForm.amount" type="number" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Number of Installments*</label>
                        <input v-model="loanForm.installments" type="number" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date*</label>
                        <input v-model="loanForm.start_date" type="date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose*</label>
                        <textarea v-model="loanForm.purpose" required rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <button type="button" @click="closeEditModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">Update Loan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="showAddPaymentModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            @click.self="showAddPaymentModal = false">
            <div class="relative top-20 mx-auto p-8 border w-full max-w-md shadow-lg rounded-lg bg-white">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Add Loan Payment</h3>
                <form @submit.prevent="submitPayment" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date*</label>
                        <input v-model="paymentForm.payment_date" type="date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (PKR)*</label>
                        <input v-model="paymentForm.amount" type="number" step="0.01" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method*</label>
                        <select v-model="paymentForm.payment_method" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="salary_deduction">Salary Deduction</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                        <textarea v-model="paymentForm.remarks" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <button type="button" @click="showAddPaymentModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">Record
                            Payment</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Approve Confirmation Modal -->
        <div v-if="showApproveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="px-6 py-5 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Approve Loan</h3>
                    <p class="text-sm text-gray-600">Are you sure you want to approve loan <span
                            class="font-semibold">{{
                                selectedLoanForAction?.loan_number }}</span>?</p>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
                    <button @click="showApproveModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button @click="approveLoan"
                        class="px-5 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">Approve</button>
                </div>
            </div>
        </div>

        <!-- Reject Confirmation Modal -->
        <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="px-6 py-5">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">Reject Loan</h3>
                    <p class="text-sm text-gray-600 mb-4 text-center">Rejecting loan <span class="font-semibold">{{
                        selectedLoanForAction?.loan_number }}</span></p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason for rejection*</label>
                        <textarea v-model="rejectionReason" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
                            placeholder="Please provide a reason..."></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
                    <button @click="showRejectModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button @click="rejectLoan" :disabled="!rejectionReason"
                        class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">Reject</button>
                </div>
            </div>
        </div>

        <!-- Disburse Confirmation Modal -->
        <div v-if="showDisburseModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
                <div class="px-6 py-5 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Disburse Loan</h3>
                    <p class="text-sm text-gray-600">Are you sure you want to disburse loan <span
                            class="font-semibold">{{
                                selectedLoanForAction?.loan_number }}</span>?</p>
                    <p class="text-sm text-gray-500 mt-2">Amount: <span class="font-bold text-gray-900">Rs. {{
                        formatCurrency(selectedLoanForAction?.amount) }}</span></p>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
                    <button @click="showDisburseModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button @click="disburseLoan"
                        class="px-5 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700">Disburse</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';
import Pagination from '@/components/Pagination.vue';
import SearchableSelect from '@/components/SearchableSelect.vue';
import { useNotification } from '@/composables/useNotification';

const { success, error: showError } = useNotification();

const authStore = useAuthStore();
const router = useRouter();
const isAdminOrManager = computed(() => ['admin', 'manager'].includes(authStore.user?.role));
const isEmployee = computed(() => authStore.user?.role === 'employee');
const currentEmployeeId = computed(() => authStore.user?.employee?.id || null);

const loans = ref([]);
const employees = ref([]);
const pagination = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0 });
const stats = ref({ total: 0, pending: 0, active: 0, totalAmount: 0 });
const filters = ref({ status: '', loan_type: '' });
const searchQuery = ref('');
const showApplyModal = ref(false);
const showEditModal = ref(false);
const showAddPaymentModal = ref(false);
const selectedLoan = ref(null);
const editingLoan = ref(null);

// Modal states for confirmation dialogs
const showApproveModal = ref(false);
const showRejectModal = ref(false);
const showDisburseModal = ref(false);
const selectedLoanForAction = ref(null);
const rejectionReason = ref('');

const loanForm = ref({
    employee_id: '',
    loan_type: 'personal',
    amount: '',
    interest_rate: 0,
    installments: 12,
    start_date: new Date(new Date().setMonth(new Date().getMonth() + 1)).toISOString().split('T')[0],
    purpose: ''
});

const employeeOptions = computed(() => {
    return employees.value.map(emp => ({
        value: emp.id,
        label: `${emp.employee_code} - ${emp.first_name} ${emp.last_name}${emp.department ? ' (' + emp.department.name + ')' : ''}`
    }));
});

const paymentForm = ref({
    payment_date: new Date().toISOString().split('T')[0],
    amount: '',
    payment_method: 'salary_deduction',
    remarks: ''
});

const fetchLoans = async (page = 1) => {
    try {
        const params = new URLSearchParams();
        params.append('page', page);
        if (filters.value.status) params.append('status', filters.value.status);
        if (filters.value.loan_type) params.append('loan_type', filters.value.loan_type);
        if (searchQuery.value) params.append('search', searchQuery.value);

        const response = await axios.get(`/loans?${params}`);

        console.log('Loan API Response:', response.data);

        // Handle paginated response
        if (response.data.data && Array.isArray(response.data.data)) {
            loans.value = response.data.data;

            // Update pagination data
            pagination.value = {
                current_page: response.data.current_page || 1,
                last_page: response.data.last_page || 1,
                total: response.data.total || 0,
                from: response.data.from || 0,
                to: response.data.to || 0
            };
        } else {
            // Fallback for non-paginated response
            loans.value = Array.isArray(response.data) ? response.data : [];
            pagination.value = {
                current_page: 1,
                last_page: 1,
                total: loans.value.length,
                from: loans.value.length > 0 ? 1 : 0,
                to: loans.value.length
            };
        }

        console.log('Loans loaded:', loans.value.length);
        console.log('Sample loan:', loans.value[0]);
        console.log('Pagination:', pagination.value);

        // Calculate stats from all loans (fetch stats from backend later for accuracy)
        stats.value.total = pagination.value.total;
        stats.value.pending = loans.value.filter(l => l.status === 'pending').length;
        stats.value.active = loans.value.filter(l => l.status === 'active').length;
        stats.value.totalAmount = loans.value.reduce((sum, l) => sum + parseFloat(l.amount || 0), 0);
    } catch (error) {
        console.error('Failed to fetch loans:', error);
        console.error('Error details:', error.response?.data);
        showError(error.response?.data?.message || 'Failed to load loans');
    }
};

const handlePageChange = (page) => {
    fetchLoans(page);
};

const handleSearch = () => {
    fetchLoans(1);
};

const fetchEmployees = async () => {
    try {
        const response = await axios.get('/employees/dropdown');
        employees.value = response.data || [];
    } catch (error) {
        console.error('Failed to fetch employees:', error);
        showError('Failed to load employees');
    }
};

const viewDetails = (loanId) => {
    router.push(`/loans/${loanId}`);
};

const openEditModal = (loan) => {
    editingLoan.value = loan;
    loanForm.value = {
        employee_id: loan.employee_id,
        loan_type: loan.loan_type,
        amount: loan.amount,
        interest_rate: loan.interest_rate,
        installments: loan.installments,
        start_date: loan.start_date,
        purpose: loan.purpose
    };
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editingLoan.value = null;
    loanForm.value = {
        employee_id: '',
        loan_type: 'personal',
        amount: '',
        interest_rate: 0,
        installments: 12,
        start_date: new Date(new Date().setMonth(new Date().getMonth() + 1)).toISOString().split('T')[0],
        purpose: ''
    };
};

const submitLoanApplication = async () => {
    // Validate employee selection
    if (!loanForm.value.employee_id) {
        showError('Please select an employee');
        return;
    }

    try {
        await axios.post('/loans', loanForm.value);
        showApplyModal.value = false;
        loanForm.value = {
            employee_id: '',
            loan_type: 'personal',
            amount: '',
            interest_rate: 0,
            installments: 12,
            start_date: new Date(new Date().setMonth(new Date().getMonth() + 1)).toISOString().split('T')[0],
            purpose: ''
        };
        fetchLoans();
        success('Loan application submitted successfully!');
    } catch (error) {
        console.error('Failed to submit loan application:', error);
        console.error('Error details:', error.response?.data);
        let errorMsg = 'Failed to submit loan application';
        if (error.response?.data?.message) {
            errorMsg = error.response.data.message;
        } else if (error.response?.data?.errors) {
            errorMsg = Object.values(error.response.data.errors).flat().join(', ');
        }
        showError(errorMsg);
    }
};

const updateLoan = async () => {
    if (!loanForm.value.employee_id) {
        showError('Please select an employee');
        return;
    }

    try {
        await axios.put(`/loans/${editingLoan.value.id}`, loanForm.value);
        closeEditModal();
        fetchLoans();
        success('Loan updated successfully!');
    } catch (error) {
        console.error('Failed to update loan:', error);
        let errorMsg = 'Failed to update loan';
        if (error.response?.data?.message) {
            errorMsg = error.response.data.message;
        } else if (error.response?.data?.errors) {
            errorMsg = Object.values(error.response.data.errors).flat().join(', ');
        }
        showError(errorMsg);
    }
};

// Open modal functions
const openApproveModal = (loan) => {
    selectedLoanForAction.value = loan;
    showApproveModal.value = true;
};

const openRejectModal = (loan) => {
    selectedLoanForAction.value = loan;
    rejectionReason.value = '';
    showRejectModal.value = true;
};

const openDisburseModal = (loan) => {
    selectedLoanForAction.value = loan;
    showDisburseModal.value = true;
};

const approveLoan = async () => {
    try {
        await axios.post(`/loans/${selectedLoanForAction.value.id}/approve`, { remarks: 'Approved' });
        showApproveModal.value = false;
        fetchLoans();
        success('Loan approved successfully!');
    } catch (error) {
        console.error('Failed to approve loan:', error);
        showError(error.response?.data?.message || 'Failed to approve loan');
    }
};

const rejectLoan = async () => {
    try {
        await axios.post(`/loans/${selectedLoanForAction.value.id}/reject`, { rejection_reason: rejectionReason.value });
        showRejectModal.value = false;
        fetchLoans();
        success('Loan rejected successfully');
    } catch (error) {
        console.error('Failed to reject loan:', error);
        showError(error.response?.data?.message || 'Failed to reject loan');
    }
};

const disburseLoan = async () => {
    try {
        await axios.post(`/loans/${selectedLoanForAction.value.id}/disburse`, {
            disbursed_date: new Date().toISOString().split('T')[0],
            payment_method: 'bank_transfer'
        });
        showDisburseModal.value = false;
        fetchLoans();
        success('Loan disbursed successfully!');
    } catch (error) {
        console.error('Failed to disburse loan:', error);
        showError(error.response?.data?.message || 'Failed to disburse loan');
    }
};

const showPaymentModal = (loan) => {
    selectedLoan.value = loan;
    paymentForm.value.amount = loan.installment_amount || '';
    showAddPaymentModal.value = true;
};

const submitPayment = async () => {
    try {
        await axios.post(`/loans/${selectedLoan.value.id}/payments`, {
            ...paymentForm.value,
            principal_amount: parseFloat(paymentForm.value.amount) * 0.95,
            interest_amount: parseFloat(paymentForm.value.amount) * 0.05
        });
        showAddPaymentModal.value = false;
        paymentForm.value = { payment_date: new Date().toISOString().split('T')[0], amount: '', payment_method: 'salary_deduction', remarks: '' };
        fetchLoans();
        success('Payment recorded successfully!');
    } catch (error) {
        console.error('Failed to record payment:', error);
        showError(error.response?.data?.message || 'Failed to record payment');
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PK').format(value || 0);
};

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-blue-100 text-blue-800',
        disbursed: 'bg-purple-100 text-purple-800',
        active: 'bg-green-100 text-green-800',
        completed: 'bg-gray-100 text-gray-800',
        rejected: 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
    fetchLoans();
    fetchEmployees();
    // Pre-fill employee_id for employee role
    if (isEmployee.value && currentEmployeeId.value) {
        loanForm.value.employee_id = currentEmployeeId.value;
    }
});
</script>
