<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex items-center mb-6">
      <router-link to="/employees" class="mr-3 p-2 hover:bg-gray-100 rounded-lg transition-colors">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </router-link>
      <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Edit Employee' : 'Add New Employee' }}</h1>
    </div>

    <!-- Error Banner -->
    <div v-if="error" class="mb-5 flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
      <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span class="text-sm">{{ error }}</span>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Personal Information -->
      <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          Personal Information
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
            <input v-model="form.first_name" type="text" required
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
            <input v-model="form.last_name" type="text" required
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
            <input v-model="form.email" type="email" required :disabled="isEdit"
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-100 disabled:text-gray-500" />
          </div>

          <div v-if="!isEdit">
            <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
            <input v-model="form.password" type="password" required
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input v-model="form.phone" type="text"
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
            <select v-model="form.gender"
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
              <option value="">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
            <input v-model="form.date_of_birth" type="date"
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">National ID / CNIC</label>
            <input v-model="form.national_id" type="text" placeholder="e.g. 12345-1234567-1"
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
          </div>
        </div>
      </div>

      <!-- Employment Information -->
      <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          Employment Information
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employee Code <span class="text-red-500">*</span></label>
            <input v-model="form.employee_code" type="text" required :disabled="isEdit" placeholder="e.g. EMP-004"
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent disabled:bg-gray-100 disabled:text-gray-500" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
            <select v-model="form.role" required
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
              <option value="super_admin">Super Admin</option>
              <option value="hr_admin">HR Admin</option>
              <option value="section_head">Section Head</option>
              <option value="admin">Admin</option>
              <option value="manager">Manager</option>
              <option value="employee">Employee</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
            <select v-model="form.department_id"
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
              <option value="">Select Department</option>
              <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
            <select v-model="form.designation_id"
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
              <option value="">Select Designation</option>
              <option v-for="desig in designations" :key="desig.id" :value="desig.id">{{ desig.title }}</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employment Type <span class="text-red-500">*</span></label>
            <select v-model="form.employment_type" required
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
              <option value="full_time">Full Time</option>
              <option value="part_time">Part Time</option>
              <option value="contract">Contract</option>
              <option value="intern">Intern</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Joining Date <span class="text-red-500">*</span></label>
            <input v-model="form.joining_date" type="date" required
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
          </div>

          <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">Reporting Manager (Section Head)</label>
            <div class="relative">
              <input
                v-model="managerSearch"
                @focus="showManagerDropdown = true"
                @blur="handleManagerBlur"
                @input="filterManagers"
                type="text"
                placeholder="Search section heads..."
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
              />
              <div v-if="selectedManager" class="absolute inset-y-0 right-0 flex items-center pr-3">
                <button
                  @mousedown.prevent="clearManager"
                  type="button"
                  class="text-gray-400 hover:text-gray-600"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            <div
              v-if="showManagerDropdown && filteredManagers.length > 0"
              class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto"
            >
              <div
                v-for="mgr in filteredManagers"
                :key="mgr.id"
                @mousedown.prevent="selectManager(mgr)"
                class="px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0"
              >
                <div class="font-medium text-gray-900">{{ mgr.name }}</div>
                <div class="text-sm text-gray-500">
                  {{ mgr.employee_code }}
                  <span v-if="mgr.department"> • {{ mgr.department }}</span>
                </div>
              </div>
            </div>
            <div
              v-else-if="showManagerDropdown && managerSearch && filteredManagers.length === 0"
              class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg p-3 text-gray-500 text-sm"
            >
              No section heads found
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact</label>
            <input v-model="form.emergency_contact" type="text" placeholder="Name & Phone"
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
          </div>
        </div>
      </div>

      <!-- Address Information -->
      <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          Address
        </h2>

        <div class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
            <textarea v-model="form.address" rows="2" placeholder="House #, Street, Area"
              class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
              <input v-model="form.city" type="text"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">State / Province</label>
              <input v-model="form.state" type="text"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
              <input v-model="form.postal_code" type="text"
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" />
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end gap-3">
        <router-link to="/employees"
          class="px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
          Cancel
        </router-link>
        <button type="submit" :disabled="saving"
          class="px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow disabled:opacity-50 disabled:cursor-not-allowed">
          <span v-if="saving" class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Saving...
          </span>
          <span v-else>{{ isEdit ? 'Update Employee' : 'Create Employee' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const isEdit = computed(() => !!route.params.id);
const saving = ref(false);
const error = ref(null);

const departments = ref([]);
const designations = ref([]);
const managers = ref([]);
const filteredManagers = ref([]);
const managerSearch = ref('');
const showManagerDropdown = ref(false);
const selectedManager = ref(null);

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  role: 'employee',
  employee_code: '',
  phone: '',
  gender: '',
  date_of_birth: '',
  employment_type: 'full_time',
  joining_date: '',
  national_id: '',
  department_id: '',
  designation_id: '',
  manager_id: '',
  emergency_contact: '',
  address: '',
  city: '',
  state: '',
  postal_code: '',
  country: 'Pakistan',
});

const loadDropdownData = async () => {
  try {
    const [deptRes, desigRes, mgrRes] = await Promise.all([
      axios.get('/departments'),
      axios.get('/designations'),
      axios.get('/employees/section-heads'),
    ]);
    departments.value = deptRes.data.data || deptRes.data;
    designations.value = desigRes.data;
    managers.value = mgrRes.data || [];
    filteredManagers.value = managers.value;
  } catch (err) {
    console.error('Failed to load dropdown data:', err);
  }
};

const filterManagers = () => {
  const search = managerSearch.value.toLowerCase().trim();
  if (!search) {
    filteredManagers.value = managers.value;
    return;
  }
  filteredManagers.value = managers.value.filter(mgr => 
    mgr.name.toLowerCase().includes(search) ||
    mgr.employee_code?.toLowerCase().includes(search) ||
    mgr.department?.toLowerCase().includes(search)
  );
};

const selectManager = (mgr) => {
  form.value.manager_id = mgr.id;
  selectedManager.value = mgr;
  managerSearch.value = mgr.name;
  showManagerDropdown.value = false;
};

const clearManager = () => {
  form.value.manager_id = '';
  selectedManager.value = null;
  managerSearch.value = '';
  filteredManagers.value = managers.value;
  showManagerDropdown.value = false;
};

const handleManagerBlur = () => {
  setTimeout(() => {
    showManagerDropdown.value = false;
    if (!selectedManager.value && managerSearch.value) {
      managerSearch.value = '';
    }
  }, 200);
};

const handleSubmit = async () => {
  saving.value = true;
  error.value = null;

  try {
    // Build payload, remove empty strings
    const payload = { ...form.value };
    Object.keys(payload).forEach(key => {
      if (payload[key] === '') delete payload[key];
    });

    if (isEdit.value) {
      delete payload.email;
      delete payload.password;
      delete payload.employee_code;
      await axios.put(`/employees/${route.params.id}`, payload);
    } else {
      await axios.post('/employees', payload);
    }
    router.push('/employees');
  } catch (err) {
    if (err.response?.data?.errors) {
      const messages = Object.values(err.response.data.errors).flat();
      error.value = messages.join('. ');
    } else {
      error.value = err.response?.data?.message || 'Failed to save employee';
    }
  } finally {
    saving.value = false;
  }
};

onMounted(async () => {
  await loadDropdownData();

  if (isEdit.value) {
    try {
      const res = await axios.get(`/employees/${route.params.id}`);
      const employee = res.data;
      form.value.first_name = employee.first_name || '';
      form.value.last_name = employee.last_name || '';
      form.value.email = employee.user?.email || '';
      form.value.role = employee.user?.role || 'employee';
      form.value.employee_code = employee.employee_code || '';
      form.value.phone = employee.phone || '';
      form.value.gender = employee.gender || '';
      form.value.date_of_birth = employee.date_of_birth ? employee.date_of_birth.split('T')[0] : '';
      form.value.employment_type = employee.employment_type || 'full_time';
      form.value.joining_date = employee.joining_date ? employee.joining_date.split('T')[0] : '';
      form.value.national_id = employee.national_id || '';
      form.value.department_id = employee.department_id || '';
      form.value.designation_id = employee.designation_id || '';
      form.value.manager_id = employee.manager_id || '';
      
      // Set selected manager if exists
      if (employee.manager_id) {
        const manager = managers.value.find(m => m.id === employee.manager_id);
        if (manager) {
          selectedManager.value = manager;
          managerSearch.value = manager.name;
        }
      }
      
      form.value.emergency_contact = employee.emergency_contact || '';
      form.value.address = employee.address || '';
      form.value.city = employee.city || '';
      form.value.state = employee.state || '';
      form.value.postal_code = employee.postal_code || '';
    } catch (err) {
      error.value = 'Failed to load employee data';
    }
  }
});
</script>
