<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Training Courses</h1>
      <button v-if="can('training.create')" @click="openCreateModal" class="inline-flex items-center px-5 py-2.5 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors shadow">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Create Course
      </button>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <div v-else-if="courses.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Courses</h3>
      <p class="text-gray-500">Click "Create Course" to add one.</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div v-for="course in courses" :key="course.id" class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex justify-between items-start mb-2">
          <h3 class="text-lg font-bold text-gray-900">{{ course.name }}</h3>
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">{{ course.type }}</span>
        </div>
        <p class="text-sm text-gray-600 mb-4 line-clamp-2 min-h-[2.5rem]">{{ course.description || 'No description.' }}</p>
        <div class="space-y-1 text-sm text-gray-600 mb-4">
          <div><span class="font-medium text-gray-700">Duration:</span> {{ course.duration_hours }} hours</div>
          <div><span class="font-medium text-gray-700">Mode:</span> {{ course.delivery_mode }}</div>
          <div><span class="font-medium text-gray-700">Cost:</span> {{ formatCurrency(course.cost) }}</div>
        </div>
        <div class="flex space-x-2 pt-3 border-t border-gray-100">
          <button v-if="can('training.update')" @click="editCourse(course)" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">Edit</button>
          <button @click="openSessions(course)" class="px-3 py-1.5 text-sm font-medium text-white bg-accent hover:bg-accent-dark rounded-lg">Sessions</button>
        </div>
      </div>
    </div>

    <!-- Course Modal -->
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingCourse ? 'Edit' : 'Create' }} Course</h3>
          <button @click="closeForm" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        <form @submit.prevent="saveCourse">
          <div class="px-6 py-5 space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Course Name</label>
              <input v-model="form.name" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
              <textarea v-model="form.description" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Type</label>
                <select v-model="form.type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                  <option value="technical">Technical</option>
                  <option value="soft_skills">Soft Skills</option>
                  <option value="compliance">Compliance</option>
                  <option value="leadership">Leadership</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Duration (Hours)</label>
                <input v-model="form.duration_hours" type="number" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Delivery Mode</label>
                <select v-model="form.delivery_mode" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                  <option value="online">Online</option>
                  <option value="in_person">In Person</option>
                  <option value="hybrid">Hybrid</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Cost (PKR)</label>
                <input v-model="form.cost" type="number" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
              </div>
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

    <!-- Sessions Modal -->
    <div v-if="showSessions" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl mx-4 overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <div>
            <h3 class="text-lg font-bold text-gray-900">Sessions</h3>
            <p class="text-sm text-gray-500">{{ selectedCourse?.name }}</p>
          </div>
          <button @click="closeSessions" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        <div class="px-6 py-4 border-b border-gray-100 flex justify-end">
          <button v-if="can('training.create')" @click="openSessionForm()" class="inline-flex items-center px-4 py-2 bg-accent hover:bg-accent-dark text-white text-sm font-medium rounded-lg">
            Add Session
          </button>
        </div>
        <div class="px-6 py-4 overflow-y-auto flex-1">
          <div v-if="sessionsLoading" class="flex justify-center py-10">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
          </div>
          <div v-else-if="sessions.length === 0" class="text-center py-10 text-gray-500">No sessions yet.</div>
          <div v-else class="space-y-3">
            <div v-for="session in sessions" :key="session.id" class="border border-gray-200 rounded-lg p-4 flex justify-between items-start">
              <div>
                <h4 class="font-semibold text-gray-900">{{ session.session_name }}</h4>
                <p class="text-sm text-gray-600 mt-1">{{ formatDate(session.start_date) }} – {{ formatDate(session.end_date) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ session.location || session.meeting_link || 'No location' }} · {{ session.available_seats || '—' }} seats</p>
              </div>
              <div class="flex items-center space-x-2">
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize" :class="sessionStatusClass(session.status)">{{ session.status }}</span>
                <button v-if="can('training.update')" @click="openSessionForm(session)" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Session Form Modal -->
    <div v-if="showSessionForm" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingSession ? 'Edit' : 'Add' }} Session</h3>
          <button @click="showSessionForm = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
        </div>
        <form @submit.prevent="saveSession">
          <div class="px-6 py-5 space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Session Name</label>
              <input v-model="sessionForm.session_name" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date</label>
                <input v-model="sessionForm.start_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">End Date</label>
                <input v-model="sessionForm.end_date" type="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Start Time</label>
                <input v-model="sessionForm.start_time" type="time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">End Time</label>
                <input v-model="sessionForm.end_time" type="time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Location</label>
              <input v-model="sessionForm.location" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Meeting Link</label>
              <input v-model="sessionForm.meeting_link" type="url" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Available Seats</label>
                <input v-model="sessionForm.available_seats" type="number" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select v-model="sessionForm.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                  <option value="scheduled">Scheduled</option>
                  <option value="ongoing">Ongoing</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
            </div>
            <div v-if="sessionError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ sessionError }}</div>
          </div>
          <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
            <button type="button" @click="showSessionForm = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
            <button type="submit" :disabled="sessionSaving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">{{ sessionSaving ? 'Saving...' : 'Save' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { usePermissions } from '@/composables/usePermissions';

const { can } = usePermissions();
const courses = ref([]);
const loading = ref(false);
const showForm = ref(false);
const editingCourse = ref(null);
const saving = ref(false);
const formError = ref(null);

const showSessions = ref(false);
const selectedCourse = ref(null);
const sessions = ref([]);
const sessionsLoading = ref(false);
const showSessionForm = ref(false);
const editingSession = ref(null);
const sessionSaving = ref(false);
const sessionError = ref(null);

const form = reactive({
  name: '',
  description: '',
  type: 'technical',
  duration_hours: 8,
  delivery_mode: 'online',
  cost: ''
});

const sessionForm = reactive({
  session_name: '',
  start_date: '',
  end_date: '',
  start_time: '',
  end_time: '',
  location: '',
  meeting_link: '',
  available_seats: 20,
  status: 'scheduled'
});

const fetchCourses = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/training/courses');
    courses.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch courses:', error);
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  editingCourse.value = null;
  formError.value = null;
  Object.assign(form, { name: '', description: '', type: 'technical', duration_hours: 8, delivery_mode: 'online', cost: '' });
  showForm.value = true;
};

const saveCourse = async () => {
  formError.value = null;
  saving.value = true;
  try {
    if (editingCourse.value) {
      await axios.put(`/training/courses/${editingCourse.value.id}`, form);
    } else {
      await axios.post('/training/courses', form);
    }
    closeForm();
    fetchCourses();
  } catch (error) {
    formError.value = error.response?.data?.message || 'Failed to save course';
  } finally {
    saving.value = false;
  }
};

const editCourse = (course) => {
  editingCourse.value = course;
  formError.value = null;
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

const closeForm = () => {
  showForm.value = false;
  editingCourse.value = null;
};

const openSessions = async (course) => {
  selectedCourse.value = course;
  showSessions.value = true;
  await fetchSessions();
};

const closeSessions = () => {
  showSessions.value = false;
  selectedCourse.value = null;
  sessions.value = [];
};

const fetchSessions = async () => {
  if (!selectedCourse.value) return;
  sessionsLoading.value = true;
  try {
    const response = await axios.get('/training/sessions', { params: { course_id: selectedCourse.value.id } });
    sessions.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch sessions:', error);
  } finally {
    sessionsLoading.value = false;
  }
};

const openSessionForm = (session = null) => {
  editingSession.value = session;
  sessionError.value = null;
  if (session) {
    Object.assign(sessionForm, {
      session_name: session.session_name,
      start_date: session.start_date?.substring(0, 10) || '',
      end_date: session.end_date?.substring(0, 10) || '',
      start_time: session.start_time || '',
      end_time: session.end_time || '',
      location: session.location || '',
      meeting_link: session.meeting_link || '',
      available_seats: session.available_seats || 20,
      status: session.status || 'scheduled'
    });
  } else {
    Object.assign(sessionForm, {
      session_name: '',
      start_date: '',
      end_date: '',
      start_time: '',
      end_time: '',
      location: '',
      meeting_link: '',
      available_seats: 20,
      status: 'scheduled'
    });
  }
  showSessionForm.value = true;
};

const saveSession = async () => {
  sessionError.value = null;
  sessionSaving.value = true;
  try {
    const payload = { ...sessionForm, course_id: selectedCourse.value.id };
    if (editingSession.value) {
      await axios.put(`/training/sessions/${editingSession.value.id}`, payload);
    } else {
      await axios.post('/training/sessions', payload);
    }
    showSessionForm.value = false;
    fetchSessions();
  } catch (error) {
    sessionError.value = error.response?.data?.message || 'Failed to save session';
  } finally {
    sessionSaving.value = false;
  }
};

const formatCurrency = (amount) => amount ? `Rs. ${parseFloat(amount).toLocaleString('en-PK')}` : 'Free';
const formatDate = (date) => date ? new Date(date).toLocaleDateString('en-PK') : '—';
const sessionStatusClass = (status) => ({
  scheduled: 'bg-blue-100 text-blue-800',
  ongoing: 'bg-green-100 text-green-800',
  completed: 'bg-gray-100 text-gray-800',
  cancelled: 'bg-red-100 text-red-800'
}[status] || 'bg-gray-100 text-gray-800');

onMounted(fetchCourses);
</script>
