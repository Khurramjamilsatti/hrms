<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-lg font-semibold">Training Courses</h3>
      <button @click="showForm = true" class="btn btn-primary">Create Course</button>
    </div>

    <!-- Courses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="course in courses" :key="course.id" class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-start mb-2">
          <h4 class="font-semibold text-lg">{{ course.name }}</h4>
          <span class="px-2 py-1 text-xs rounded-full bg-primary-100 text-primary-800">
            {{ course.type }}
          </span>
        </div>
        
        <p class="text-sm text-gray-600 mb-4">{{ course.description }}</p>
        
        <div class="space-y-2 text-sm mb-4">
          <div><span class="text-gray-600">Duration:</span> {{ course.duration_hours }} hours</div>
          <div><span class="text-gray-600">Mode:</span> {{ course.delivery_mode }}</div>
          <div><span class="text-gray-600">Cost:</span> {{ formatCurrency(course.cost) }}</div>
        </div>
        
        <div class="flex space-x-2">
          <button @click="editCourse(course)" class="btn btn-sm btn-primary">Edit</button>
          <button @click="viewSessions(course)" class="btn btn-sm btn-secondary">Sessions</button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Course Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-screen overflow-y-auto">
        <h3 class="text-xl font-semibold mb-4">{{ editingCourse ? 'Edit' : 'Create' }} Course</h3>
        
        <form @submit.prevent="saveCourse">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Course Name</label>
              <input type="text" v-model="form.name" required class="form-input" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
              <textarea v-model="form.description" rows="3" required class="form-input"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select v-model="form.type" required class="form-input">
                  <option value="technical">Technical</option>
                  <option value="soft_skills">Soft Skills</option>
                  <option value="compliance">Compliance</option>
                  <option value="leadership">Leadership</option>
                  <option value="other">Other</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (Hours)</label>
                <input type="number" v-model="form.duration_hours" required class="form-input" min="1" />
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Mode</label>
                <select v-model="form.delivery_mode" required class="form-input">
                  <option value="online">Online</option>
                  <option value="in_person">In Person</option>
                  <option value="hybrid">Hybrid</option>
                </select>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cost (PKR)</label>
                <input type="number" v-model="form.cost" step="0.01" class="form-input" />
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

const courses = ref([]);
const showForm = ref(false);
const editingCourse = ref(null);

const form = reactive({
  name: '',
  description: '',
  type: 'technical',
  duration_hours: 8,
  delivery_mode: 'online',
  cost: ''
});

const fetchCourses = async () => {
  try {
    const response = await axios.get('/api/training/courses');
    courses.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch courses:', error);
  }
};

const saveCourse = async () => {
  try {
    if (editingCourse.value) {
      await axios.put(`/api/training/courses/${editingCourse.value.id}`, form);
    } else {
      await axios.post('/api/training/courses', form);
    }
    
    closeForm();
    fetchCourses();
  } catch (error) {
    console.error('Failed to save course:', error);
    alert('Error saving course');
  }
};

const editCourse = (course) => {
  editingCourse.value = course;
  Object.assign(form, {
    name: course.name,
    description: course.description,
    type: course.type,
    duration_hours: course.duration_hours,
    delivery_mode: course.delivery_mode,
    cost: course.cost || ''
  });
  showForm.value = true;
};

const viewSessions = (course) => {
  alert(`View sessions for: ${course.name}\n\n(Sessions UI to be implemented)`);
};

const closeForm = () => {
  showForm.value = false;
  editingCourse.value = null;
  Object.assign(form, {
    name: '',
    description: '',
    type: 'technical',
    duration_hours: 8,
    delivery_mode: 'online',
    cost: ''
  });
};

const formatCurrency = (amount) => {
  return amount ? `Rs. ${parseFloat(amount).toLocaleString('en-PK')}` : 'Free';
};

onMounted(() => {
  fetchCourses();
});
</script>
