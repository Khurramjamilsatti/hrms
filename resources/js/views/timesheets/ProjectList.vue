<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-lg font-semibold">Projects</h3>
      <button @click="showForm = true" class="btn btn-primary">
        Create Project
      </button>
    </div>

    <!-- Projects Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Manager</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="project in projects" :key="project.id">
            <td class="px-6 py-4 text-sm font-medium">{{ project.name }}</td>
            <td class="px-6 py-4 text-sm">{{ project.client_name || '-' }}</td>
            <td class="px-6 py-4 text-sm">{{ project.manager?.name }}</td>
            <td class="px-6 py-4 text-sm">
              {{ formatDate(project.start_date) }} - {{ project.end_date ? formatDate(project.end_date) : 'Ongoing' }}
            </td>
            <td class="px-6 py-4 text-sm">{{ formatCurrency(project.budget) }}</td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs rounded-full" :class="getStatusClass(project.status)">
                {{ project.status }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm">
              <button @click="editProject(project)" class="text-primary-600 hover:text-primary-900 mr-3">Edit</button>
              <button @click="viewTasks(project)" class="text-green-600 hover:text-green-900">Tasks</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Project Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">{{ editingProject ? 'Edit' : 'Create' }} Project</h3>
        
        <form @submit.prevent="saveProject">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
              <input type="text" v-model="form.name" required class="form-input" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Client Name</label>
              <input type="text" v-model="form.client_name" class="form-input" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Project Manager</label>
              <select v-model="form.manager_id" required class="form-input">
                <option value="">Select Manager</option>
                <option v-for="manager in managers" :key="manager.id" :value="manager.id">
                  {{ manager.name }}
                </option>
              </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" v-model="form.start_date" required class="form-input" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date (Optional)</label>
                <input type="date" v-model="form.end_date" class="form-input" />
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Budget (PKR)</label>
              <input type="number" v-model="form.budget" step="0.01" class="form-input" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
              <textarea v-model="form.description" rows="3" class="form-input"></textarea>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select v-model="form.status" required class="form-input">
                <option value="planning">Planning</option>
                <option value="active">Active</option>
                <option value="on_hold">On Hold</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>
          
          <div class="flex justify-end space-x-3 mt-6">
            <button type="button" @click="closeForm" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const projects = ref([]);
const managers = ref([]);
const showForm = ref(false);
const editingProject = ref(null);

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

const fetchProjects = async () => {
  try {
    const response = await axios.get('/api/timesheets/projects');
    projects.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch projects:', error);
  }
};

const fetchManagers = async () => {
  try {
    const response = await axios.get('/api/employees?role=manager,admin');
    managers.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch managers:', error);
  }
};

const saveProject = async () => {
  try {
    if (editingProject.value) {
      await axios.put(`/api/timesheets/projects/${editingProject.value.id}`, form);
    } else {
      await axios.post('/api/timesheets/projects', form);
    }
    
    closeForm();
    fetchProjects();
  } catch (error) {
    console.error('Failed to save project:', error);
    alert('Error saving project');
  }
};

const editProject = (project) => {
  editingProject.value = project;
  Object.assign(form, {
    name: project.name,
    client_name: project.client_name || '',
    manager_id: project.manager_id,
    start_date: project.start_date,
    end_date: project.end_date || '',
    budget: project.budget || '',
    description: project.description || '',
    status: project.status
  });
  showForm.value = true;
};

const viewTasks = (project) => {
  alert(`View tasks for: ${project.name}\n\n(Tasks management UI to be implemented)`);
};

const closeForm = () => {
  showForm.value = false;
  editingProject.value = null;
  Object.assign(form, {
    name: '',
    client_name: '',
    manager_id: '',
    start_date: '',
    end_date: '',
    budget: '',
    description: '',
    status: 'planning'
  });
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PK');
};

const formatCurrency = (amount) => {
  return amount ? `Rs. ${parseFloat(amount).toLocaleString('en-PK')}` : '-';
};

const getStatusClass = (status) => {
  const classes = {
    planning: 'bg-blue-100 text-blue-800',
    active: 'bg-green-100 text-green-800',
    on_hold: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-gray-100 text-gray-800',
    cancelled: 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  fetchProjects();
  fetchManagers();
});
</script>
