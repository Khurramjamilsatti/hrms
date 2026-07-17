<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">CV Bank</h1>
        <p class="text-sm text-gray-500 mt-1">Store, version, and download employee resumes</p>
      </div>
      <button
        v-if="canManage"
        @click="openUploadModal"
        class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
        </svg>
        Upload CV
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Total CVs</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Current Versions</p>
        <h3 class="text-2xl font-bold text-green-600">{{ stats.current }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Employees Covered</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.employees }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Avg. Experience</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.avgExperience }} yrs</h3>
      </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="relative max-w-xl">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          placeholder="Search by employee name or email..."
          class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
        />
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
      <p class="font-medium">{{ error }}</p>
      <button @click="fetchCvs()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <!-- Empty -->
    <div v-else-if="cvs.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <div class="mx-auto w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-4">
        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      </div>
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No CVs found</h3>
      <p class="text-gray-500 mb-5">
        {{ searchQuery ? 'Try a different search term.' : 'Upload the first employee resume to get started.' }}
      </p>
      <button
        v-if="canManage && !searchQuery"
        @click="openUploadModal"
        class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors"
      >
        Upload CV
      </button>
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employee</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Document</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Version</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Experience</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Skills</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Uploaded</th>
              <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="cv in cvs"
              :key="cv.id"
              class="hover:bg-gray-50 transition-colors"
              :class="{ 'bg-emerald-50/40': cv.is_current }"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-3">
                  <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-bold text-gray-700">{{ getInitials(cv.employee) }}</span>
                  </div>
                  <div class="min-w-0">
                    <div class="text-sm font-semibold text-gray-900 truncate">{{ getEmployeeName(cv.employee) }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ cv.employee?.user?.email || cv.employee?.email || '—' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-start gap-2.5 min-w-0">
                  <div class="mt-0.5 flex-shrink-0 w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                    </svg>
                  </div>
                  <div class="min-w-0">
                    <div class="text-sm font-medium text-gray-900 truncate max-w-[200px]" :title="cv.file_name">{{ cv.file_name }}</div>
                    <span
                      v-if="cv.is_current"
                      class="inline-flex mt-1 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide bg-green-100 text-green-800 rounded-full"
                    >
                      Current
                    </span>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-md bg-gray-100 text-gray-800">
                  v{{ cv.version }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                {{ formatExperience(cv.experience_years) }}
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1 max-w-[220px]">
                  <span
                    v-for="(skill, index) in (cv.skills || []).slice(0, 3)"
                    :key="index"
                    class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 rounded-md"
                  >
                    {{ skill }}
                  </span>
                  <span
                    v-if="(cv.skills || []).length > 3"
                    class="px-2 py-0.5 text-xs font-medium bg-gray-200 text-gray-600 rounded-md"
                  >
                    +{{ (cv.skills || []).length - 3 }}
                  </span>
                  <span v-if="!(cv.skills || []).length" class="text-xs text-gray-400">—</span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ formatDate(cv.uploaded_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center justify-center gap-1">
                  <button
                    @click="viewDetails(cv.id)"
                    class="p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                    title="View details"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                  <button
                    @click="downloadCv(cv)"
                    class="p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                    title="Download"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                  </button>
                  <button
                    @click="viewHistory(cv)"
                    class="p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                    title="Version history"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>
                  <button
                    v-if="canManage"
                    @click="deleteCv(cv)"
                    class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    title="Delete"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination
        v-if="cvs.length > 0"
        :current-page="pagination.current_page"
        :total-pages="pagination.last_page"
        :total="pagination.total"
        :from="pagination.from"
        :to="pagination.to"
        @page-change="handlePageChange"
      />
    </div>

    <!-- Upload Modal -->
    <div v-if="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closeUploadModal">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
          <div>
            <h3 class="text-lg font-bold text-gray-900">Upload Employee CV</h3>
            <p class="text-xs text-gray-500 mt-0.5">PDF, DOC, or DOCX · max 5MB</p>
          </div>
          <button @click="closeUploadModal" :disabled="uploading" class="text-gray-400 hover:text-gray-600 disabled:opacity-40">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>

        <form @submit.prevent="submitCvUpload" class="px-6 py-5 space-y-4 overflow-y-auto">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Employee *</label>
            <select
              v-model="cvForm.employee_id"
              required
              :disabled="uploading"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:bg-gray-100"
            >
              <option value="">Select employee</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.first_name }} {{ emp.last_name }}{{ emp.employee_code ? ` (${emp.employee_code})` : '' }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">CV File *</label>
            <label
              class="flex flex-col items-center justify-center w-full px-4 py-8 border-2 border-dashed rounded-lg cursor-pointer transition-colors"
              :class="cvForm.cv_file ? 'border-gray-900 bg-gray-50' : 'border-gray-300 hover:border-gray-400 hover:bg-gray-50'"
            >
              <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
              <span v-if="cvForm.cv_file" class="text-sm font-medium text-gray-900">{{ cvForm.cv_file.name }}</span>
              <span v-else class="text-sm text-gray-500">Click to choose a file or drag it here</span>
              <input @change="handleFileChange" type="file" accept=".pdf,.doc,.docx" required class="hidden" :disabled="uploading" />
            </label>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Experience (years)</label>
              <input
                v-model="cvForm.experience_years"
                type="number"
                min="0"
                step="1"
                :disabled="uploading"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:bg-gray-100"
              />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Education level</label>
              <select
                v-model="cvForm.education_level"
                :disabled="uploading"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:bg-gray-100"
              >
                <option value="">Select level</option>
                <option value="High School">High School</option>
                <option value="Bachelor">Bachelor's</option>
                <option value="Masters">Master's</option>
                <option value="PhD">PhD</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Summary</label>
            <textarea
              v-model="cvForm.summary"
              rows="3"
              :disabled="uploading"
              placeholder="Brief professional summary..."
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:bg-gray-100"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Skills</label>
            <input
              v-model="cvForm.skillsText"
              type="text"
              :disabled="uploading"
              placeholder="PHP, Laravel, Vue.js, PostgreSQL"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:bg-gray-100"
            />
            <p class="mt-1 text-xs text-gray-500">Comma-separated</p>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Certifications</label>
            <input
              v-model="cvForm.certificationsText"
              type="text"
              :disabled="uploading"
              placeholder="AWS Certified, PMP"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 disabled:bg-gray-100"
            />
            <p class="mt-1 text-xs text-gray-500">Comma-separated</p>
          </div>

          <div v-if="uploadError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ uploadError }}</div>
        </form>

        <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3 bg-gray-50 flex-shrink-0">
          <button
            type="button"
            @click="closeUploadModal"
            :disabled="uploading"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
          >
            Cancel
          </button>
          <button
            type="button"
            @click="submitCvUpload"
            :disabled="uploading"
            class="inline-flex items-center px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="uploading" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ uploading ? 'Uploading…' : 'Upload CV' }}
          </button>
        </div>
      </div>
    </div>

    <!-- History Modal -->
    <div v-if="showHistoryModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showHistoryModal = false">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
          <div>
            <h3 class="text-lg font-bold text-gray-900">Version History</h3>
            <p class="text-xs text-gray-500 mt-0.5">{{ historyEmployeeName }}</p>
          </div>
          <button @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>

        <div class="px-6 py-5 overflow-y-auto space-y-3">
          <div v-if="historyLoading" class="flex justify-center py-10">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
          </div>
          <div v-else-if="cvHistory.length === 0" class="text-center py-10 text-gray-500 text-sm">No version history found.</div>
          <div
            v-for="item in cvHistory"
            :key="item.id"
            class="flex items-center justify-between gap-4 p-4 rounded-lg border"
            :class="item.is_current ? 'bg-emerald-50 border-emerald-200' : 'bg-white border-gray-200'"
          >
            <div class="min-w-0 flex items-start gap-3">
              <div class="w-9 h-9 rounded-lg bg-white border border-gray-200 flex items-center justify-center flex-shrink-0">
                <span class="text-xs font-bold text-gray-700">v{{ item.version }}</span>
              </div>
              <div class="min-w-0">
                <div class="text-sm font-semibold text-gray-900 truncate">{{ item.file_name }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ formatDate(item.uploaded_at) }}</div>
                <span
                  v-if="item.is_current"
                  class="inline-flex mt-1.5 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide bg-green-100 text-green-800 rounded-full"
                >
                  Current
                </span>
              </div>
            </div>
            <button
              @click="downloadCv(item)"
              class="flex-shrink-0 inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              Download
            </button>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 flex justify-end bg-gray-50 flex-shrink-0">
          <button
            @click="showHistoryModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Pagination from '@/components/Pagination.vue';
import { useDialog } from '@/composables/useDialog';
import { usePermissions } from '@/composables/usePermissions';

const router = useRouter();
const { confirm, alert } = useDialog();
const { can } = usePermissions();

const canManage = computed(() => can('cv_bank.manage'));

const cvs = ref([]);
const pagination = ref({ current_page: 1, last_page: 1, total: 0, from: 0, to: 0 });
const employees = ref([]);
const searchQuery = ref('');
const loading = ref(false);
const error = ref(null);
const showUploadModal = ref(false);
const showHistoryModal = ref(false);
const cvHistory = ref([]);
const historyEmployeeName = ref('');
const historyLoading = ref(false);
const uploading = ref(false);
const uploadError = ref(null);

let searchTimer = null;

const emptyForm = () => ({
  employee_id: '',
  cv_file: null,
  summary: '',
  experience_years: '',
  education_level: '',
  skillsText: '',
  certificationsText: '',
});

const cvForm = ref(emptyForm());

const stats = computed(() => {
  const list = cvs.value || [];
  const employeeIds = new Set(list.map((c) => c.employee_id).filter(Boolean));
  const years = list.map((c) => Number(c.experience_years) || 0).filter((y) => y > 0);
  const avg = years.length ? years.reduce((a, b) => a + b, 0) / years.length : 0;
  return {
    total: pagination.value.total || list.length,
    current: list.filter((c) => c.is_current).length,
    employees: employeeIds.size,
    avgExperience: avg ? avg.toFixed(1) : '0',
  };
});

const getEmployeeName = (emp) => {
  if (!emp) return 'Unknown';
  return emp.user?.name || `${emp.first_name || ''} ${emp.last_name || ''}`.trim() || 'Unknown';
};

const getInitials = (emp) => {
  const name = getEmployeeName(emp);
  return name
    .split(' ')
    .filter(Boolean)
    .map((n) => n[0])
    .join('')
    .substring(0, 2)
    .toUpperCase();
};

const formatExperience = (years) => {
  const n = Number(years);
  if (!n && n !== 0) return '—';
  return `${n} ${n === 1 ? 'year' : 'years'}`;
};

const formatDate = (dateString) => {
  if (!dateString) return '—';
  return new Date(dateString).toLocaleDateString('en-PK', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const fetchCvs = async (page = 1) => {
  loading.value = true;
  error.value = null;
  try {
    const params = new URLSearchParams();
    params.append('page', page);
    if (searchQuery.value) params.append('search', searchQuery.value);

    const response = await axios.get(`/cvs?${params}`);
    cvs.value = response.data.data || response.data || [];

    if (response.data.current_page) {
      pagination.value = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        total: response.data.total,
        from: response.data.from || 0,
        to: response.data.to || 0,
      };
    }
  } catch (err) {
    console.error('Failed to fetch CVs:', err);
    error.value = 'Failed to load CVs';
  } finally {
    loading.value = false;
  }
};

const handleSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => fetchCvs(1), 300);
};

const handlePageChange = (page) => {
  fetchCvs(page);
};

const viewDetails = (cvId) => {
  router.push(`/cvs/${cvId}`);
};

const fetchEmployees = async () => {
  try {
    const response = await axios.get('/employees/dropdown');
    employees.value = response.data.data || response.data || [];
  } catch (err) {
    console.error('Failed to fetch employees:', err);
  }
};

const openUploadModal = () => {
  uploadError.value = null;
  cvForm.value = emptyForm();
  showUploadModal.value = true;
};

const closeUploadModal = () => {
  if (uploading.value) return;
  showUploadModal.value = false;
};

const handleFileChange = (event) => {
  cvForm.value.cv_file = event.target.files?.[0] || null;
};

const submitCvUpload = async () => {
  uploadError.value = null;
  if (!cvForm.value.employee_id) {
    uploadError.value = 'Please select an employee';
    return;
  }
  if (!cvForm.value.cv_file) {
    uploadError.value = 'Please choose a CV file';
    return;
  }

  try {
    uploading.value = true;
    const formData = new FormData();
    formData.append('employee_id', cvForm.value.employee_id);
    formData.append('file', cvForm.value.cv_file);
    if (cvForm.value.summary) formData.append('summary', cvForm.value.summary);
    if (cvForm.value.experience_years) formData.append('experience_years', cvForm.value.experience_years);
    if (cvForm.value.education_level) formData.append('education_level', cvForm.value.education_level);

    if (cvForm.value.skillsText) {
      const skills = cvForm.value.skillsText.split(',').map((s) => s.trim()).filter(Boolean);
      skills.forEach((skill) => formData.append('skills[]', skill));
    }

    if (cvForm.value.certificationsText) {
      const certs = cvForm.value.certificationsText.split(',').map((s) => s.trim()).filter(Boolean);
      certs.forEach((cert) => formData.append('certifications[]', cert));
    }

    await axios.post('/cvs', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    showUploadModal.value = false;
    cvForm.value = emptyForm();
    await fetchCvs();
    await alert({
      title: 'Success',
      message: 'CV uploaded successfully!',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'success',
    });
  } catch (err) {
    console.error('Failed to upload CV:', err);
    const validationErrors = err.response?.data?.errors
      ? Object.values(err.response.data.errors).flat().join(' ')
      : null;
    uploadError.value = validationErrors || err.response?.data?.message || 'Failed to upload CV';
  } finally {
    uploading.value = false;
  }
};

const downloadCv = async (cv) => {
  try {
    const response = await axios.get(`/cvs/${cv.id}/download`, {
      responseType: 'blob',
    });

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', cv.file_name);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (err) {
    console.error('Failed to download CV:', err);
    await alert({
      title: 'Error',
      message: 'Failed to download CV',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  }
};

const viewHistory = async (cv) => {
  historyEmployeeName.value = getEmployeeName(cv.employee);
  showHistoryModal.value = true;
  historyLoading.value = true;
  cvHistory.value = [];
  try {
    const response = await axios.get(`/cvs/employees/${cv.employee_id}/history`);
    cvHistory.value = response.data.data || response.data || [];
  } catch (err) {
    console.error('Failed to fetch CV history:', err);
    showHistoryModal.value = false;
    await alert({
      title: 'Error',
      message: 'Failed to fetch CV history',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  } finally {
    historyLoading.value = false;
  }
};

const deleteCv = async (cv) => {
  if (!(await confirm({
    title: 'Delete CV?',
    message: `Delete "${cv.file_name}" for ${getEmployeeName(cv.employee)}? This cannot be undone.`,
    confirmText: 'Delete',
    cancelText: 'Cancel',
    variant: 'danger',
  }))) return;

  try {
    await axios.delete(`/cvs/${cv.id}`);
    await fetchCvs(pagination.value.current_page || 1);
    await alert({
      title: 'Success',
      message: 'CV deleted successfully',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'success',
    });
  } catch (err) {
    console.error('Failed to delete CV:', err);
    await alert({
      title: 'Error',
      message: err.response?.data?.message || 'Failed to delete CV',
      confirmText: 'OK',
      cancelText: 'Close',
      variant: 'danger',
    });
  }
};

onMounted(() => {
  fetchCvs();
  fetchEmployees();
});
</script>
