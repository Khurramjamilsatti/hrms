<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-6">
      <div class="flex items-start gap-3">
        <router-link
          to="/cvs"
          class="mt-1 p-2 hover:bg-gray-100 rounded-lg transition-colors"
          title="Back to CV Bank"
        >
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </router-link>
        <div>
          <div class="flex flex-wrap items-center gap-3">
            <h1 class="text-3xl font-bold text-gray-900">{{ employeeName }}'s CV</h1>
            <span
              v-if="cv.is_current"
              class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800"
            >
              Current
            </span>
          </div>
          <p class="text-sm text-gray-500 mt-1">
            Version {{ cv.version || '—' }} · Uploaded {{ formatDate(cv.uploaded_at) }}
          </p>
        </div>
      </div>
      <button
        @click="downloadCv"
        :disabled="!cv.id || downloading"
        class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow disabled:opacity-50"
      >
        <svg v-if="downloading" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        {{ downloading ? 'Downloading…' : 'Download CV' }}
      </button>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg">
      <p class="font-medium">{{ error }}</p>
      <button @click="fetchCvDetails" class="mt-2 text-sm underline">Try again</button>
    </div>

    <div v-else class="space-y-5">
      <!-- File + employee strip -->
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div class="flex items-center gap-4 min-w-0">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
              </svg>
            </div>
            <div class="min-w-0">
              <h3 class="text-base font-semibold text-gray-900 truncate">{{ cv.file_name || 'Untitled' }}</h3>
              <p class="text-sm text-gray-500">
                {{ formatFileSize(cv.file_size) }}
                <span v-if="cv.file_type"> · {{ cv.file_type }}</span>
              </p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
              <span class="text-sm font-bold text-gray-700">{{ initials }}</span>
            </div>
            <div>
              <div class="text-sm font-semibold text-gray-900">{{ employeeName }}</div>
              <div class="text-xs text-gray-500">{{ cv.employee?.user?.email || cv.employee?.email || '—' }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Main column -->
        <div class="lg:col-span-2 space-y-5">
          <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Professional Profile</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
              <div class="rounded-lg bg-gray-50 border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Experience</p>
                <p class="mt-1 text-lg font-bold text-gray-900">
                  {{ cv.experience_years ?? '—' }}
                  <span v-if="cv.experience_years != null" class="text-sm font-medium text-gray-500">
                    {{ Number(cv.experience_years) === 1 ? 'year' : 'years' }}
                  </span>
                </p>
              </div>
              <div class="rounded-lg bg-gray-50 border border-gray-100 p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Education</p>
                <p class="mt-1 text-lg font-bold text-gray-900">{{ cv.education_level || 'Not specified' }}</p>
              </div>
            </div>
            <div v-if="cv.summary">
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Summary</p>
              <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ cv.summary }}</p>
            </div>
            <p v-else class="text-sm text-gray-400 italic">No professional summary provided.</p>
          </div>

          <div v-if="cv.skills?.length" class="bg-white rounded-lg shadow border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Skills</h2>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="skill in cv.skills"
                :key="skill"
                class="px-3 py-1.5 bg-gray-100 text-gray-800 rounded-lg text-sm font-medium"
              >
                {{ skill }}
              </span>
            </div>
          </div>

          <div v-if="cv.certifications?.length" class="bg-white rounded-lg shadow border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Certifications</h2>
            <ul class="space-y-2.5">
              <li
                v-for="cert in cv.certifications"
                :key="cert"
                class="flex items-start gap-2.5 text-sm text-gray-800"
              >
                <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ cert }}
              </li>
            </ul>
          </div>

          <div v-if="cv.languages?.length" class="bg-white rounded-lg shadow border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Languages</h2>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="language in cv.languages"
                :key="language"
                class="px-3 py-1.5 bg-gray-100 text-gray-800 rounded-lg text-sm font-medium"
              >
                {{ language }}
              </span>
            </div>
          </div>
        </div>

        <!-- Side column -->
        <div class="space-y-5">
          <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Employee</h2>
            <dl class="space-y-3 text-sm">
              <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</dt>
                <dd class="mt-1 text-gray-900 font-medium">{{ employeeName }}</dd>
              </div>
              <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</dt>
                <dd class="mt-1 text-gray-700">{{ cv.employee?.user?.email || cv.employee?.email || '—' }}</dd>
              </div>
              <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Department</dt>
                <dd class="mt-1 text-gray-700">{{ cv.employee?.department?.name || '—' }}</dd>
              </div>
              <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Employee Code</dt>
                <dd class="mt-1 text-gray-700">{{ cv.employee?.employee_code || cv.employee?.employee_id || '—' }}</dd>
              </div>
            </dl>
          </div>

          <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Upload Info</h2>
            <dl class="space-y-3 text-sm">
              <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Uploaded By</dt>
                <dd class="mt-1 text-gray-700">{{ cv.uploader?.name || '—' }}</dd>
              </div>
              <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Upload Date</dt>
                <dd class="mt-1 text-gray-700">{{ formatDate(cv.uploaded_at) }}</dd>
              </div>
              <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Last Updated</dt>
                <dd class="mt-1 text-gray-700">{{ formatDate(cv.updated_at) }}</dd>
              </div>
              <div v-if="cv.updated_by">
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Updated By</dt>
                <dd class="mt-1 text-gray-700">{{ cv.updater?.name || '—' }}</dd>
              </div>
            </dl>
            <div v-if="cv.notes" class="mt-4 pt-4 border-t border-gray-100">
              <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Notes</p>
              <p class="text-sm text-gray-700">{{ cv.notes }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { useDialog } from '@/composables/useDialog';

const route = useRoute();
const { alert } = useDialog();
const cv = ref({});
const loading = ref(true);
const error = ref(null);
const downloading = ref(false);

const employeeName = computed(() => {
  const emp = cv.value.employee;
  if (!emp) return 'Employee';
  return emp.user?.name || `${emp.first_name || ''} ${emp.last_name || ''}`.trim() || 'Employee';
});

const initials = computed(() =>
  employeeName.value
    .split(' ')
    .filter(Boolean)
    .map((n) => n[0])
    .join('')
    .substring(0, 2)
    .toUpperCase()
);

const fetchCvDetails = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get(`/cvs/${route.params.id}`);
    cv.value = response.data;
  } catch (err) {
    console.error('Error fetching CV details:', err);
    error.value = 'Failed to load CV details';
  } finally {
    loading.value = false;
  }
};

const downloadCv = async () => {
  if (downloading.value) return;
  downloading.value = true;
  try {
    const response = await axios.get(`/cvs/${route.params.id}/download`, {
      responseType: 'blob',
    });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', cv.value.file_name || 'cv.pdf');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
  } catch (err) {
    console.error('Error downloading CV:', err);
    await alert({
      title: 'Error',
      message: 'Failed to download CV',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  } finally {
    downloading.value = false;
  }
};

const formatDate = (date) => {
  if (!date) return '—';
  return new Date(date).toLocaleDateString('en-PK', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const formatFileSize = (bytes) => {
  if (!bytes) return 'Unknown size';
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
};

onMounted(() => {
  fetchCvDetails();
});
</script>
