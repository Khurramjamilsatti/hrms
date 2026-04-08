<template>
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Salary Components Master</h1>
            <p class="text-gray-600 mt-1">Manage salary component types (allowances, bonuses, deductions)</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Earnings</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.earnings }}</p>
                    </div>
                    <svg class="w-12 h-12 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Deductions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.deductions }}</p>
                    </div>
                    <svg class="w-12 h-12 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Active Components</p>
                        <p class="text-2xl font-bold text-gray-900">{{ stats.active }}</p>
                    </div>
                    <svg class="w-12 h-12 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Components Table -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-900">Salary Components</h2>
                    <button @click="showAddModal = true"
                        class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">
                        + Add Component
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calculation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taxable</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="component in components" :key="component.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-mono">{{ component.code }}</code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ component.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="component.type === 'earning' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                    class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ component.type === 'earning' ? '+ Earning' : '- Deduction' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ capitalise(component.calculation_type) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="component.is_taxable" class="text-orange-600">✓ Taxable</span>
                                <span v-else class="text-gray-400">✗ Tax-free</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="component.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                    class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ component.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <button @click="toggleStatus(component)"
                                    class="text-blue-600 hover:text-blue-800 mr-3">
                                    {{ component.is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Component Modal -->
        <div v-if="showAddModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Add Salary Component</h3>
                </div>
                <form @submit.prevent="addComponent" class="px-6 py-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Code*</label>
                            <input v-model="componentForm.code" type="text" required placeholder="e.g., TRANSPORT"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg uppercase"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name*</label>
                            <input v-model="componentForm.name" type="text" required placeholder="e.g., Transport Allowance"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type*</label>
                            <select v-model="componentForm.type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="earning">Earning / Allowance</option>
                                <option value="deduction">Deduction</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Calculation Type*</label>
                            <select v-model="componentForm.calculation_type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="fixed">Fixed Amount</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input v-model="componentForm.is_taxable" type="checkbox" class="mr-2"/>
                            <span class="text-sm text-gray-700">Taxable</span>
                        </label>
                        <label class="flex items-center">
                            <input v-model="componentForm.is_mandatory" type="checkbox" class="mr-2"/>
                            <span class="text-sm text-gray-700">Mandatory</span>
                        </label>
                    </div>
                </form>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
                    <button @click="closeAddModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="addComponent"
                        class="px-5 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800">
                        Add Component
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const components = ref([]);
const showAddModal = ref(false);

const componentForm = ref({
    code: '',
    name: '',
    type: 'earning',
    calculation_type: 'fixed',
    is_taxable: false,
    is_mandatory: false,
    is_active: true
});

const stats = computed(() => ({
    earnings: components.value.filter(c => c.type === 'earning').length,
    deductions: components.value.filter(c => c.type === 'deduction').length,
    active: components.value.filter(c => c.is_active).length
}));

const fetchComponents = async () => {
    try {
        const response = await axios.get('/salary-components');
        components.value = response.data;
    } catch (err) {
        console.error('Failed to fetch components:', err);
        showNotification('error', 'Failed to load salary components');
    }
};

const addComponent = async () => {
    try {
        await axios.post('/salary-components', componentForm.value);
        closeAddModal();
        fetchComponents();
        showNotification('success', 'Salary component added successfully!');
    } catch (err) {
        console.error('Failed to add component:', err);
        showNotification('error', err.response?.data?.message || 'Failed to add component');
    }
};

const toggleStatus = async (component) => {
    if (!confirm(`Are you sure you want to ${component.is_active ? 'deactivate' : 'activate'} this component?`)) {
        return;
    }

    try {
        await axios.put(`/salary-components/${component.id}`, {
            ...component,
            is_active: !component.is_active
        });
        fetchComponents();
        showNotification('success', `Component ${component.is_active ? 'deactivated' : 'activated'} successfully!`);
    } catch (err) {
        console.error('Failed to toggle status:', err);
        showNotification('error', 'Failed to update component status');
    }
};

const closeAddModal = () => {
    showAddModal.value = false;
    componentForm.value = {
        code: '',
        name: '',
        type: 'earning',
        calculation_type: 'fixed',
        is_taxable: false,
        is_mandatory: false,
        is_active: true
    };
};

const capitalise = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';

const showNotification = (type, message) => {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 animate-fade-in ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-gray-800 text-white'
    }`;
    
    const icon = type === 'success' ? '✓' : type === 'error' ? '✗' : '•';
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
    fetchComponents();
});
</script>
