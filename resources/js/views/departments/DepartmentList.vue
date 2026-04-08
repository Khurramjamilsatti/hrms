<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Departments</h1>
      <button v-if="can('departments.create')" @click="openCreateModal" class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Add Department
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Departments</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
          </div>
          <div class="bg-gray-100 rounded-lg p-3"><svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg></div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Active</p>
            <h3 class="text-2xl font-bold text-green-600">{{ stats.active }}</h3>
          </div>
          <div class="bg-green-50 rounded-lg p-3"><svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Inactive</p>
            <h3 class="text-2xl font-bold text-red-600">{{ stats.inactive }}</h3>
          </div>
          <div class="bg-red-50 rounded-lg p-3"><svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg></div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Employees</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ stats.totalEmployees }}</h3>
          </div>
          <div class="bg-gray-100 rounded-lg p-3"><svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg></div>
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
      <button @click="loadDepartments()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <!-- Empty -->
    <div v-else-if="departments.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Departments</h3>
      <p class="text-gray-500">Click "Add Department" to create one.</p>
    </div>

    <!-- Department Cards -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="dept in departments" :key="dept.id" class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
        <div class="p-5">
          <div class="flex items-start justify-between mb-3">
            <div>
              <h3 class="text-lg font-bold text-gray-900">{{ dept.name }}</h3>
              <p v-if="dept.parent" class="text-xs text-gray-500 mt-0.5">Under {{ dept.parent.name }}</p>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" :class="dept.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700'">{{ dept.is_active ? 'Active' : 'Inactive' }}</span>
          </div>

          <p class="text-sm text-gray-600 mb-4 line-clamp-2 min-h-[2.5rem]">{{ dept.description || 'No description provided.' }}</p>

          <div class="flex items-center justify-between pt-3 border-t border-gray-100">
            <div class="flex items-center text-sm text-gray-600">
              <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
              <span class="font-medium">{{ dept.employees_count || 0 }}</span>&nbsp;employees
            </div>
            <div v-if="dept.manager" class="flex items-center text-sm text-gray-600">
              <div class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center mr-1.5">
                <span class="text-[10px] font-bold text-gray-500">{{ getManagerInitials(dept.manager) }}</span>
              </div>
              <span class="text-xs">{{ getManagerName(dept.manager) }}</span>
            </div>
          </div>
        </div>

        <div v-if="can('departments.update') || can('departments.delete')" class="px-5 py-3 bg-gray-50 border-t border-gray-200 flex justify-end space-x-2">
          <button v-if="can('departments.update')" @click="openEditModal(dept)" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-200 rounded-md transition-colors" title="Edit">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
          </button>
          <button v-if="can('departments.delete')" @click="openDeleteModal(dept)" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Delete">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingDept ? 'Edit Department' : 'Add Department' }}</h3>
          <button @click="showModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Department Name</label>
            <input v-model="form.name" type="text" placeholder="e.g. Engineering" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea v-model="form.description" rows="3" placeholder="Department description..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"></textarea>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Parent Department</label>
            <select v-model="form.parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
              <option :value="null">None (Top-level)</option>
              <option v-for="d in departments.filter(d => d.id !== editingDept?.id)" :key="d.id" :value="d.id">{{ d.name }}</option>
            </select>
          </div>
          <div class="flex items-center space-x-3">
            <label class="relative inline-flex items-center cursor-pointer">
              <input v-model="form.is_active" type="checkbox" class="sr-only peer" />
              <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-gray-900 peer-focus:ring-2 peer-focus:ring-gray-300 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
            </label>
            <span class="text-sm font-medium text-gray-700">Active</span>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveDepartment" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50">{{ saving ? 'Saving...' : (editingDept ? 'Update' : 'Create') }}</button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
        <div class="px-6 py-5 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4"><svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg></div>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Department</h3>
          <p class="text-sm text-gray-600">Are you sure you want to delete <span class="font-semibold">{{ deletingDept?.name }}</span>? This action cannot be undone.</p>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="deleteDepartment" :disabled="deleting" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">{{ deleting ? 'Deleting...' : 'Delete' }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePermissions } from '@/composables/usePermissions';
import axios from 'axios';

const { can } = usePermissions();

const departments = ref([]);
const loading = ref(false);
const error = ref(null);
const showModal = ref(false);
const showDeleteModal = ref(false);
const editingDept = ref(null);
const deletingDept = ref(null);
const saving = ref(false);
const deleting = ref(false);
const formError = ref(null);

const form = ref({ name: '', description: '', parent_id: null, is_active: true });

const stats = computed(() => {
  const list = departments.value || [];
  return {
    total: list.length,
    active: list.filter(d => d.is_active).length,
    inactive: list.filter(d => !d.is_active).length,
    totalEmployees: list.reduce((sum, d) => sum + (d.employees_count || 0), 0)
  };
});

const loadDepartments = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/departments');
    departments.value = Array.isArray(response.data) ? response.data : (response.data.data || []);
  } catch (err) {
    error.value = 'Failed to load departments';
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  editingDept.value = null;
  form.value = { name: '', description: '', parent_id: null, is_active: true };
  formError.value = null;
  showModal.value = true;
};

const openEditModal = (dept) => {
  editingDept.value = dept;
  form.value = { name: dept.name, description: dept.description || '', parent_id: dept.parent_id, is_active: !!dept.is_active };
  formError.value = null;
  showModal.value = true;
};

const saveDepartment = async () => {
  formError.value = null;
  if (!form.value.name.trim()) { formError.value = 'Department name is required'; return; }
  saving.value = true;
  try {
    if (editingDept.value) {
      await axios.put(`/departments/${editingDept.value.id}`, form.value);
    } else {
      await axios.post('/departments', form.value);
    }
    showModal.value = false;
    loadDepartments();
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to save department';
  } finally { saving.value = false; }
};

const openDeleteModal = (dept) => { deletingDept.value = dept; showDeleteModal.value = true; };

const deleteDepartment = async () => {
  deleting.value = true;
  try {
    await axios.delete(`/departments/${deletingDept.value.id}`);
    showDeleteModal.value = false;
    loadDepartments();
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to delete department');
  } finally { deleting.value = false; }
};

const getManagerName = (mgr) => mgr?.name || `${mgr?.first_name || ''} ${mgr?.last_name || ''}`.trim() || 'N/A';
const getManagerInitials = (mgr) => getManagerName(mgr).split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

onMounted(() => { loadDepartments(); });
</script>
