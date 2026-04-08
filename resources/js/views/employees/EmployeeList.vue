<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Employees</h1>
      <router-link 
        v-if="can('employees.create')"
        to="/employees/create" 
        class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Employee
      </router-link>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="flex items-center space-x-3">
        <div class="flex-1 relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search employees by name, code, email..."
            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
            @input="handleSearch"
          />
        </div>
        <select 
          v-model="statusFilter" 
          @change="handleStatusFilter"
          class="px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent bg-white text-gray-700 font-medium"
        >
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="on_leave">On Leave</option>
          <option value="terminated">Terminated</option>
        </select>
        <select 
          v-model="departmentFilter" 
          @change="handleDepartmentFilter"
          class="px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent bg-white text-gray-700 font-medium"
        >
          <option value="">All Departments</option>
          <option v-for="dept in departments" :key="dept.id" :value="dept.id">
            {{ dept.name }}
          </option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="text-gray-600">Loading employees...</div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
      {{ error }}
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-100 border-b border-gray-300">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Employee Code</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Department</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Designation</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="employee in employees" :key="employee.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-semibold text-gray-900">{{ employee.employee_code }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-semibold text-gray-900">{{ employee.full_name }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-gray-600">{{ employee.department?.name || 'N/A' }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-gray-600">{{ employee.designation?.title || 'N/A' }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-gray-600">{{ employee.user?.email }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span :class="{
                  'inline-flex px-2.5 py-1 text-xs font-semibold rounded-full': true,
                  'bg-green-100 text-green-700': employee.employment_status === 'active',
                  'bg-amber-100 text-amber-700': employee.employment_status === 'on_leave',
                  'bg-red-100 text-red-700': employee.employment_status === 'terminated',
                }">
                  {{ employee.employment_status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="flex items-center justify-center space-x-2">
                  <router-link 
                    :to="`/employees/${employee.id}`" 
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                    title="View"
                  >
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </router-link>
                  <router-link 
                    v-if="can('employees.update')"
                    :to="`/employees/${employee.id}/edit`" 
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                    title="Edit"
                  >
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </router-link>
                  <button 
                    v-if="can('employees.delete')"
                    @click="handleDelete(employee.id)" 
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                    title="Delete"
                  >
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination" class="px-6 py-4 bg-gray-100 border-t border-gray-300 flex items-center justify-between">
        <div class="text-sm text-gray-600">
          Showing <span class="font-semibold text-gray-900">{{ ((pagination.current_page - 1) * pagination.per_page) + 1 }}</span> to 
          <span class="font-semibold text-gray-900">{{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}</span> of 
          <span class="font-semibold text-gray-900">{{ pagination.total }}</span> employees
        </div>
        <div class="flex items-center space-x-2">
          <button
            @click="loadPage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :class="pagination.current_page === 1 ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'"
          >
            Previous
          </button>
          <div class="flex items-center space-x-1">
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="loadPage(page)"
              class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
              :class="page === pagination.current_page 
                ? 'bg-gray-900 text-white' 
                : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-100'"
            >
              {{ page }}
            </button>
          </div>
          <button
            @click="loadPage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-4 py-2 text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :class="pagination.current_page === pagination.last_page ? 'bg-gray-300 text-gray-500' : 'bg-gray-900 text-white hover:bg-gray-800'"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useEmployeeStore } from '@/stores/employee';
import { usePermissions } from '@/composables/usePermissions';
import axios from 'axios';

const { can } = usePermissions();

const employeeStore = useEmployeeStore();

const searchQuery = ref('');
const departmentFilter = ref('');
const statusFilter = ref('');
const departments = ref([]);
const employees = computed(() => employeeStore.employees);
const loading = computed(() => employeeStore.loading);
const error = computed(() => employeeStore.error);
const pagination = computed(() => employeeStore.pagination);

const visiblePages = computed(() => {
  if (!pagination.value) return [];
  
  const current = pagination.value.current_page;
  const last = pagination.value.last_page;
  const pages = [];
  
  // Show max 5 pages
  let start = Math.max(1, current - 2);
  let end = Math.min(last, current + 2);
  
  // Adjust if at the beginning or end
  if (current <= 3) {
    end = Math.min(5, last);
  } else if (current >= last - 2) {
    start = Math.max(1, last - 4);
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
});

const loadEmployees = async (params = {}) => {
  await employeeStore.fetchEmployees(params);
};

const fetchDepartments = async () => {
  try {
    const response = await axios.get('/departments');
    departments.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch departments:', error);
    // Silently fail - filter will just be empty
  }
};

const handleSearch = () => {
  const params = { search: searchQuery.value };
  if (departmentFilter.value) {
    params.department_id = departmentFilter.value;
  }
  if (statusFilter.value) {
    params.employment_status = statusFilter.value;
  }
  loadEmployees(params);
};

const handleDepartmentFilter = () => {
  const params = { search: searchQuery.value };
  if (departmentFilter.value) {
    params.department_id = departmentFilter.value;
  }
  if (statusFilter.value) {
    params.employment_status = statusFilter.value;
  }
  loadEmployees(params);
};

const handleStatusFilter = () => {
  const params = { search: searchQuery.value };
  if (departmentFilter.value) {
    params.department_id = departmentFilter.value;
  }
  if (statusFilter.value) {
    params.employment_status = statusFilter.value;
  }
  loadEmployees(params);
};

const loadPage = (page) => {
  const params = { page, search: searchQuery.value };
  if (departmentFilter.value) {
    params.department_id = departmentFilter.value;
  }
  if (statusFilter.value) {
    params.employment_status = statusFilter.value;
  }
  loadEmployees(params);
};

const handleDelete = async (id) => {
  if (confirm('Are you sure you want to delete this employee?')) {
    try {
      await employeeStore.deleteEmployee(id);
      const params = { search: searchQuery.value };
      if (departmentFilter.value) {
        params.department_id = departmentFilter.value;
      }
      if (statusFilter.value) {
        params.employment_status = statusFilter.value;
      }
      loadEmployees(params);
    } catch (err) {
      alert('Failed to delete employee');
    }
  }
};

onMounted(() => {
  loadEmployees();
  fetchDepartments();
});
</script>
