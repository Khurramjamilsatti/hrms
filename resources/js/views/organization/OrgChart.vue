<template>
    <div class="p-6 space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Organization</h1>
                <p class="text-gray-600 mt-1">Company structure and employee directory</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs font-medium">Total Employees</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.total_employees || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="current Color" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-xs font-medium">Departments</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.total_departments || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-xs font-medium">Managers</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.total_managers || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-xs font-medium">Designations</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.total_designations || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-xs font-medium">Avg Team Size</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.avg_team_size || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-xs font-medium">Active</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.active_employees || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="flex space-x-4 border-b px-6">
                <button 
                    v-for="tab in tabs" 
                    :key="tab.key"
                    @click="activeTab = tab.key"
                    class="px-4 py-3 font-medium transition-colors relative"
                    :class="activeTab === tab.key ? 'text-black' : 'text-gray-600 hover:text-gray-900'"
                >
                    {{ tab.label }}
                    <div v-if="activeTab === tab.key" class="absolute bottom-0 left-0 right-0 h-0.5 bg-black"></div>
                </button>
            </div>
        </div>

        <!-- Organization Chart Tab -->
        <div v-if="activeTab === 'chart'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold mb-6">Organization Hierarchy</h3>
            
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full">
                    <div class="flex flex-col items-center">
                        <div v-if="orgChart" class="org-node">
                            <OrgNode :node="orgChart" />
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            No organization chart data available
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Stats Tab -->
        <div v-if="activeTab === 'departments'">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="dept in departmentStats" :key="dept.id" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                    <h4 class="font-semibold text-lg mb-4 text-gray-900">{{ dept.name }}</h4>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Employees</span>
                            <span class="font-bold text-2xl text-blue-600">{{ dept.employees_count || 0 }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Manager</span>
                            <span class="font-medium text-sm">{{ dept.manager ? `${dept.manager.first_name} ${dept.manager.last_name}` : 'N/A' }}</span>
                        </div>
                        
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500">Status</span>
                                <span :class="dept.is_active ? 'text-green-600 font-medium' : 'text-gray-400'">
                                    {{ dept.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <button @click="viewDepartmentDetails(dept)" class="mt-4 w-full px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors">
                        View Details
                    </button>
                </div>
                <div v-if="departmentStats.length === 0" class="col-span-3 text-center py-8 text-gray-500">
                    No departments found
                </div>
            </div>

            <!-- Department Stats Pagination -->
            <div v-if="deptPagination.total > 0" class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-4 mt-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ deptPagination.from }}</span> to <span class="font-medium">{{ deptPagination.to }}</span> of <span class="font-medium">{{ deptPagination.total }}</span> departments
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm text-gray-600">Per Page:</label>
                            <select v-model="deptPagination.perPage" @change="changeDeptPerPage"
                                class="px-3 py-1 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                <option :value="20">20</option>
                                <option :value="40">40</option>
                                <option :value="60">60</option>
                                <option :value="80">80</option>
                                <option :value="100">100</option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <button @click="changeDeptPage(deptPagination.current_page - 1)" :disabled="deptPagination.current_page === 1"
                                class="px-3 py-1 border border-gray-300 rounded-lg disabled:opacity-50 hover:bg-gray-50">
                                Previous
                            </button>
                            <button v-for="page in displayedDeptPages" :key="page" @click="changeDeptPage(page)"
                                :class="page === deptPagination.current_page ? 'bg-black text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                                class="px-3 py-1 border border-gray-300 rounded-lg">
                                {{ page }}
                            </button>
                            <button @click="changeDeptPage(deptPagination.current_page + 1)" :disabled="deptPagination.current_page === deptPagination.last_page"
                                class="px-3 py-1 border border-gray-300 rounded-lg disabled:opacity-50 hover:bg-gray-50">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Directory Tab -->
        <div v-if="activeTab === 'directory'">
            <!-- Search & Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" v-model="searchQuery" placeholder="Search by name or email..." 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select v-model="filterDepartment" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Departments</option>
                            <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                {{ dept.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select v-model="filterStatus" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="on_leave">On Leave</option>
                            <option value="probation">Probation</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                        <select v-model="pagination.perPage" @change="changePerPage"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option :value="20">20</option>
                            <option :value="40">40</option>
                            <option :value="60">60</option>
                            <option :value="80">80</option>
                            <option :value="100">100</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button @click="fetchDirectory" class="w-full px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">
                            Search
                        </button>
                    </div>
                </div>
            </div>

            <!-- Directory Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Designation</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="emp in directory" :key="emp.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ emp.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ emp.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ emp.employee?.department?.name || '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ emp.employee?.designation?.title || emp.employee?.designation?.name || '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ emp.employee?.phone || '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                                        :class="{
                                            'bg-green-100 text-green-800': emp.employee?.employment_status === 'active',
                                            'bg-yellow-100 text-yellow-800': emp.employee?.employment_status === 'on_leave',
                                            'bg-blue-100 text-blue-800': emp.employee?.employment_status === 'probation',
                                            'bg-gray-100 text-gray-800': emp.employee?.employment_status === 'terminated'
                                        }">
                                        {{ emp.employee?.employment_status ? emp.employee.employment_status.replace('_', ' ').toUpperCase() : '-' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="directory.length === 0">
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    No employees found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.total > 0" class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-4 mt-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ pagination.from }}</span> to <span class="font-medium">{{ pagination.to }}</span> of <span class="font-medium">{{ pagination.total }}</span> employees
                    </div>
                    <div class="flex space-x-2">
                        <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                            class="px-3 py-1 border border-gray-300 rounded-lg disabled:opacity-50 hover:bg-gray-50">
                            Previous
                        </button>
                        <button v-for="page in displayedPages" :key="page" @click="changePage(page)"
                            :class="page === pagination.current_page ? 'bg-black text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                            class="px-3 py-1 border border-gray-300 rounded-lg">
                            {{ page }}
                        </button>
                        <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 py-1 border border-gray-300 rounded-lg disabled:opacity-50 hover:bg-gray-50">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Details Modal -->
        <div v-if="showDeptModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">{{ selectedDept?.name }}</h3>
                        <p class="text-sm text-blue-100 mt-1">Department Details</p>
                    </div>
                    <button @click="closeDeptModal" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                    <!-- Department Info -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">Total Employees</div>
                            <div class="text-3xl font-bold text-blue-600">{{ selectedDept?.employees_count || 0 }}</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">Department Manager</div>
                            <div class="text-lg font-semibold text-gray-900">
                                {{ selectedDept?.manager ? `${selectedDept.manager.first_name} ${selectedDept.manager.last_name}` : 'Not Assigned' }}
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">Status</div>
                            <div class="text-lg font-semibold" :class="selectedDept?.is_active ? 'text-green-600' : 'text-gray-400'">
                                {{ selectedDept?.is_active ? 'Active' : 'Inactive' }}
                            </div>
                        </div>
                    </div>

                    <!-- Department Employees -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-lg mb-4">Department Employees</h4>
                        <div class="bg-white rounded-lg overflow-hidden border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Designation</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="emp in deptEmployees" :key="emp.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ emp.name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ emp.email }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ emp.employee?.designation?.title || '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ emp.employee?.phone || '-' }}</td>
                                    </tr>
                                    <tr v-if="deptEmployees.length === 0">
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                            No employees in this department
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button @click="closeDeptModal" class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Notification Toast -->
        <div v-if="notification.show" 
            :class="notification.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
            class="fixed bottom-4 right-4 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in">
            {{ notification.message }}
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, defineComponent, h, reactive, computed } from 'vue';
import axios from 'axios';

const activeTab = ref('chart');
const orgChart = ref(null);
const departmentStats = ref([]);
const directory = ref([]);
const departments = ref([]);
const searchQuery = ref('');
const filterDepartment = ref('');
const filterStatus = ref('');

const statistics = reactive({
    total_employees: 0,
    total_departments: 0,
    total_managers: 0,
    total_designations: 0,
    avg_team_size: 0,
    active_employees: 0
});

const pagination = reactive({
    current_page: 1,
    last_page: 1,
    perPage: 20,
    total: 0,
    from: 0,
    to: 0
});

const deptPagination = reactive({
    current_page: 1,
    last_page: 1,
    perPage: 20,
    total: 0,
    from: 0,
    to: 0
});

const notification = reactive({
    show: false,
    message: '',
    type: 'success'
});

const tabs = [
    { key: 'chart', label: 'Organization Chart' },
    { key: 'departments', label: 'Department Stats' },
    { key: 'directory', label: 'Employee Directory' }
];

const displayedPages = computed(() => {
    const pages = [];
    const current = pagination.current_page;
    const last = pagination.last_page;
    
    if (last <= 7) {
        for (let i = 1; i <= last; i++) pages.push(i);
    } else {
        if (current <= 4) {
            for (let i = 1; i <= 5; i++) pages.push(i);
            pages.push('...');
            pages.push(last);
        } else if (current >= last - 3) {
            pages.push(1);
            pages.push('...');
            for (let i = last - 4; i <= last; i++) pages.push(i);
        } else {
            pages.push(1);
            pages.push('...');
            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
            pages.push('...');
            pages.push(last);
        }
    }
    return pages;
});

const displayedDeptPages = computed(() => {
    const pages = [];
    const current = deptPagination.current_page;
    const last = deptPagination.last_page;
    
    if (last <= 7) {
        for (let i = 1; i <= last; i++) pages.push(i);
    } else {
        if (current <= 4) {
            for (let i = 1; i <= 5; i++) pages.push(i);
            pages.push('...');
            pages.push(last);
        } else if (current >= last - 3) {
            pages.push(1);
            pages.push('...');
            for (let i = last - 4; i <= last; i++) pages.push(i);
        } else {
            pages.push(1);
            pages.push('...');
            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
            pages.push('...');
            pages.push(last);
        }
    }
    return pages;
});

// Org Node Component
const OrgNode = defineComponent({
    props: ['node'],
    setup(props) {
        return () => {
            const node = props.node;
            return h('div', { class: 'flex flex-col items-center' }, [
                h('div', { class: 'bg-white border-2 border-blue-500 rounded-lg p-4 mb-4 shadow-md min-w-48 text-center hover:shadow-lg transition-shadow' }, [
                    h('div', { class: 'font-semibold text-lg' }, node.name),
                    h('div', { class: 'text-sm text-gray-600' }, node.designation),
                    h('div', { class: 'text-xs text-gray-500 mt-1' }, node.department),
                    node.email && h('div', { class: 'text-xs text-blue-600 mt-1' }, node.email)
                ]),
                node.subordinates && node.subordinates.length > 0 && h('div', { class: 'flex space-x-8' }, 
                    node.subordinates.map(sub => h(OrgNode, { key: sub.id, node: sub }))
                )
            ]);
        };
    }
});

const fetchOrgChart = async () => {
    try {
        const response = await axios.get('organization/chart');
        orgChart.value = response.data;
    } catch (error) {
        console.error('Failed to fetch org chart:', error);
        showNotification('Failed to load organization chart', 'error');
    }
};

const fetchDepartmentStats = async () => {
    try {
        const params = new URLSearchParams();
        params.append('page', deptPagination.current_page);
        params.append('per_page', deptPagination.perPage);
        
        const response = await axios.get(`organization/department-stats?${params.toString()}`);
        const data = response.data;
        
        if (data.data) {
            departmentStats.value = data.data;
            deptPagination.current_page = data.current_page || 1;
            deptPagination.last_page = data.last_page || 1;
            deptPagination.total = data.total || 0;
            deptPagination.from = data.from || 0;
            deptPagination.to = data.to || 0;
            statistics.total_departments = data.total;
        } else {
            departmentStats.value = data;
            statistics.total_departments = data.length;
        }
    } catch (error) {
        console.error('Failed to fetch department stats:', error);
        showNotification('Failed to load department statistics', 'error');
    }
};

const fetchDirectory = async () => {
    try {
        const params = new URLSearchParams();
        params.append('page', pagination.current_page);
        params.append('per_page', pagination.perPage);
        if (searchQuery.value) params.append('search', searchQuery.value);
        if (filterDepartment.value) params.append('department_id', filterDepartment.value);
        if (filterStatus.value) params.append('status', filterStatus.value);
        
        const response = await axios.get(`organization/directory?${params.toString()}`);
        const data = response.data;
        
        if (data.data) {
            directory.value = data.data;
            pagination.current_page = data.current_page || 1;
            pagination.last_page = data.last_page || 1;
            pagination.total = data.total || 0;
            pagination.from = data.from || 0;
            pagination.to = data.to || 0;
        } else {
            directory.value = data;
        }
        
        calculateStatistics();
    } catch (error) {
        console.error('Failed to fetch directory:', error);
        showNotification('Failed to load employee directory', 'error');
    }
};

const fetchDepartments = async () => {
    try {
        const response = await axios.get('departments');
        departments.value = response.data.data || response.data;
    } catch (error) {
        console.error('Failed to fetch departments:', error);
    }
};

const calculateStatistics = () => {
    // Use total from pagination (actual count from database)
    statistics.total_employees = pagination.total;
    statistics.active_employees = pagination.total; // All returned employees are active
    statistics.total_managers = directory.value.filter(e => e.role === 'manager' || e.role === 'admin').length;
    
    // Calculate avg team size
    if (statistics.total_departments > 0) {
        statistics.avg_team_size = Math.round(statistics.total_employees / statistics.total_departments);
    }
};

const changePage = (page) => {
    if (page >= 1 && page <= pagination.last_page && page !== '...') {
        pagination.current_page = page;
        fetchDirectory();
    }
};

const changePerPage = () => {
    pagination.current_page = 1; // Reset to first page when changing items per page
    fetchDirectory();
};

const changeDeptPage = (page) => {
    if (page >= 1 && page <= deptPagination.last_page && page !== '...') {
        deptPagination.current_page = page;
        fetchDepartmentStats();
    }
};

const changeDeptPerPage = () => {
    deptPagination.current_page = 1; // Reset to first page when changing items per page
    fetchDepartmentStats();
};

// Department Detail Modal
const showDeptModal = ref(false);
const selectedDept = ref(null);
const deptEmployees = ref([]);

const viewDepartmentDetails = async (dept) => {
    selectedDept.value = dept;
    showDeptModal.value = true;
    
    // Fetch department employees
    try {
        const response = await axios.get(`organization/directory?department_id=${dept.id}&per_page=100`);
        deptEmployees.value = response.data.data || response.data;
    } catch (error) {
        console.error('Failed to fetch department employees:', error);
        showNotification('Failed to load department employees', 'error');
    }
};

const closeDeptModal = () => {
    showDeptModal.value = false;
    selectedDept.value = null;
    deptEmployees.value = [];
};

const showNotification = (message, type = 'success') => {
    notification.message = message;
    notification.type = type;
    notification.show = true;
    setTimeout(() => {
        notification.show = false;
    }, 3000);
};

onMounted(() => {
    fetchOrgChart();
    fetchDepartmentStats();
    fetchDirectory();
    fetchDepartments();
});
</script>

<style scoped>
.org-node {
    position: relative;
}

.org-node::before {
    content: '';
    position: absolute;
    top: -20px;
    left: 50%;
    width: 2px;
    height: 20px;
    background-color: #e5e7eb;
}

.org-node::after {
    content: '';
    position: absolute;
    top: -20px;
    left: 0;
    right: 0;
    height: 2px;
    background-color: #e5e7eb;
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
