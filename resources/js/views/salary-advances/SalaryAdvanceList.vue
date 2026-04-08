<template>
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Salary Advance Management</h1>
            <p class="text-gray-600 mt-1">View and manage employee salary advance requests</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Requests</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                    </div>
                    <svg class="w-12 h-12 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.5 14.25H9v-2h4.5v2zm0-3.75H9V7h4.5v5.5z"/>
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
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.approved }}</p>
                    </div>
                    <svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
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
                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
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
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input v-model="searchQuery" @input="handleSearch" type="text"
                        placeholder="Search by employee name, code, email..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"/>
                </div>
                <select v-model="filters.status" @change="fetchAdvances"
                    class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="paid">Paid</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <button @click="showApplyModal = true"
                class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">
                Request Advance
            </button>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"></div>
            <p class="mt-4 text-gray-600">Loading...</p>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
            <p class="font-medium">{{ error }}</p>
            <button @click="fetchAdvances()" class="mt-2 text-sm underline">Try again</button>
        </div>

        <!-- Table -->
        <div v-else-if="advances.length > 0" class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Request #</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Installments</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="advance in advances" :key="advance.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ advance.request_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ getEmployeeName(advance.employee) }}</div>
                            <div class="text-xs text-gray-500">{{ advance.employee?.employee_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 capitalize">{{ formatType(advance.advance_type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">Rs. {{ formatCurrency(advance.amount) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ advance.installments }} months</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">Rs. {{ formatCurrency(advance.balance_amount) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="getStatusClass(advance.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                {{ capitalise(advance.status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2">
                                <button v-if="advance.status === 'pending' && isAdminOrManager" 
                                    @click="openApproveModal(advance)"
                                    class="px-3 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded-md">
                                    Approve
                                </button>
                                <button v-if="advance.status === 'pending' && isAdminOrManager" 
                                    @click="openRejectModal(advance)"
                                    class="px-3 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
                                    Reject
                                </button>
                                <button v-if="advance.status === 'approved' && isAdminOrManager" 
                                    @click="openDisburseModal(advance)"
                                    class="px-3 py-1 text-xs font-medium text-white bg-gray-900 hover:bg-gray-800 rounded-md">
                                    Disburse
                                </button>
                                <button v-if="advance.status === 'pending'" 
                                    @click="editAdvance(advance)"
                                    class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                                    Edit
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="pagination.total > 0" class="border-t border-gray-200 bg-gray-50 px-4 py-3 flex items-center justify-between">
                <div class="flex-1 flex justify-between items-center">
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ pagination.from }}</span> to <span class="font-medium">{{ pagination.to }}</span> of
                        <span class="font-medium">{{ pagination.total }}</span> results
                    </p>
                    <div class="flex space-x-2">
                        <button @click="fetchAdvances(pagination.current_page - 1)" 
                            :disabled="pagination.current_page === 1"
                            class="px-4 py-2 text-sm font-medium rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="pagination.current_page === 1 ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'">
                            Previous
                        </button>
                        <button @click="fetchAdvances(pagination.current_page + 1)" 
                            :disabled="pagination.current_page === pagination.last_page"
                            class="px-4 py-2 text-sm font-medium rounded-lg disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="pagination.current_page === pagination.last_page ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.5 14.25H9v-2h4.5v2zm0-3.75H9V7h4.5v5.5z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">No Salary Advance Requests</h3>
            <p class="text-gray-500">Click "Request Advance" to create a new request.</p>
        </div>

        <!-- Request Advance Modal -->
        <div v-if="showApplyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            @click.self="showApplyModal = false">
            <div class="relative top-20 mx-auto p-8 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Request Salary Advance</h3>
                <form @submit.prevent="submitAdvanceRequest" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Employee*</label>
                        <SearchableSelect v-model="advanceForm.employee_id" :options="employeeOptions"
                            placeholder="Select employee..." search-placeholder="Search by name or code..." :disabled="isEmployee" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Advance Type*</label>
                        <select v-model="advanceForm.advance_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="salary">Regular Salary Advance</option>
                            <option value="emergency_salary">Emergency Salary Advance</option>
                            <option value="festival">Festival Advance</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (PKR)*</label>
                        <input v-model="advanceForm.amount" type="number" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Number of Installments*</label>
                        <select v-model="advanceForm.installments" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option :value="1">1 Month</option>
                            <option :value="2">2 Months</option>
                            <option :value="3">3 Months</option>
                            <option :value="6">6 Months</option>
                            <option :value="12">12 Months</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Required Date*</label>
                        <input v-model="advanceForm.required_date" type="date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose*</label>
                        <textarea v-model="advanceForm.purpose" required rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <button type="button" @click="showApplyModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            @click.self="closeEditModal">
            <div class="relative top-20 mx-auto p-8 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Advance Request</h3>
                <form @submit.prevent="updateAdvance" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount (PKR)*</label>
                        <input v-model="advanceForm.amount" type="number" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Number of Installments*</label>
                        <select v-model="advanceForm.installments" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option :value="1">1 Month</option>
                            <option :value="2">2 Months</option>
                            <option :value="3">3 Months</option>
                            <option :value="6">6 Months</option>
                            <option :value="12">12 Months</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Required Date*</label>
                        <input v-model="advanceForm.required_date" type="date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"/>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose*</label>
                        <textarea v-model="advanceForm.purpose" required rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>

                    <div class="flex justify-end gap-4 mt-6">
                        <button type="button" @click="closeEditModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">
                            Update Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Approve Modal -->
        <div v-if="showApproveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Approve Advance Request</h3>
                </div>
                <div class="px-6 py-5">
                    <p class="text-gray-700 mb-4">Are you sure you want to approve this salary advance request?</p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-700"><strong>Employee:</strong> {{ getEmployeeName(selectedAdvanceForAction?.employee) }}</p>
                        <p class="text-sm text-gray-700"><strong>Amount:</strong> Rs. {{ formatCurrency(selectedAdvanceForAction?.amount) }}</p>
                        <p class="text-sm text-gray-700"><strong>Installments:</strong> {{ selectedAdvanceForAction?.installments }} months</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Remarks (Optional)</label>
                        <textarea v-model="approvalRemarks" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
                    <button @click="closeApproveModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="approveAdvance"
                        class="px-5 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                        Approve
                    </button>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="showRejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Reject Advance Request</h3>
                </div>
                <div class="px-6 py-5">
                    <p class="text-gray-700 mb-4">Are you sure you want to reject this salary advance request?</p>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-700"><strong>Employee:</strong> {{ getEmployeeName(selectedAdvanceForAction?.employee) }}</p>
                        <p class="text-sm text-gray-700"><strong>Amount:</strong> Rs. {{ formatCurrency(selectedAdvanceForAction?.amount) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason*</label>
                        <textarea v-model="rejectionReason" required rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
                    <button @click="closeRejectModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="rejectAdvance"
                        class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                        Reject
                    </button>
                </div>
            </div>
        </div>

        <!-- Disburse Modal -->
        <div v-if="showDisburseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Disburse Salary Advance</h3>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <p class="text-gray-700">Mark this advance as paid/disbursed to the employee.</p>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-sm text-gray-700"><strong>Employee:</strong> {{ getEmployeeName(selectedAdvanceForAction?.employee) }}</p>
                        <p class="text-sm text-gray-700"><strong>Amount:</strong> Rs. {{ formatCurrency(selectedAdvanceForAction?.amount) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date*</label>
                        <input v-model="disburseForm.payment_date" type="date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method*</label>
                        <select v-model="disburseForm.payment_method" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Reference</label>
                        <input v-model="disburseForm.payment_reference" type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            placeholder="Transaction ID, Cheque #, etc."/>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
                    <button @click="closeDisburseModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="disburseAdvance"
                        class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                        Confirm Disburse
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import SearchableSelect from '../../components/SearchableSelect.vue';
import { useNotification } from '@/composables/useNotification';

const { success, error: showError, info } = useNotification();

const advances = ref([]);
const employees = ref([]);
const loading = ref(false);
const error = ref(null);
const searchQuery = ref('');
const filters = ref({ status: '', employee_id: '' });
const pagination = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0 });

const showApplyModal = ref(false);
const showEditModal = ref(false);
const showApproveModal = ref(false);
const showRejectModal = ref(false);
const showDisburseModal = ref(false);
const selectedAdvanceForAction = ref(null);
const editingAdvance = ref(null);
const rejectionReason = ref('');
const approvalRemarks = ref('');

const user = JSON.parse(localStorage.getItem('user') || '{}');
const isAdminOrManager = computed(() => user.role === 'admin' || user.role === 'manager');
const isEmployee = computed(() => user.role === 'employee');
const currentEmployeeId = computed(() => user.employee?.id || null);

const advanceForm = ref({
    employee_id: '',
    advance_type: 'salary',
    amount: '',
    installments: 3,
    required_date: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
    purpose: ''
});

const disburseForm = ref({
    payment_date: new Date().toISOString().split('T')[0],
    payment_method: 'bank_transfer',
    payment_reference: ''
});

const stats = computed(() => {
    const list = advances.value || [];
    return {
        total: list.length,
        pending: list.filter(a => a.status === 'pending').length,
        approved: list.filter(a => a.status === 'approved').length,
        totalAmount: list.reduce((sum, a) => sum + Number(a.amount || 0), 0)
    };
});

const employeeOptions = computed(() => {
    return employees.value.map(emp => ({
        value: emp.id,
        label: `${emp.employee_code} - ${emp.first_name} ${emp.last_name}${emp.department ? ' (' + emp.department.name + ')' : ''}`
    }));
});

const fetchAdvances = async (page = 1) => {
    loading.value = true;
    error.value = null;
    try {
        const params = new URLSearchParams({ page });
        if (filters.value.status) params.append('status', filters.value.status);
        if (filters.value.employee_id) params.append('employee_id', filters.value.employee_id);
        if (searchQuery.value) params.append('search', searchQuery.value);
        
        const response = await axios.get('/salary-advances', { params });
        advances.value = response.data.data || [];
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            total: response.data.total,
            from: response.data.from,
            to: response.data.to
        };
    } catch (err) {
        error.value = 'Failed to load salary advances';
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const fetchEmployees = async () => {
    try {
        const response = await axios.get('/employees/dropdown');
        employees.value = response.data.data || response.data || [];
    } catch (err) {
        console.error('Failed to load employees:', err);
    }
};

const handleSearch = () => {
    fetchAdvances(1);
};

const submitAdvanceRequest = async () => {
    if (!advanceForm.value.employee_id) {
        alert('Please select an employee');
        return;
    }

    try {
        await axios.post('/salary-advances', advanceForm.value);
        showApplyModal.value = false;
        resetForm();
        fetchAdvances();
        success('Salary advance request submitted successfully!');
    } catch (err) {
        console.error('Failed to submit:', err);
        showError(err.response?.data?.message || 'Failed to submit request');
    }
};

const editAdvance = (advance) => {
    editingAdvance.value = advance;
    advanceForm.value = {
        amount: advance.amount,
        installments: advance.installments,
        required_date: advance.required_date,
        purpose: advance.purpose
    };
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editingAdvance.value = null;
    resetForm();
};

const updateAdvance = async () => {
    try {
        await axios.put(`/salary-advances/${editingAdvance.value.id}`, advanceForm.value);
        showEditModal.value = false;
        editingAdvance.value = null;
        resetForm();
        fetchAdvances();
        success('Advance request updated successfully!');
    } catch (err) {
        console.error('Failed to update:', err);
        showError(err.response?.data?.message || 'Failed to update request');
    }
};

const openApproveModal = (advance) => {
    selectedAdvanceForAction.value = advance;
    showApproveModal.value = true;
};

const closeApproveModal = () => {
    showApproveModal.value = false;
    selectedAdvanceForAction.value = null;
    approvalRemarks.value = '';
};

const approveAdvance = async () => {
    if (!confirm('Are you sure you want to approve this salary advance request?')) {
        return;
    }

    try {
        await axios.post(`/salary-advances/${selectedAdvanceForAction.value.id}/approve`, {
            remarks: approvalRemarks.value
        });
        closeApproveModal();
        fetchAdvances();
        success('Advance request approved successfully!');
    } catch (err) {
        console.error('Failed to approve:', err);
        showError(err.response?.data?.message || 'Failed to approve request');
    }
};

const openRejectModal = (advance) => {
    selectedAdvanceForAction.value = advance;
    showRejectModal.value = true;
};

const closeRejectModal = () => {
    showRejectModal.value = false;
    selectedAdvanceForAction.value = null;
    rejectionReason.value = '';
};

const rejectAdvance = async () => {
    if (!rejectionReason.value) {
        showError('Please provide a rejection reason');
        return;
    }

    if (!confirm('Are you sure you want to reject this salary advance request?')) {
        return;
    }

    try {
        await axios.post(`/salary-advances/${selectedAdvanceForAction.value.id}/reject`, {
            rejection_reason: rejectionReason.value
        });
        closeRejectModal();
        fetchAdvances();
        info('Advance request rejected');
    } catch (err) {
        console.error('Failed to reject:', err);
        showError(err.response?.data?.message || 'Failed to reject request');
    }
};

const openDisburseModal = (advance) => {
    selectedAdvanceForAction.value = advance;
    showDisburseModal.value = true;
};

const closeDisburseModal = () => {
    showDisburseModal.value = false;
    selectedAdvanceForAction.value = null;
    disburseForm.value = {
        payment_date: new Date().toISOString().split('T')[0],
        payment_method: 'bank_transfer',
        payment_reference: ''
    };
};

const disburseAdvance = async () => {
    if (!confirm('Are you sure you want to mark this advance as disbursed?')) {
        return;
    }

    try {
        await axios.post(`/salary-advances/${selectedAdvanceForAction.value.id}/disburse`, disburseForm.value);
        closeDisburseModal();
        fetchAdvances();
        success('Advance disbursed successfully!');
    } catch (err) {
        console.error('Failed to disburse:', err);
        showError(err.response?.data?.message || 'Failed to disburse advance');
    }
};

const resetForm = () => {
    advanceForm.value = {
        employee_id: '',
        advance_type: 'salary',
        amount: '',
        installments: 3,
        required_date: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
        purpose: ''
    };
};

const getEmployeeName = (emp) => emp?.user?.name || `${emp?.first_name || ''} ${emp?.last_name || ''}`.trim() || 'N/A';
const formatCurrency = (val) => Number(val || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
const capitalise = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
const formatType = (type) => type ? type.replace(/_/g, ' ') : '';

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-blue-100 text-blue-800',
        paid: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};



onMounted(() => {
    fetchAdvances();
    fetchEmployees();
    // Pre-fill employee_id for employee role
    if (isEmployee.value && currentEmployeeId.value) {
        advanceForm.value.employee_id = currentEmployeeId.value;
    }
});
</script>
