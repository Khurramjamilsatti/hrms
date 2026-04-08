<template>
    <div class="p-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Salary Components Management</h1>
                <p class="text-gray-600 mt-1">Manage employee salary structure and allowances</p>
            </div>
            <button @click="$router.back()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                Back to Employee
            </button>
        </div>

        <!-- Employee Info -->
        <div v-if="employee" class="bg-white rounded-lg shadow border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Information</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Employee Code</p>
                    <p class="font-medium text-gray-900">{{ employee.employee_code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Name</p>
                    <p class="font-medium text-gray-900">{{ getEmployeeName(employee) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Department</p>
                    <p class="font-medium text-gray-900">{{ employee.department?.name || 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Current Salary Structure -->
        <div v-if="currentSalary" class="bg-white rounded-lg shadow border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Current Salary Structure</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Active salary details with all components</p>
                </div>
                <div class="flex items-center gap-3">
                    <span v-if="currentSalary.effective_to === null" class="px-3 py-1.5 bg-green-100 text-green-800 text-xs font-medium rounded-full flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Active
                    </span>
                    <span v-else class="px-3 py-1.5 bg-red-100 text-red-800 text-xs font-medium rounded-full flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Inactive (Ended: {{ formatDate(currentSalary.effective_to) }})
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6 mb-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Basic Salary</p>
                    <p class="text-2xl font-bold text-gray-900">Rs. {{ formatCurrency(currentSalary.basic_salary) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Effective: {{ formatDate(currentSalary.effective_from) }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Gross Salary</p>
                    <p class="text-2xl font-bold text-green-600">Rs. {{ formatCurrency(currentSalary.gross_salary || 0) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Basic + Allowances</p>
                </div>
            </div>
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Salary Components</h4>
                <div class="space-y-2">
                    <div v-for="comp in currentSalary.components" :key="comp.id" class="flex justify-between items-center py-2 border-b border-gray-100">
                        <div>
                            <p class="font-medium text-gray-900">{{ comp.salary_component?.name || 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ comp.salary_component?.code }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold" :class="comp.salary_component?.type === 'earning' ? 'text-green-600' : 'text-red-600'">
                                {{ comp.salary_component?.type === 'earning' ? '+' : '-' }} Rs. {{ formatCurrency(comp.amount) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-3 gap-4 text-sm text-gray-600">
                <div>
                    <span class="font-medium">Effective From:</span> {{ formatDate(currentSalary.effective_from) }}
                </div>
                <div>
                    <span class="font-medium">Effective To:</span> {{ currentSalary.effective_to ? formatDate(currentSalary.effective_to) : 'Current' }}
                </div>
                <div>
                    <span class="font-medium">Payment Mode:</span> {{ capitalise(currentSalary.payment_mode) }}
                </div>
            </div>
        </div>

        <!-- Apply Salary Increment -->
        <div v-if="currentSalary" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow border border-blue-200 p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Apply Salary Increment</h3>
                    <p class="text-sm text-gray-600">Automatically apply increment to basic salary and allowances</p>
                </div>
                <button v-if="!showIncrementForm" @click="showIncrementForm = true" type="button"
                    class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">
                    Apply Increment
                </button>
            </div>

            <form v-if="showIncrementForm" @submit.prevent="applyIncrement" class="mt-6 space-y-4 bg-white rounded-lg p-6 border border-gray-200">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Increment Type*</label>
                        <select v-model="incrementForm.increment_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (PKR)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ incrementForm.increment_type === 'percentage' ? 'Percentage' : 'Amount (PKR)' }}*
                        </label>
                        <input v-model="incrementForm.increment_value" type="number" step="0.01" required min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Effective From*</label>
                        <input v-model="incrementForm.effective_from" type="date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"/>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea v-model="incrementForm.remarks" rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                        placeholder="Annual increment, performance-based increment, etc."></textarea>
                </div>

                <!-- Preview Calculation -->
                <div v-if="incrementForm.increment_value > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-2">Preview New Salary</h4>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Current Basic Salary</p>
                            <p class="font-bold text-gray-900">Rs. {{ formatCurrency(currentSalary.basic_salary) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Increment Amount</p>
                            <p class="font-bold text-green-600">+ Rs. {{ formatCurrency(calculateIncrementAmount()) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">New Basic Salary</p>
                            <p class="font-bold text-blue-600">Rs. {{ formatCurrency(calculateNewBasicSalary()) }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button" @click="cancelIncrement"
                        class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" :disabled="loading"
                        class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800 disabled:opacity-50">
                        {{ loading ? 'Applying...' : 'Apply Increment' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Add New Salary Structure -->
        <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Add New Salary Structure</h3>
                <p class="text-sm text-gray-600 mt-1">Define basic salary and add allowances/deductions</p>
            </div>
            
            <form @submit.prevent="saveSalaryStructure" class="space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Basic Salary (PKR)*</label>
                        <input v-model="salaryForm.basic_salary" type="number" required step="0.01" min="0"
                            placeholder="50000"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Effective From*</label>
                        <input v-model="salaryForm.effective_from" type="date" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"/>
                    </div>
                </div>

                <!-- Components Section -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h4 class="text-md font-semibold text-gray-900">Salary Components (Allowances & Deductions)</h4>
                            <p class="text-xs text-gray-500 mt-0.5">Add earning components like HRA, Medical, Transport or deductions like Tax, EOBI</p>
                        </div>
                        <button type="button" @click="addComponent"
                            class="px-4 py-2 text-sm bg-black text-white rounded-lg hover:bg-gray-800 transition-colors">
                            + Add Component
                        </button>
                    </div>

                    <div v-if="salaryForm.components.length === 0" class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">No components added yet</p>
                        <p class="text-xs text-gray-500">Click "Add Component" to include allowances or deductions</p>
                    </div>

                    <div v-else class="space-y-3">
                        <div v-for="(component, index) in salaryForm.components" :key="index"
                            class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-start gap-4">
                                <div class="flex-1 min-w-0">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Component Type</label>
                                    <select v-model="component.salary_component_id" required
                                        @change="updateComponentDetails(index)"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                        <option value="">Select Component</option>
                                        <optgroup label="✅ Earnings / Allowances">
                                            <option v-for="comp in availableComponents.earnings" :key="comp.id" :value="comp.id">
                                                {{ comp.name }} ({{ comp.code }})
                                            </option>
                                        </optgroup>
                                        <optgroup label="❌ Deductions">
                                            <option v-for="comp in availableComponents.deductions" :key="comp.id" :value="comp.id">
                                                {{ comp.name }} ({{ comp.code }})
                                            </option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="w-48">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount (PKR)</label>
                                    <input v-model="component.amount" type="number" required step="0.01" min="0"
                                        placeholder="0.00"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"/>
                                </div>
                                <div class="flex items-end pb-0.5">
                                    <button type="button" @click="confirmRemoveComponent(index)"
                                        class="p-2.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Remove component">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <!-- Component Type Badge -->
                            <div v-if="component.componentDetails" class="mt-3 pt-3 border-t border-gray-200">
                                <span :class="component.componentDetails.type === 'earning' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ component.componentDetails.type === 'earning' ? '+ Earning' : '- Deduction' }}
                                </span>
                                <span v-if="component.componentDetails.is_taxable" class="ml-2 text-xs text-orange-600">
                                    • Taxable
                                </span>
                                <span v-else class="ml-2 text-xs text-gray-500">
                                    • Tax Exempt
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calculated Gross Salary -->
                <div v-if="salaryForm.basic_salary || salaryForm.components.length > 0" class="border-t border-gray-200 pt-6">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Salary Calculation Summary</h4>
                        <div class="grid grid-cols-4 gap-6 text-center">
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Basic Salary</p>
                                <p class="text-xl font-bold text-gray-900">Rs. {{ formatCurrency(calculatedBasic) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Total Allowances</p>
                                <p class="text-xl font-bold text-green-600">+ Rs. {{ formatCurrency(totalAllowances) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Total Deductions</p>
                                <p class="text-xl font-bold text-red-600">- Rs. {{ formatCurrency(totalDeductions) }}</p>
                            </div>
                            <div class="border-l-2 border-blue-300">
                                <p class="text-xs text-gray-600 mb-1">Gross Salary</p>
                                <p class="text-2xl font-bold text-blue-600">Rs. {{ formatCurrency(calculatedGross) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Payment Details</h4>
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Mode*</label>
                            <select v-model="salaryForm.payment_mode" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                <option value="bank_transfer">🏦 Bank Transfer</option>
                                <option value="cash">💵 Cash</option>
                                <option value="cheque">📝 Cheque</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                            <input v-model="salaryForm.bank_name" type="text" placeholder="e.g., Meezan Bank"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                            <input v-model="salaryForm.account_number" type="text" placeholder="e.g., 01234567890"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"/>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                    <textarea v-model="salaryForm.remarks" rows="3" placeholder="Add any notes or comments about this salary structure..."
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"></textarea>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                    <button type="button" @click="$router.back()"
                        class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" :disabled="!salaryForm.basic_salary"
                        class="px-6 py-2.5 bg-black text-white rounded-lg hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        💾 Save Salary Structure
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const employeeId = route.params.id;

const employee = ref(null);
const currentSalary = ref(null);
const availableComponents = ref({ earnings: [], deductions: [] });
const loading = ref(false);
const showIncrementForm = ref(false);

const salaryForm = ref({
    basic_salary: '',
    effective_from: new Date().toISOString().split('T')[0],
    payment_mode: 'bank_transfer',
    bank_name: '',
    account_number: '',
    remarks: '',
    components: []
});

const incrementForm = ref({
    increment_type: 'percentage',
    increment_value: '',
    effective_from: new Date().toISOString().split('T')[0],
    remarks: ''
});

const calculatedBasic = computed(() => Number(salaryForm.value.basic_salary) || 0);

const totalAllowances = computed(() => {
    return salaryForm.value.components.reduce((total, comp) => {
        const component = availableComponents.value.earnings.find(c => c.id == comp.salary_component_id) ||
                         availableComponents.value.deductions.find(c => c.id == comp.salary_component_id);
        if (component?.type === 'earning') {
            return total + Number(comp.amount || 0);
        }
        return total;
    }, 0);
});

const totalDeductions = computed(() => {
    return salaryForm.value.components.reduce((total, comp) => {
        const component = availableComponents.value.earnings.find(c => c.id == comp.salary_component_id) ||
                         availableComponents.value.deductions.find(c => c.id == comp.salary_component_id);
        if (component?.type === 'deduction') {
            return total + Number(comp.amount || 0);
        }
        return total;
    }, 0);
});

const calculatedGross = computed(() => {
    const basic = calculatedBasic.value;
    const earnings = salaryForm.value.components.reduce((total, comp) => {
        const component = availableComponents.value.earnings.find(c => c.id == comp.salary_component_id);
        if (component) {
            return total + Number(comp.amount || 0);
        }
        return total;
    }, 0);
    
    return basic + earnings;
});

const fetchEmployee = async () => {
    try {
        const response = await axios.get(`/employees/${employeeId}`);
        employee.value = response.data;
    } catch (err) {
        console.error('Failed to load employee:', err);
        showNotification('error', 'Failed to load employee details');
    }
};

const fetchCurrentSalary = async () => {
    try {
        const response = await axios.get(`/salary-components/employees/${employeeId}`);
        if (response.data.length > 0) {
            // Find the active salary (where effective_to is NULL)
            const activeSalary = response.data.find(salary => salary.effective_to === null);
            // If no active salary found, use the most recent one
            currentSalary.value = activeSalary || response.data[0];
        }
    } catch (err) {
        console.error('Failed to load current salary:', err);
    }
};

const fetchAvailableComponents = async () => {
    try {
        const response = await axios.get('/salary-components');
        const components = response.data || [];
        availableComponents.value = {
            earnings: components.filter(c => c.type === 'earning'),
            deductions: components.filter(c => c.type === 'deduction')
        };
    } catch (err) {
        console.error('Failed to load components:', err);
        showNotification('error', 'Failed to load salary components');
    }
};

const addComponent = () => {
    salaryForm.value.components.push({
        salary_component_id: '',
        amount: '',
        componentDetails: null
    });
};

const removeComponent = (index) => {
    salaryForm.value.components.splice(index, 1);
};

const confirmRemoveComponent = async (index) => {
    const confirmed = await showConfirmDialog(
        'Remove Component?',
        'Are you sure you want to remove this salary component? This will not affect existing salary records.',
        'Remove',
        'Cancel'
    );
    
    if (confirmed) {
        removeComponent(index);
        showNotification('info', 'Component removed successfully');
    }
};

const updateComponentDetails = (index) => {
    const componentId = salaryForm.value.components[index].salary_component_id;
    const component = availableComponents.value.earnings.find(c => c.id == componentId) ||
                     availableComponents.value.deductions.find(c => c.id == componentId);
    salaryForm.value.components[index].componentDetails = component || null;
};

const saveSalaryStructure = async () => {
    if (!salaryForm.value.basic_salary) {
        showNotification('error', 'Please enter basic salary');
        return;
    }

    if (salaryForm.value.components.length === 0) {
        if (!confirm('No salary components added. Do you want to continue with basic salary only?')) {
            return;
        }
    }

    try {
        // Send salary form data - gross_salary will be calculated on backend
        await axios.post(`/salary-components/employees/${employeeId}`, salaryForm.value);
        
        showNotification('success', 'Salary structure saved successfully!');
        
        setTimeout(() => {
            router.push(`/employees/${employeeId}`);
        }, 1000);
    } catch (err) {
        console.error('Failed to save:', err);
        showNotification('error', err.response?.data?.message || 'Failed to save salary structure');
    }
};

const getEmployeeName = (emp) => {
    return emp?.user?.name || `${emp?.first_name || ''} ${emp?.last_name || ''}`.trim() || 'N/A';
};

const formatCurrency = (val) => {
    return Number(val || 0).toLocaleString('en-PK', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-PK');
};

const capitalise = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1).replace(/_/g, ' ') : '';

const calculateIncrementAmount = () => {
    if (!currentSalary.value || !incrementForm.value.increment_value) return 0;
    
    if (incrementForm.value.increment_type === 'percentage') {
        return (currentSalary.value.basic_salary * incrementForm.value.increment_value) / 100;
    }
    return Number(incrementForm.value.increment_value);
};

const calculateNewBasicSalary = () => {
    if (!currentSalary.value) return 0;
    return Number(currentSalary.value.basic_salary) + calculateIncrementAmount();
};

const applyIncrement = async () => {
    const incrementText = incrementForm.value.increment_type === 'percentage' 
        ? incrementForm.value.increment_value + '%' 
        : 'Rs. ' + formatCurrency(incrementForm.value.increment_value);
    
    // Show confirmation modal
    const confirmed = await showConfirmDialog(
        'Apply Salary Increment?',
        `Are you sure you want to apply ${incrementText} increment to this employee's salary? This will create a new salary record effective from ${incrementForm.value.effective_from}.`,
        'Apply Increment',
        'Cancel'
    );
    
    if (!confirmed) {
        return;
    }

    loading.value = true;
    try {
        const response = await axios.post(`/salary-components/employees/${employeeId}/apply-increment`, incrementForm.value);
        
        showNotification('success', 'Salary increment applied successfully!');
        
        // Reset form and refresh data
        showIncrementForm.value = false;
        incrementForm.value = {
            increment_type: 'percentage',
            increment_value: '',
            effective_from: new Date().toISOString().split('T')[0],
            remarks: ''
        };
        
        await fetchCurrentSalary();
    } catch (err) {
        console.error('Failed to apply increment:', err);
        showNotification('error', err.response?.data?.message || 'Failed to apply increment');
    } finally {
        loading.value = false;
    }
};

const cancelIncrement = () => {
    showIncrementForm.value = false;
    incrementForm.value = {
        increment_type: 'percentage',
        increment_value: '',
        effective_from: new Date().toISOString().split('T')[0],
        remarks: ''
    };
};

const showConfirmDialog = (title, message, confirmText = 'Confirm', cancelText = 'Cancel') => {
    return new Promise((resolve) => {
        const overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        overlay.style.animation = 'fadeIn 0.2s ease-out';
        
        overlay.innerHTML = `
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 animate-scale-in">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">${title}</h3>
                        <p class="text-sm text-gray-600">${message}</p>
                    </div>
                </div>
                <div class="flex gap-3 justify-end mt-6">
                    <button id="cancelBtn" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                        ${cancelText}
                    </button>
                    <button id="confirmBtn" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        ${confirmText}
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        const confirmBtn = overlay.querySelector('#confirmBtn');
        const cancelBtn = overlay.querySelector('#cancelBtn');
        
        const cleanup = (result) => {
            overlay.style.opacity = '0';
            setTimeout(() => overlay.remove(), 200);
            resolve(result);
        };
        
        confirmBtn.addEventListener('click', () => cleanup(true));
        cancelBtn.addEventListener('click', () => cleanup(false));
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) cleanup(false);
        });
    });
};

const showNotification = (type, message) => {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 animate-fade-in ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        type === 'info' ? 'bg-blue-500 text-white' :
        'bg-gray-800 text-white'
    }`;
    
    const icon = type === 'success' ? '✓' : type === 'error' ? '✗' : type === 'info' ? 'ℹ' : '•';
    notification.innerHTML = `
        <span class="text-xl font-bold">${icon}</span>
        <span class="font-medium">${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
};

onMounted(() => {
    fetchEmployee();
    fetchCurrentSalary();
    fetchAvailableComponents();
});
</script>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes scale-in {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.animate-scale-in {
    animation: scale-in 0.2s ease-out;
}

/* Smooth transitions for form elements */
input:focus,
select:focus,
textarea:focus {
    outline: none;
}

/* Better select dropdown styling */
select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

/* Remove number input spinners */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    appearance: textfield;
    -moz-appearance: textfield;
}
</style>
