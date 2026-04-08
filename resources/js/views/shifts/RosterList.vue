<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-lg font-semibold">Shift Rosters</h3>
      <button @click="showForm = true" class="btn btn-primary">Create Roster</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="roster in rosters" :key="roster.id" class="bg-white rounded-lg shadow p-6">
        <h4 class="font-semibold text-lg mb-2">{{ roster.name }}</h4>
        <div class="space-y-2 text-sm mb-4">
          <div><span class="text-gray-600">Department:</span> {{ roster.department?.name }}</div>
          <div><span class="text-gray-600">Period:</span> {{ formatDate(roster.start_date) }} - {{ formatDate(roster.end_date) }}</div>
          <div><span class="text-gray-600">Status:</span> 
            <span class="px-2 py-1 text-xs rounded-full" :class="roster.is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
              {{ roster.is_published ? 'Published' : 'Draft' }}
            </span>
          </div>
        </div>
        
        <div class="flex space-x-2">
          <button @click="editRoster(roster)" class="btn btn-sm btn-primary">Edit</button>
          <button v-if="!roster.is_published" @click="publishRoster(roster.id)" class="btn btn-sm btn-secondary">Publish</button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Roster Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">{{ editingRoster ? 'Edit' : 'Create' }} Roster</h3>
        
        <form @submit.prevent="saveRoster">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Roster Name</label>
              <input type="text" v-model="form.name" required class="form-input" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
              <select v-model="form.department_id" required class="form-input">
                <option value="">Select Department</option>
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                  {{ dept.name }}
                </option>
              </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" v-model="form.start_date" required class="form-input" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" v-model="form.end_date" required class="form-input" />
              </div>
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

const rosters = ref([]);
const departments = ref([]);
const showForm = ref(false);
const editingRoster = ref(null);

const form = reactive({
  name: '',
  department_id: '',
  start_date: '',
  end_date: ''
});

const fetchRosters = async () => {
  try {
    const response = await axios.get('/api/shift-scheduling/rosters');
    rosters.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch rosters:', error);
  }
};

const fetchDepartments = async () => {
  try {
    const response = await axios.get('/api/departments');
    departments.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch departments:', error);
  }
};

const saveRoster = async () => {
  try {
    if (editingRoster.value) {
      await axios.put(`/api/shift-scheduling/rosters/${editingRoster.value.id}`, form);
    } else {
      await axios.post('/api/shift-scheduling/rosters', form);
    }
    
    closeForm();
    fetchRosters();
  } catch (error) {
    console.error('Failed to save roster:', error);
    alert('Error saving roster');
  }
};

const publishRoster = async (id) => {
  if (confirm('Publish this roster? Employees will be able to view their shifts.')) {
    try {
      await axios.post(`/api/shift-scheduling/rosters/${id}/publish`);
      fetchRosters();
    } catch (error) {
      console.error('Failed to publish roster:', error);
    }
  }
};

const editRoster = (roster) => {
  editingRoster.value = roster;
  Object.assign(form, {
    name: roster.name,
    department_id: roster.department_id,
    start_date: roster.start_date,
    end_date: roster.end_date
  });
  showForm.value = true;
};

const closeForm = () => {
  showForm.value = false;
  editingRoster.value = null;
  Object.assign(form, {
    name: '',
    department_id: '',
    start_date: '',
    end_date: ''
  });
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PK');
};

onMounted(() => {
  fetchRosters();
  fetchDepartments();
});
</script>
