<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Onboarding Templates</h1>
        <p class="text-sm text-gray-500 mt-1">Manage reusable onboarding checklists for different roles and departments</p>
      </div>
      <div class="flex items-center space-x-3">
        <button @click="$router.push('/onboarding')" class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-colors shadow-sm">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
          Back to Onboarding
        </button>
        <button @click="openCreateModal" class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          Create Template
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Templates</p>
            <h3 class="text-2xl font-bold text-gray-900">{{ templates.length }}</h3>
          </div>
          <div class="bg-gray-100 rounded-lg p-3">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Active Templates</p>
            <h3 class="text-2xl font-bold text-green-600">{{ activeCount }}</h3>
          </div>
          <div class="bg-green-50 rounded-lg p-3">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Tasks</p>
            <h3 class="text-2xl font-bold text-blue-600">{{ totalTasks }}</h3>
          </div>
          <div class="bg-blue-50 rounded-lg p-3">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Avg Duration</p>
            <h3 class="text-2xl font-bold text-purple-600">{{ avgDuration }} days</h3>
          </div>
          <div class="bg-purple-50 rounded-lg p-3">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="templates.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Templates Yet</h3>
      <p class="text-gray-500">Create your first onboarding template to get started.</p>
    </div>

    <!-- Templates Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="tmpl in templates" :key="tmpl.id"
        class="bg-white rounded-lg shadow border border-gray-200 hover:shadow-lg transition-shadow overflow-hidden">
        <!-- Header -->
        <div class="p-5 border-b border-gray-200">
          <div class="flex items-start justify-between mb-2">
            <h3 class="text-lg font-bold text-gray-900">{{ tmpl.name }}</h3>
            <span v-if="tmpl.is_active" class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Active</span>
            <span v-else class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Inactive</span>
          </div>
          <p class="text-sm text-gray-600 line-clamp-2">{{ tmpl.description || 'No description' }}</p>
        </div>

        <!-- Info -->
        <div class="p-5 bg-gray-50 space-y-2.5">
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500 flex items-center">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
              Department
            </span>
            <span class="font-medium text-gray-900">{{ tmpl.department?.name || 'All Departments' }}</span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500 flex items-center">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              Duration
            </span>
            <span class="font-medium text-gray-900">{{ tmpl.duration_days }} days</span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="text-gray-500 flex items-center">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
              Tasks
            </span>
            <span class="font-medium text-gray-900">{{ tmpl.tasks?.length || 0 }} tasks</span>
          </div>
        </div>

        <!-- Actions -->
        <div class="p-4 border-t border-gray-200 flex items-center justify-between gap-2">
          <button @click="viewTasks(tmpl)" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
            View Tasks
          </button>
          <button @click="editTemplate(tmpl)" class="flex-1 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            Edit
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Template Modal -->
    <div v-if="showFormModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingTemplate ? 'Edit' : 'Create' }} Template</h3>
          <button @click="showFormModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Template Name <span class="text-red-500">*</span></label>
            <input v-model="form.name" type="text" required placeholder="e.g., Software Engineer Onboarding" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea v-model="form.description" rows="3" placeholder="Describe this onboarding template..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"></textarea>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Department</label>
            <select v-model="form.department_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
              <option value="">All Departments</option>
              <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Duration (Days) <span class="text-red-500">*</span></label>
            <input v-model.number="form.duration_days" type="number" required min="1" max="365" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
          </div>
          <div class="flex items-center">
            <input v-model="form.is_active" type="checkbox" id="is_active" class="h-4 w-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900" />
            <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Active (can be used for new onboardings)</label>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showFormModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveTemplate" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50">
            {{ saving ? 'Saving...' : (editingTemplate ? 'Update' : 'Create') }}
          </button>
        </div>
      </div>
    </div>

    <!-- View Tasks Modal -->
    <div v-if="showTasksModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4 overflow-hidden max-h-[85vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center flex-shrink-0">
          <div>
            <h3 class="text-lg font-bold text-gray-900">{{ selectedTemplate?.name }} — Tasks</h3>
            <p class="text-sm text-gray-500">{{ selectedTemplate?.tasks?.length || 0 }} tasks over {{ selectedTemplate?.duration_days }} days</p>
          </div>
          <button @click="showTasksModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>

        <div class="overflow-y-auto flex-1">
          <!-- Add Task Form -->
          <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <button @click="showAddTaskForm = !showAddTaskForm" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
              <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
              {{ editingTask ? 'Edit Task' : 'Add New Task' }}
            </button>
            <div v-if="showAddTaskForm" class="mt-3 space-y-3">
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1">Task Title <span class="text-red-500">*</span></label>
                  <input v-model="taskForm.title" type="text" required placeholder="e.g., IT Setup" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1">Day Number <span class="text-red-500">*</span></label>
                  <input v-model.number="taskForm.day_number" type="number" required min="1" :max="selectedTemplate?.duration_days" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
                </div>
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Description</label>
                <input v-model="taskForm.description" type="text" placeholder="What needs to be done?" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Task Type <span class="text-red-500">*</span></label>
                <select v-model="taskForm.task_type" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
                  <option value="document">Document</option>
                  <option value="training">Training</option>
                  <option value="meeting">Meeting</option>
                  <option value="system_access">System Access</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="flex items-center space-x-3">
                <input v-model="taskForm.is_mandatory" type="checkbox" id="task_mandatory" class="h-4 w-4 text-gray-900 border-gray-300 rounded" />
                <label for="task_mandatory" class="text-xs font-medium text-gray-700">Mandatory task</label>
              </div>
              <div class="flex items-center justify-end space-x-2">
                <button @click="cancelAddTask" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md">Cancel</button>
                <button @click="saveTask" :disabled="savingTask" class="px-3 py-1.5 text-xs font-medium text-white bg-gray-900 hover:bg-gray-800 rounded-md disabled:opacity-50">
                  {{ savingTask ? 'Saving...' : (editingTask ? 'Update Task' : 'Add Task') }}
                </button>
              </div>
            </div>
          </div>

          <!-- Tasks List -->
          <div class="px-6 py-4">
            <div v-if="!selectedTemplate?.tasks || selectedTemplate.tasks.length === 0" class="text-sm text-gray-500 text-center py-8">
              No tasks yet. Add your first task above.
            </div>
            <div v-else class="space-y-2">
              <div v-for="task in sortedTemplateTasks" :key="task.id"
                class="border border-gray-200 rounded-lg p-3 flex items-start justify-between gap-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-start space-x-3 flex-1 min-w-0">
                  <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <span class="text-sm font-bold text-gray-700">D{{ task.day_number }}</span>
                  </div>
                  <div class="min-w-0">
                    <p class="font-semibold text-gray-900">{{ task.title }}</p>
                    <p v-if="task.description" class="text-sm text-gray-500 mt-0.5">{{ task.description }}</p>
                    <div class="flex items-center space-x-2 mt-1">
                      <span v-if="task.is_mandatory" class="text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded-full font-medium">Mandatory</span>
                      <span class="text-xs text-gray-400">Day {{ task.day_number }} of {{ selectedTemplate.duration_days }}</span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center space-x-1 flex-shrink-0">
                  <button @click="editTask(task)" class="px-2.5 py-1 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">Edit</button>
                  <button @click="deleteTask(task.id)" class="px-2.5 py-1 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors">Delete</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useNotification } from '@/composables/useNotification';

const router = useRouter();
const { success, error: showError } = useNotification();
const templates = ref([]);
const departments = ref([]);
const loading = ref(false);
const saving = ref(false);
const savingTask = ref(false);
const formError = ref(null);

const showFormModal = ref(false);
const showTasksModal = ref(false);
const showAddTaskForm = ref(false);
const editingTemplate = ref(null);
const editingTask = ref(null);
const selectedTemplate = ref(null);

const form = ref({
  name: '',
  description: '',
  department_id: '',
  duration_days: 30,
  is_active: true,
});

const taskForm = ref({
  title: '',
  description: '',
  day_number: 1,
  task_type: 'other',
  is_mandatory: true,
});

const activeCount = computed(() => templates.value.filter(t => t.is_active).length);
const totalTasks = computed(() => templates.value.reduce((sum, t) => sum + (t.tasks?.length || 0), 0));
const avgDuration = computed(() => {
  if (templates.value.length === 0) return 0;
  const total = templates.value.reduce((sum, t) => sum + (t.duration_days || 0), 0);
  return Math.round(total / templates.value.length);
});

const sortedTemplateTasks = computed(() => {
  if (!selectedTemplate.value?.tasks) return [];
  return [...selectedTemplate.value.tasks].sort((a, b) => a.day_number - b.day_number);
});

const fetchTemplates = async () => {
  loading.value = true;
  try {
    const res = await axios.get('/onboarding/templates');
    templates.value = res.data.data || res.data;
  } catch (err) {
    console.error('Failed to load templates:', err);
  } finally {
    loading.value = false;
  }
};

const fetchDepartments = async () => {
  try {
    const res = await axios.get('/departments');
    departments.value = res.data.data || res.data;
  } catch (err) {
    console.error('Failed to load departments:', err);
  }
};

const openCreateModal = () => {
  editingTemplate.value = null;
  formError.value = null;
  form.value = {
    name: '',
    description: '',
    department_id: '',
    duration_days: 30,
    is_active: true,
  };
  showFormModal.value = true;
};

const editTemplate = (tmpl) => {
  editingTemplate.value = tmpl;
  formError.value = null;
  form.value = {
    name: tmpl.name,
    description: tmpl.description || '',
    department_id: tmpl.department_id || '',
    duration_days: tmpl.duration_days,
    is_active: tmpl.is_active ?? true,
  };
  showFormModal.value = true;
};

const saveTemplate = async () => {
  formError.value = null;
  if (!form.value.name || !form.value.duration_days) {
    formError.value = 'Please fill in all required fields';
    return;
  }
  saving.value = true;
  try {
    const payload = { ...form.value };
    if (!payload.department_id) delete payload.department_id;
    if (!payload.description) delete payload.description;

    if (editingTemplate.value) {
      await axios.put(`/onboarding/templates/${editingTemplate.value.id}`, payload);
    } else {
      await axios.post('/onboarding/templates', payload);
    }
    showFormModal.value = false;
    fetchTemplates();
    success(editingTemplate.value ? 'Template updated successfully' : 'Template created successfully');
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to save template';
  } finally {
    saving.value = false;
  }
};

const viewTasks = async (tmpl) => {
  try {
    const res = await axios.get(`/onboarding/templates/${tmpl.id}`);
    selectedTemplate.value = res.data;
    showAddTaskForm.value = false;
    editingTask.value = null;
    taskForm.value = {
      title: '',
      description: '',
      day_number: 1,
      task_type: 'other',
      is_mandatory: true,
    };
    showTasksModal.value = true;
  } catch (err) {
    showError('Failed to load template details');
  }
};

const saveTask = async () => {
  if (!taskForm.value.title || !taskForm.value.day_number) {
    showError('Please fill in title and day number');
    return;
  }
  savingTask.value = true;
  try {
    const payload = {
      template_id: selectedTemplate.value.id,
      title: taskForm.value.title,
      description: taskForm.value.description || '',
      task_type: taskForm.value.task_type,
      day_number: taskForm.value.day_number,
      is_mandatory: taskForm.value.is_mandatory,
    };
    
    if (editingTask.value) {
      await axios.put(`/onboarding/template-tasks/${editingTask.value.id}`, payload);
      success('Task updated successfully');
    } else {
      await axios.post('/onboarding/template-tasks', payload);
      success('Task added successfully');
    }
    
    // Refresh template
    const res = await axios.get(`/onboarding/templates/${selectedTemplate.value.id}`);
    selectedTemplate.value = res.data;
    fetchTemplates();
    cancelAddTask();
  } catch (err) {
    showError(err.response?.data?.message || `Failed to ${editingTask.value ? 'update' : 'add'} task`);
  } finally {
    savingTask.value = false;
  }
};

const editTask = (task) => {
  editingTask.value = task;
  taskForm.value = {
    title: task.title,
    description: task.description || '',
    day_number: task.day_number,
    task_type: task.task_type,
    is_mandatory: task.is_mandatory ?? true,
  };
  showAddTaskForm.value = true;
};

const cancelAddTask = () => {
  showAddTaskForm.value = false;
  editingTask.value = null;
  taskForm.value = {
    title: '',
    description: '',
    task_type: 'other',
    day_number: 1,
    is_mandatory: true,
  };
};

const deleteTask = async (taskId) => {
  if (!confirm('Delete this task?')) return;
  try {
    await axios.delete(`/onboarding/template-tasks/${taskId}`);
    const res = await axios.get(`/onboarding/templates/${selectedTemplate.value.id}`);
    selectedTemplate.value = res.data;
    fetchTemplates();
    success('Task deleted successfully');
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to delete task');
  }
};

onMounted(() => {
  fetchTemplates();
  fetchDepartments();
});
</script>
