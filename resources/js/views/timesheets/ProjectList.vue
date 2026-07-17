<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Projects</h1>
      <button
        v-if="canManageProjects"
        @click="openCreateModal"
        class="inline-flex items-center px-5 py-2.5 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors shadow"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Create Project
      </button>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <div v-else-if="projects.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Projects</h3>
      <p class="text-gray-500">Click "Create Project" to add one.</p>
    </div>

    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Project Name</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Client</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Manager</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Duration</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Budget</th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
            <th v-if="canManageProjects" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="project in projects" :key="project.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ project.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ project.client_name || '—' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ project.manager?.name || '—' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700">
              {{ formatDate(project.start_date) }} – {{ project.end_date ? formatDate(project.end_date) : 'Ongoing' }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-700">{{ formatCurrency(project.budget) }}</td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs rounded-full font-semibold" :class="getStatusClass(project.status)">{{ project.status }}</span>
            </td>
            <td v-if="canManageProjects" class="px-6 py-4 text-sm space-x-2">
              <button @click="editProject(project)" class="text-gray-700 hover:text-gray-900 font-medium">Edit</button>
              <button @click="openTasks(project)" class="text-gray-900 hover:text-gray-700 font-semibold">Tasks</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Project Modal -->
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingProject ? 'Edit' : 'Create' }} Project</h3>
          <button @click="closeForm" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        <form @submit.prevent="saveProject">
          <div class="px-6 py-5 space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Project Name</label>
              <input v-model="form.name" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Client Name</label>
              <input v-model="form.client_name" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Project Manager</label>
              <select v-model="form.manager_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                <option value="">Select Manager</option>
                <option v-for="manager in managers" :key="manager.id" :value="manager.id">{{ manager.name || `${manager.first_name} ${manager.last_name}` }}</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date</label>
                <input v-model="form.start_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">End Date</label>
                <input v-model="form.end_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Budget (PKR)</label>
              <input v-model="form.budget" type="number" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
              <textarea v-model="form.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
              <select v-model="form.status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                <option value="planning">Planning</option>
                <option value="active">Active</option>
                <option value="on_hold">On Hold</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
          </div>
          <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
            <button type="button" @click="closeForm" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
            <button type="submit" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">{{ saving ? 'Saving...' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Tasks Modal -->
    <div v-if="showTasks" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4 overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <div>
            <h3 class="text-lg font-bold text-gray-900">Tasks</h3>
            <p class="text-sm text-gray-500">{{ selectedProject?.name }}</p>
          </div>
          <button @click="closeTasks" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        <div class="px-6 py-4 border-b border-gray-100 flex justify-end">
          <button @click="openTaskForm()" class="inline-flex items-center px-4 py-2 bg-accent hover:bg-accent-dark text-white text-sm font-medium rounded-lg">Add Task</button>
        </div>
        <div class="px-6 py-4 overflow-y-auto flex-1">
          <div v-if="tasksLoading" class="flex justify-center py-10">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
          </div>
          <div v-else-if="tasks.length === 0" class="text-center py-10 text-gray-500">No tasks yet.</div>
          <div v-else class="space-y-3">
            <div v-for="task in tasks" :key="task.id" class="border border-gray-200 rounded-lg p-4 flex justify-between items-start">
              <div>
                <h4 class="font-semibold text-gray-900">{{ task.title }}</h4>
                <p class="text-sm text-gray-600 mt-1">{{ task.description || 'No description' }}</p>
                <p class="text-xs text-gray-500 mt-1">
                  Due: {{ task.due_date ? formatDate(task.due_date) : '—' }}
                  · Assigned: {{ task.assigned_to?.name || task.assigned_to?.first_name || 'Unassigned' }}
                </p>
              </div>
              <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize" :class="taskStatusClass(task.status)">{{ task.status }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize bg-gray-100 text-gray-700">{{ task.priority }}</span>
                <button @click="openTaskForm(task)" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Task Form Modal -->
    <div v-if="showTaskForm" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingTask ? 'Edit' : 'Add' }} Task</h3>
          <button @click="showTaskForm = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        <form @submit.prevent="saveTask">
          <div class="px-6 py-5 space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
              <input v-model="taskForm.title" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
              <textarea v-model="taskForm.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Priority</label>
                <select v-model="taskForm.priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                  <option value="urgent">Urgent</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select v-model="taskForm.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                  <option value="todo">To Do</option>
                  <option value="in_progress">In Progress</option>
                  <option value="review">Review</option>
                  <option value="completed">Completed</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Due Date</label>
              <input v-model="taskForm.due_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div v-if="taskError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ taskError }}</div>
          </div>
          <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
            <button type="button" @click="showTaskForm = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
            <button type="submit" :disabled="taskSaving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">{{ taskSaving ? 'Saving...' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import { usePermissions } from '@/composables/usePermissions';

const { can } = usePermissions();
const canManageProjects = computed(() => can('timesheets.manage') || can('timesheets.projects'));

const projects = ref([]);
const managers = ref([]);
const loading = ref(false);
const showForm = ref(false);
const editingProject = ref(null);
const saving = ref(false);
const formError = ref(null);

const showTasks = ref(false);
const selectedProject = ref(null);
const tasks = ref([]);
const tasksLoading = ref(false);
const showTaskForm = ref(false);
const editingTask = ref(null);
const taskSaving = ref(false);
const taskError = ref(null);

const form = reactive({
  name: '',
  client_name: '',
  manager_id: '',
  start_date: '',
  end_date: '',
  budget: '',
  description: '',
  status: 'planning'
});

const taskForm = reactive({
  title: '',
  description: '',
  priority: 'medium',
  status: 'todo',
  due_date: ''
});

const fetchProjects = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/timesheets/projects');
    projects.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch projects:', error);
  } finally {
    loading.value = false;
  }
};

const fetchManagers = async () => {
  try {
    const response = await axios.get('/employees/dropdown');
    managers.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch managers:', error);
  }
};

const openCreateModal = () => {
  editingProject.value = null;
  formError.value = null;
  Object.assign(form, { name: '', client_name: '', manager_id: '', start_date: '', end_date: '', budget: '', description: '', status: 'planning' });
  showForm.value = true;
};

const saveProject = async () => {
  formError.value = null;
  saving.value = true;
  try {
    if (editingProject.value) {
      await axios.put(`/timesheets/projects/${editingProject.value.id}`, form);
    } else {
      await axios.post('/timesheets/projects', form);
    }
    closeForm();
    fetchProjects();
  } catch (error) {
    formError.value = error.response?.data?.message || 'Failed to save project';
  } finally {
    saving.value = false;
  }
};

const editProject = (project) => {
  editingProject.value = project;
  formError.value = null;
  Object.assign(form, {
    name: project.name,
    client_name: project.client_name || '',
    manager_id: project.manager_id,
    start_date: project.start_date?.substring?.(0, 10) || project.start_date,
    end_date: project.end_date?.substring?.(0, 10) || project.end_date || '',
    budget: project.budget || '',
    description: project.description || '',
    status: project.status
  });
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
  editingProject.value = null;
};

const openTasks = async (project) => {
  selectedProject.value = project;
  showTasks.value = true;
  await fetchTasks();
};

const closeTasks = () => {
  showTasks.value = false;
  selectedProject.value = null;
  tasks.value = [];
};

const fetchTasks = async () => {
  if (!selectedProject.value) return;
  tasksLoading.value = true;
  try {
    const response = await axios.get(`/timesheets/projects/${selectedProject.value.id}/tasks`);
    tasks.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch tasks:', error);
  } finally {
    tasksLoading.value = false;
  }
};

const openTaskForm = (task = null) => {
  editingTask.value = task;
  taskError.value = null;
  if (task) {
    Object.assign(taskForm, {
      title: task.title,
      description: task.description || '',
      priority: task.priority || 'medium',
      status: task.status || 'todo',
      due_date: task.due_date?.substring?.(0, 10) || task.due_date || ''
    });
  } else {
    Object.assign(taskForm, { title: '', description: '', priority: 'medium', status: 'todo', due_date: '' });
  }
  showTaskForm.value = true;
};

const saveTask = async () => {
  taskError.value = null;
  taskSaving.value = true;
  try {
    const payload = { ...taskForm, project_id: selectedProject.value.id };
    if (editingTask.value) {
      await axios.put(`/timesheets/tasks/${editingTask.value.id}`, payload);
    } else {
      await axios.post('/timesheets/tasks', payload);
    }
    showTaskForm.value = false;
    fetchTasks();
  } catch (error) {
    taskError.value = error.response?.data?.message || 'Failed to save task';
  } finally {
    taskSaving.value = false;
  }
};

const formatDate = (date) => date ? new Date(date).toLocaleDateString('en-PK') : '—';
const formatCurrency = (amount) => amount ? `Rs. ${parseFloat(amount).toLocaleString('en-PK')}` : '—';
const getStatusClass = (status) => ({
  planning: 'bg-blue-100 text-blue-800',
  active: 'bg-green-100 text-green-800',
  on_hold: 'bg-yellow-100 text-yellow-800',
  completed: 'bg-gray-100 text-gray-800',
  cancelled: 'bg-red-100 text-red-800'
}[status] || 'bg-gray-100 text-gray-800');
const taskStatusClass = (status) => ({
  todo: 'bg-gray-100 text-gray-800',
  in_progress: 'bg-blue-100 text-blue-800',
  review: 'bg-yellow-100 text-yellow-800',
  completed: 'bg-green-100 text-green-800'
}[status] || 'bg-gray-100 text-gray-800');

onMounted(() => {
  fetchProjects();
  fetchManagers();
});
</script>
