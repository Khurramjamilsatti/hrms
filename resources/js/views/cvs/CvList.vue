<template>
  <div class="p-6">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">CV Bank</h1>
      <p class="text-gray-600 mt-1">Manage employee CVs and resumes</p>
    </div>

    <!-- Actions -->
    <div class="mb-6 flex justify-between items-center">
      <div class="flex gap-4">
        <input
          v-model="searchQuery"
          @input="fetchCvs"
          type="text"
          placeholder="Search by employee name..."
          class="px-4 py-2 border border-gray-300 rounded-lg w-64"
        />
      </div>
      
      <button @click="showUploadModal = true" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
        </svg>
        Upload CV
      </button>
    </div>

    <!-- CVs Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Experience</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Skills</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="cv in cvs" :key="cv.id" class="hover:bg-gray-50" :class="{'bg-green-50': cv.is_current}">
            <td class="px-6 py-4 whitespace-nowrap">
              <div>
                <div class="text-sm font-medium text-gray-900">
                  {{ cv.employee?.first_name }} {{ cv.employee?.last_name }}
                </div>
                <div class="text-sm text-gray-500">{{ cv.employee?.email }}</div>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                </svg>
                {{ cv.file_name }}
                <span v-if="cv.is_current" class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Current</span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">v{{ cv.version }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ cv.experience_years }} years</td>
            <td class="px-6 py-4">
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="(skill, index) in (cv.skills || []).slice(0, 3)"
                  :key="index"
                  class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded"
                >
                  {{ skill }}
                </span>
                <span v-if="(cv.skills || []).length > 3" class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">
                  +{{ (cv.skills || []).length - 3 }}
                </span>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ formatDate(cv.uploaded_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
              <button @click="viewDetails(cv.id)" class="text-green-600 hover:text-green-900">View</button>
              <button @click="viewDetails(cv.id)" class="text-green-600 hover:text-green-900">View</button>
              <button @click="downloadCv(cv)" class="text-blue-600 hover:text-blue-900">Download</button>
              <button @click="viewHistory(cv)" class="text-purple-600 hover:text-purple-900">History</button>
              <button @click="deleteCv(cv)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-if="cvs.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
        </svg>
        <p class="mt-2 text-gray-500">No CVs found</p>
      </div>
    </div>
    
    <!-- Pagination -->
    <Pagination
      v-if="cvs.length > 0"
      :current-page="pagination.current_page"
      :total-pages="pagination.last_page"
      :total="pagination.total"
      :from="pagination.from"
      :to="pagination.to"
      @page-change="handlePageChange"
    />
    
    <!-- Pagination -->
    <Pagination
      v-if="cvs.length > 0"
      :current-page="pagination.current_page"
      :total-pages="pagination.last_page"
      :total="pagination.total"
      :from="pagination.from"
      :to="pagination.to"
      @page-change="handlePageChange"
    />

    <!-- Upload CV Modal -->
    <div v-if="showUploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showUploadModal = false">
      <div class="relative top-20 mx-auto p-8 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Upload Employee CV</h3>
        <form @submit.prevent="submitCvUpload" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employee*</label>
            <select v-model="cvForm.employee_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
              <option value="">Select Employee</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.first_name }} {{ emp.last_name }} - {{ emp.email }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">CV File* (PDF, DOC, DOCX - Max 5MB)</label>
            <input @change="handleFileChange" type="file" accept=".pdf,.doc,.docx" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Experience (Years)</label>
              <input v-model="cvForm.experience_years" type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Education Level</label>
              <select v-model="cvForm.education_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">Select Level</option>
                <option value="High School">High School</option>
                <option value="Bachelor">Bachelor's</option>
                <option value="Masters">Master's</option>
                <option value="PhD">PhD</option>
              </select>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Summary</label>
            <textarea v-model="cvForm.summary" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Skills (comma-separated)</label>
            <input v-model="cvForm.skillsText" type="text" placeholder="PHP, Laravel, Vue.js, PostgreSQL" class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Certifications (comma-separated)</label>
            <input v-model="cvForm.certificationsText" type="text" placeholder="AWS Certified, PMP" class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
          </div>
          
          <div class="flex justify-end gap-4 mt-6">
            <button type="button" @click="showUploadModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
            <button type="submit" :disabled="uploading" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              {{ uploading ? 'Uploading...' : 'Upload CV' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- History Modal -->
    <div v-if="showHistoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showHistoryModal = false">
      <div class="relative top-20 mx-auto p-8 border w-full max-w-3xl shadow-lg rounded-lg bg-white">
        <h3 class="text-lg font-bold text-gray-900 mb-4">CV Version History</h3>
        <div class="space-y-3">
          <div v-for="item in cvHistory" :key="item.id" class="flex items-center justify-between p-4 border rounded-lg" :class="{'bg-green-50 border-green-300': item.is_current}">
            <div>
              <div class="text-sm font-medium text-gray-900">Version {{ item.version }}</div>
              <div class="text-sm text-gray-600">{{ item.file_name }}</div>
              <div class="text-xs text-gray-500 mt-1">{{ formatDate(item.uploaded_at) }}</div>
            </div>
            <div class="flex items-center gap-2">
              <span v-if="item.is_current" class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Current</span>
              <button @click="downloadCv(item)" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Download</button>
            </div>
          </div>
        </div>
        <div class="flex justify-end mt-6">
          <button @click="showHistoryModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Pagination from '@/components/Pagination.vue';

const router = useRouter();
const cvs = ref([]);
const pagination = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0 });
const employees = ref([]);
const searchQuery = ref('');
const showUploadModal = ref(false);
const showHistoryModal = ref(false);
const cvHistory = ref([]);
const uploading = ref(false);

const cvForm = ref({
  employee_id: '',
  cv_file: null,
  summary: '',
  experience_years: '',
  education_level: '',
  skillsText: '',
  certificationsText: ''
});

const fetchCvs = async (page = 1) => {
  try {
    const params = new URLSearchParams();
    params.append('page', page);
    if (searchQuery.value) params.append('employee_name', searchQuery.value);
    
    const response = await axios.get(`/api/cvs?${params}`);
    cvs.value = response.data.data || response.data;
    
    // Update pagination data
    if (response.data.current_page) {
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        total: response.data.total,
        from: response.data.from || 0,
        to: response.data.to || 0
      };
    }
  } catch (error) {
    console.error('Failed to fetch CVs:', error);
  }
};

const handlePageChange = (page) => {
  fetchCvs(page);
};

const viewDetails = (cvId) => {
  router.push(`/cvs/${cvId}`);
};

const fetchEmployees = async () => {
  try {
    const response = await axios.get('/api/employees/dropdown');
    employees.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch employees:', error);
  }
};

const handleFileChange = (event) => {
  cvForm.value.cv_file = event.target.files[0];
};

const submitCvUpload = async () => {
  try {
    uploading.value = true;
    const formData = new FormData();
    formData.append('employee_id', cvForm.value.employee_id);
    formData.append('cv_file', cvForm.value.cv_file);
    if (cvForm.value.summary) formData.append('summary', cvForm.value.summary);
    if (cvForm.value.experience_years) formData.append('experience_years', cvForm.value.experience_years);
    if (cvForm.value.education_level) formData.append('education_level', cvForm.value.education_level);
    
    if (cvForm.value.skillsText) {
      const skills = cvForm.value.skillsText.split(',').map(s => s.trim()).filter(s => s);
      skills.forEach(skill => formData.append('skills[]', skill));
    }
    
    if (cvForm.value.certificationsText) {
      const certs = cvForm.value.certificationsText.split(',').map(s => s.trim()).filter(s => s);
      certs.forEach(cert => formData.append('certifications[]', cert));
    }
    
    await axios.post('/api/cvs', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    showUploadModal.value = false;
    cvForm.value = { employee_id: '', cv_file: null, summary: '', experience_years: '', education_level: '', skillsText: '', certificationsText: '' };
    fetchCvs();
    alert('CV uploaded successfully!');
  } catch (error) {
    console.error('Failed to upload CV:', error);
    alert('Failed to upload CV');
  } finally {
    uploading.value = false;
  }
};

const downloadCv = async (cv) => {
  try {
    const response = await axios.get(`/api/cvs/${cv.id}/download`, {
      responseType: 'blob'
    });
    
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', cv.file_name);
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (error) {
    console.error('Failed to download CV:', error);
    alert('Failed to download CV');
  }
};

const viewHistory = async (cv) => {
  try {
    const response = await axios.get(`/api/cvs/employees/${cv.employee_id}/history`);
    cvHistory.value = response.data.data || response.data;
    showHistoryModal.value = true;
  } catch (error) {
    console.error('Failed to fetch CV history:', error);
    alert('Failed to fetch CV history');
  }
};

const deleteCv = async (cv) => {
  if (!confirm(`Delete CV: ${cv.file_name}?`)) return;
  try {
    await axios.delete(`/api/cvs/${cv.id}`);
    fetchCvs();
    alert('CV deleted successfully');
  } catch (error) {
    console.error('Failed to delete CV:', error);
    alert('Failed to delete CV');
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-PK', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

onMounted(() => {
  fetchCvs();
  fetchEmployees();
});
</script>
