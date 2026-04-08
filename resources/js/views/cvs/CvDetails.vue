<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
      <router-link
        to="/cvs"
        class="inline-flex items-center text-sm text-gray-600 hover:text-blue-600 mb-4"
      >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to CV Bank
      </router-link>
      
      <div class="flex justify-between items-start">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">{{ cv.employee?.user?.name }}'s CV</h1>
          <p class="text-gray-600 mt-1">
            Version {{ cv.version }} • Uploaded {{ formatDate(cv.uploaded_at) }}
          </p>
        </div>
        <span
          v-if="cv.is_current"
          class="px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800"
        >
          CURRENT
        </span>
      </div>
    </div>

    <!-- CV File Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <svg class="w-12 h-12 text-red-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
            <path d="M8 15h8v2H8zm0-3h8v2H8z"/>
          </svg>
          <div>
            <h3 class="text-lg font-semibold text-gray-900">{{ cv.file_name }}</h3>
            <p class="text-sm text-gray-500">
              {{ formatFileSize(cv.file_size) }} • {{ cv.file_type }}
            </p>
          </div>
        </div>
        <button
          @click="downloadCv"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
        >
          <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          Download CV
        </button>
      </div>
    </div>

    <!-- Professional Profile Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Professional Profile</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-600">Experience</label>
          <p class="mt-1 text-gray-900">
            {{ cv.experience_years }} {{ cv.experience_years === 1 ? 'Year' : 'Years' }}
          </p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Education Level</label>
          <p class="mt-1 text-gray-900">{{ cv.education_level || 'Not specified' }}</p>
        </div>
      </div>
      
      <div v-if="cv.summary" class="mt-6">
        <label class="block text-sm font-medium text-gray-600 mb-2">Professional Summary</label>
        <p class="text-gray-900 leading-relaxed">{{ cv.summary }}</p>
      </div>
    </div>

    <!-- Skills Card -->
    <div v-if="cv.skills?.length > 0" class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Skills</h2>
      <div class="flex flex-wrap gap-2">
        <span
          v-for="skill in cv.skills"
          :key="skill"
          class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium"
        >
          {{ skill }}
        </span>
      </div>
    </div>

    <!-- Certifications Card -->
    <div v-if="cv.certifications?.length > 0" class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Certifications</h2>
      <ul class="space-y-2">
        <li
          v-for="cert in cv.certifications"
          :key="cert"
          class="flex items-start"
        >
          <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span class="text-gray-900">{{ cert }}</span>
        </li>
      </ul>
    </div>

    <!-- Languages Card -->
    <div v-if="cv.languages?.length > 0" class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Languages</h2>
      <div class="flex flex-wrap gap-2">
        <span
          v-for="language in cv.languages"
          :key="language"
          class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium"
        >
          {{ language }}
        </span>
      </div>
    </div>

    <!-- Employee Details Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Employee Information</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-600">Name</label>
          <p class="mt-1 text-gray-900">{{ cv.employee?.user?.name }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Email</label>
          <p class="mt-1 text-gray-900">{{ cv.employee?.user?.email }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Department</label>
          <p class="mt-1 text-gray-900">{{ cv.employee?.department?.name || 'N/A' }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Employee ID</label>
          <p class="mt-1 text-gray-900">{{ cv.employee?.employee_id }}</p>
        </div>
      </div>
    </div>

    <!-- Upload Information Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload Information</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-600">Uploaded By</label>
          <p class="mt-1 text-gray-900">{{ cv.uploader?.name || 'N/A' }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Upload Date</label>
          <p class="mt-1 text-gray-900">{{ formatDate(cv.uploaded_at) }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600">Last Updated</label>
          <p class="mt-1 text-gray-900">{{ formatDate(cv.updated_at) }}</p>
        </div>
        <div v-if="cv.updated_by">
          <label class="block text-sm font-medium text-gray-600">Updated By</label>
          <p class="mt-1 text-gray-900">{{ cv.updater?.name || 'N/A' }}</p>
        </div>
      </div>
      
      <div v-if="cv.notes" class="mt-6">
        <label class="block text-sm font-medium text-gray-600">Notes</label>
        <p class="mt-1 text-gray-900">{{ cv.notes }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const cv = ref({});

const fetchCvDetails = async () => {
  try {
    const response = await axios.get(`/api/cvs/${route.params.id}`);
    cv.value = response.data;
  } catch (error) {
    console.error('Error fetching CV details:', error);
    alert('Failed to load CV details');
  }
};

const downloadCv = async () => {
  try {
    const response = await axios.get(`/api/cvs/${route.params.id}/download`, {
      responseType: 'blob',
    });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', cv.value.file_name);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  } catch (error) {
    console.error('Error downloading CV:', error);
    alert('Failed to download CV');
  }
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-PK', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const formatFileSize = (bytes) => {
  if (!bytes) return 'Unknown';
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
};

onMounted(() => {
  fetchCvDetails();
});
</script>
