<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Designations</h1>
      <button @click="openCreateModal" class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Add Designation
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Designations</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ designations.length }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Departments Linked</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ linkedDepartments }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Levels</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ uniqueLevels }}</h3>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
      <p class="font-medium">{{ error }}</p>
      <button @click="loadDesignations" class="mt-2 text-sm underline">Try again</button>
    </div>

    <div v-else-if="designations.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Designations</h3>
      <p class="text-gray-500">Click "Add Designation" to create one.</p>
    </div>

    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Level</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Department</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="desig in designations" :key="desig.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ desig.title }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ desig.level ?? '—' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ desig.department?.name || '—' }}</td>
            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ desig.description || '—' }}</td>
            <td class="px-6 py-4 text-sm space-x-2">
              <button @click="openEditModal(desig)" class="text-gray-700 hover:text-gray-900 font-medium">Edit</button>
              <button @click="openDeleteModal(desig)" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editing ? 'Edit Designation' : 'Add Designation' }}</h3>
          <button @click="showModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
            <input v-model="form.title" type="text" placeholder="e.g. Software Engineer" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Level</label>
            <input v-model="form.level" type="number" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Department</label>
            <select v-model="form.department_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
              <option :value="null">None</option>
              <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea v-model="form.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"></textarea>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveDesignation" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50">{{ saving ? 'Saving...' : (editing ? 'Update' : 'Create') }}</button>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
        <div class="px-6 py-5 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
          </div>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Designation</h3>
          <p class="text-sm text-gray-600">Are you sure you want to delete <span class="font-semibold">{{ deleting?.title }}</span>?</p>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="deleteDesignation" :disabled="deleting_busy" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">{{ deleting_busy ? 'Deleting...' : 'Delete' }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useDialog } from '@/composables/useDialog';

const { alert } = useDialog();
const designations = ref([]);
const departments = ref([]);
const loading = ref(false);
const error = ref(null);
const showModal = ref(false);
const showDeleteModal = ref(false);
const editing = ref(null);
const deleting = ref(null);
const saving = ref(false);
const deleting_busy = ref(false);
const formError = ref(null);

const form = ref({ title: '', description: '', level: 1, department_id: null });

const linkedDepartments = computed(() => new Set(designations.value.filter(d => d.department_id).map(d => d.department_id)).size);
const uniqueLevels = computed(() => new Set(designations.value.map(d => d.level).filter(l => l != null)).size);

const loadDesignations = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/designations');
    designations.value = Array.isArray(response.data) ? response.data : (response.data.data || []);
  } catch (err) {
    error.value = 'Failed to load designations';
  } finally {
    loading.value = false;
  }
};

const loadDepartments = async () => {
  try {
    const response = await axios.get('/departments');
    departments.value = Array.isArray(response.data) ? response.data : (response.data.data || []);
  } catch (err) {
    console.error('Failed to load departments:', err);
  }
};

const openCreateModal = () => {
  editing.value = null;
  form.value = { title: '', description: '', level: 1, department_id: null };
  formError.value = null;
  showModal.value = true;
};

const openEditModal = (desig) => {
  editing.value = desig;
  form.value = {
    title: desig.title,
    description: desig.description || '',
    level: desig.level ?? 1,
    department_id: desig.department_id ?? null
  };
  formError.value = null;
  showModal.value = true;
};

const saveDesignation = async () => {
  formError.value = null;
  if (!form.value.title.trim()) {
    formError.value = 'Title is required';
    return;
  }
  saving.value = true;
  try {
    if (editing.value) {
      await axios.put(`/designations/${editing.value.id}`, form.value);
    } else {
      await axios.post('/designations', form.value);
    }
    showModal.value = false;
    loadDesignations();
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to save designation';
  } finally {
    saving.value = false;
  }
};

const openDeleteModal = (desig) => {
  deleting.value = desig;
  showDeleteModal.value = true;
};

const deleteDesignation = async () => {
  deleting_busy.value = true;
  try {
    await axios.delete(`/designations/${deleting.value.id}`);
    showDeleteModal.value = false;
    loadDesignations();
  } catch (err) {
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Failed to delete designation',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  } finally {
    deleting_busy.value = false;
  }
};

onMounted(() => {
  loadDesignations();
  loadDepartments();
});
</script>
