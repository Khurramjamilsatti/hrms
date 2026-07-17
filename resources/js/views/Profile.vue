<template>
  <div class="p-6 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
        <p class="text-sm text-gray-500 mt-1">Personal details, employment info, and account security</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <button
          v-if="!isEditing"
          type="button"
          @click="startEditing"
          :disabled="!hasEmployeeProfile"
          class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50"
        >
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
          Edit Profile
        </button>
        <template v-else>
          <button type="button" @click="cancelEditing" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
          <button type="button" @click="saveProfile" :disabled="saving" class="px-4 py-2.5 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Changes' }}
          </button>
        </template>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-24">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <template v-else>
      <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center gap-5">
          <div class="relative shrink-0">
            <img
              v-if="pictureUrl"
              :src="pictureUrl"
              class="w-24 h-24 rounded-full object-cover border border-gray-200"
              alt="Profile"
            />
            <div
              v-else
              class="w-24 h-24 rounded-full bg-accent text-white flex items-center justify-center text-2xl font-bold"
            >
              {{ getInitials(profileData.name) }}
            </div>
            <button
              v-if="isEditing && hasEmployeeProfile"
              type="button"
              @click="triggerFileUpload"
              class="absolute -bottom-1 -right-1 bg-white border border-gray-300 text-gray-800 p-2 rounded-full shadow hover:bg-gray-50"
              title="Change photo"
            >
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm3 3a1 1 0 011-1h4a1 1 0 110 2H8a1 1 0 01-1-1zm-1 5l2.293-2.293a1 1 0 011.414 0L13 12l1.293-1.293a1 1 0 011.414 0L17 13v1H5v-1l1-1z"/></svg>
            </button>
            <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="handleProfilePictureChange" />
          </div>
          <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
              <h2 class="text-2xl font-bold text-gray-900 truncate">{{ profileData.name || '—' }}</h2>
              <span
                class="px-2.5 py-1 text-xs font-semibold rounded-full"
                :class="statusClass"
              >
                {{ employmentStatusLabel }}
              </span>
            </div>
            <p class="text-sm text-gray-600 mt-1">
              {{ profileData.employee?.designation?.title || 'Team member' }}
              <span v-if="profileData.employee?.department?.name"> · {{ profileData.employee.department.name }}</span>
            </p>
            <p class="text-sm text-gray-500 mt-1">{{ profileData.email }}</p>
            <p v-if="profileData.employee?.employee_code" class="text-xs text-gray-500 mt-1">
              Employee code: {{ profileData.employee.employee_code }}
            </p>
          </div>
        </div>
      </div>

      <div v-if="!hasEmployeeProfile" class="rounded-xl border border-amber-200 bg-amber-50 p-5">
        <p class="text-sm font-semibold text-amber-900">No employee profile linked</p>
        <p class="mt-1 text-sm text-amber-800">
          Personal check-in, leave, and payslips need an employee record linked to this account.
          Ask HR to link your user, or create an employee profile if you have access.
        </p>
        <div class="mt-4 flex flex-wrap gap-2">
          <router-link
            v-if="canCreateEmployee"
            to="/employees/create"
            class="inline-flex items-center rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-white hover:bg-accent-dark"
          >
            Create employee profile
          </router-link>
          <router-link
            v-if="canManageUsers"
            to="/admin/user-roles"
            class="inline-flex items-center rounded-lg border border-amber-300 bg-white px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-amber-100"
          >
            Manage users
          </router-link>
          <router-link
            v-if="canViewEmployees"
            to="/employees"
            class="inline-flex items-center rounded-lg border border-amber-300 bg-white px-4 py-2 text-sm font-semibold text-amber-900 hover:bg-amber-100"
          >
            View employees
          </router-link>
        </div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
        <div v-for="stat in statCards" :key="stat.label" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ stat.label }}</p>
          <p
            class="font-bold text-gray-900 mt-1"
            :class="stat.compact ? 'text-base leading-snug' : 'text-2xl'"
          >
            {{ stat.value }}
          </p>
        </div>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="space-y-4">
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-base font-bold text-gray-900 mb-3">Quick actions</h3>
            <div class="space-y-2">
              <button type="button" @click="openPasswordChangeModal" class="w-full text-left px-4 py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-sm font-medium text-gray-900">
                Change password
              </button>
              <button type="button" @click="$router.push('/attendance')" class="w-full text-left px-4 py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-sm font-medium text-gray-900">
                View attendance
              </button>
              <button type="button" @click="$router.push('/leaves')" class="w-full text-left px-4 py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-sm font-medium text-gray-900">
                My leaves
              </button>
              <button type="button" @click="$router.push('/files')" class="w-full text-left px-4 py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-sm font-medium text-gray-900">
                My documents
              </button>
              <button type="button" @click="$router.push('/shifts/my')" class="w-full text-left px-4 py-2.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-sm font-medium text-gray-900">
                My schedule
              </button>
            </div>
          </div>
        </div>

        <div class="xl:col-span-2 space-y-4">
          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4">Personal information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Full name</label>
                <input v-if="isEditing" v-model="editForm.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
                <p v-else class="text-sm text-gray-900">{{ profileData.name || '—' }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Email</label>
                <p class="text-sm text-gray-900">{{ profileData.email || '—' }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Phone</label>
                <input v-if="isEditing" v-model="editForm.phone" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
                <p v-else class="text-sm text-gray-900">{{ profileData.employee?.phone || '—' }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Date of birth</label>
                <input v-if="isEditing" v-model="editForm.date_of_birth" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
                <p v-else class="text-sm text-gray-900">{{ formatDate(profileData.employee?.date_of_birth) }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Gender</label>
                <select v-if="isEditing" v-model="editForm.gender" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                  <option value="">Select</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                </select>
                <p v-else class="text-sm text-gray-900 capitalize">{{ profileData.employee?.gender || '—' }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">CNIC / National ID</label>
                <input v-if="isEditing" v-model="editForm.cnic" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
                <p v-else class="text-sm text-gray-900">{{ profileData.employee?.cnic || profileData.employee?.national_id || '—' }}</p>
              </div>
              <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Address</label>
                <textarea v-if="isEditing" v-model="editForm.address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
                <p v-else class="text-sm text-gray-900">{{ profileData.employee?.address || '—' }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4">Employment</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Employee code</label>
                <p class="text-sm text-gray-900">{{ profileData.employee?.employee_code || '—' }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Join date</label>
                <p class="text-sm text-gray-900">{{ formatDate(profileData.employee?.joining_date) }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Department</label>
                <p class="text-sm text-gray-900">{{ profileData.employee?.department?.name || '—' }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Designation</label>
                <p class="text-sm text-gray-900">{{ profileData.employee?.designation?.title || '—' }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Employment type</label>
                <p class="text-sm text-gray-900 capitalize">{{ profileData.employee?.employment_type || '—' }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Manager</label>
                <p class="text-sm text-gray-900">{{ profileData.employee?.manager?.name || '—' }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-base font-bold text-gray-900 mb-4">Emergency contact</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Contact name</label>
                <input v-if="isEditing" v-model="editForm.emergency_contact_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
                <p v-else class="text-sm text-gray-900">{{ profileData.employee?.emergency_contact_name || '—' }}</p>
              </div>
              <div>
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Contact phone</label>
                <input v-if="isEditing" v-model="editForm.emergency_contact_phone" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
                <p v-else class="text-sm text-gray-900">{{ profileData.employee?.emergency_contact_phone || '—' }}</p>
              </div>
              <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Relationship</label>
                <input v-if="isEditing" v-model="editForm.emergency_contact_relationship" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
                <p v-else class="text-sm text-gray-900">{{ profileData.employee?.emergency_contact_relationship || '—' }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div v-if="showPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
          <h3 class="text-lg font-bold text-gray-900">Change password</h3>
          <button type="button" @click="closePasswordModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <form @submit.prevent="changePassword" class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Current password</label>
            <input v-model="passwordForm.current_password" type="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">New password</label>
            <input v-model="passwordForm.new_password" type="password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm new password</label>
            <input v-model="passwordForm.new_password_confirmation" type="password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" @click="closePasswordModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
            <button type="submit" :disabled="changingPassword" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">
              {{ changingPassword ? 'Updating...' : 'Update password' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import { useNotification } from '@/composables/useNotification';
import { usePermissions } from '@/composables/usePermissions';

const { success, error: showError } = useNotification();
const { can, canAccessModule } = usePermissions();

const canCreateEmployee = computed(() => can('employees.create'));
const canViewEmployees = computed(() => canAccessModule('employees'));
const canManageUsers = computed(() => canAccessModule('users') || can('users.manage') || can('users.view'));

const loading = ref(true);
const saving = ref(false);
const changingPassword = ref(false);
const isEditing = ref(false);
const showPasswordModal = ref(false);
const fileInput = ref(null);

const profileData = ref({
  name: '',
  email: '',
  profile_picture: '',
  employee: null,
});

const editForm = reactive({
  name: '',
  phone: '',
  date_of_birth: '',
  gender: '',
  cnic: '',
  address: '',
  emergency_contact_name: '',
  emergency_contact_phone: '',
  emergency_contact_relationship: '',
});

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
});

const statistics = reactive({
  days_employed: 0,
  hours_employed: 0,
  minutes_employed: 0,
  leave_balance: 0,
  attendance_count: 0,
  pending_requests: 0,
  completed_tasks: 0,
  total_documents: 0,
});

const formatTenure = (days, hours, minutes) => {
  const d = Number(days) || 0;
  const h = Number(hours) || 0;
  const m = Number(minutes) || 0;
  const dayLabel = d === 1 ? '1 day' : `${d} days`;
  const hourLabel = h === 1 ? '1 hr' : `${h} hrs`;
  const minLabel = m === 1 ? '1 min' : `${m} mins`;
  return `${dayLabel}, ${hourLabel}, ${minLabel}`;
};

const hasEmployeeProfile = computed(() => !!profileData.value.employee?.id);

const pictureUrl = computed(() => {
  const pic = profileData.value.profile_picture || profileData.value.employee?.profile_picture;
  if (!pic) return '';
  if (pic.startsWith('http') || pic.startsWith('/storage/')) return pic;
  return `/storage/${pic.replace(/^\//, '')}`;
});

const employmentStatusLabel = computed(() => {
  const status = profileData.value.employee?.employment_status || profileData.value.employee?.status;
  return status ? String(status).replaceAll('_', ' ') : 'No profile';
});

const statusClass = computed(() => {
  const status = profileData.value.employee?.employment_status || profileData.value.employee?.status;
  if (status === 'active') return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
  if (!status) return 'bg-gray-100 text-gray-700';
  return 'bg-amber-50 text-amber-800 border border-amber-200';
});

const statCards = computed(() => [
  {
    label: 'Time employed',
    value: formatTenure(statistics.days_employed, statistics.hours_employed, statistics.minutes_employed),
    compact: true,
  },
  { label: 'Leave balance', value: statistics.leave_balance },
  { label: 'Attendance', value: statistics.attendance_count },
  { label: 'Pending', value: statistics.pending_requests },
  { label: 'Tasks', value: statistics.completed_tasks },
  { label: 'Documents', value: statistics.total_documents },
]);

const populateForm = () => {
  const emp = profileData.value.employee || {};
  editForm.name = profileData.value.name || '';
  editForm.phone = emp.phone || '';
  editForm.date_of_birth = emp.date_of_birth ? String(emp.date_of_birth).slice(0, 10) : '';
  editForm.gender = emp.gender || '';
  editForm.cnic = emp.cnic || emp.national_id || '';
  editForm.address = emp.address || '';
  editForm.emergency_contact_name = emp.emergency_contact_name || '';
  editForm.emergency_contact_phone = emp.emergency_contact_phone || '';
  editForm.emergency_contact_relationship = emp.emergency_contact_relationship || '';
};

const fetchProfile = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/me');
    profileData.value = response.data;
    populateForm();
    await fetchStatistics();
  } catch (err) {
    console.error(err);
    showError('Failed to load profile');
  } finally {
    loading.value = false;
  }
};

const fetchStatistics = async () => {
  try {
    const response = await axios.get('/profile/stats');
    const data = response.data.data || response.data;
    Object.assign(statistics, {
      days_employed: data.days_employed ?? 0,
      hours_employed: data.hours_employed ?? 0,
      minutes_employed: data.minutes_employed ?? 0,
      leave_balance: data.leave_balance ?? 0,
      attendance_count: data.attendance_count ?? 0,
      pending_requests: data.pending_requests ?? 0,
      completed_tasks: data.completed_tasks ?? 0,
      total_documents: data.total_documents ?? 0,
    });
  } catch (err) {
    console.error(err);
  }
};

const startEditing = () => {
  populateForm();
  isEditing.value = true;
};

const cancelEditing = () => {
  isEditing.value = false;
  populateForm();
};

const saveProfile = async () => {
  saving.value = true;
  try {
    const response = await axios.put('/profile', {
      name: editForm.name,
      employee: {
        phone: editForm.phone,
        date_of_birth: editForm.date_of_birth || null,
        gender: editForm.gender || null,
        cnic: editForm.cnic,
        address: editForm.address,
        emergency_contact_name: editForm.emergency_contact_name,
        emergency_contact_phone: editForm.emergency_contact_phone,
        emergency_contact_relationship: editForm.emergency_contact_relationship,
      },
    });
    profileData.value = response.data.user || response.data;
    populateForm();
    isEditing.value = false;
    success('Profile updated');
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to update profile');
  } finally {
    saving.value = false;
  }
};

const triggerFileUpload = () => fileInput.value?.click();

const handleProfilePictureChange = async (event) => {
  const file = event.target.files?.[0];
  if (!file) return;
  const formData = new FormData();
  formData.append('profile_picture', file);
  try {
    const response = await axios.post('/profile/picture', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    profileData.value.profile_picture = response.data.profile_picture;
    success('Profile picture updated');
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to upload picture');
  } finally {
    event.target.value = '';
  }
};

const openPasswordChangeModal = () => {
  Object.assign(passwordForm, {
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
  });
  showPasswordModal.value = true;
};

const closePasswordModal = () => {
  showPasswordModal.value = false;
};

const changePassword = async () => {
  if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
    showError('New passwords do not match');
    return;
  }
  changingPassword.value = true;
  try {
    await axios.post('/change-password', {
      current_password: passwordForm.current_password,
      new_password: passwordForm.new_password,
      new_password_confirmation: passwordForm.new_password_confirmation,
    });
    success('Password changed');
    closePasswordModal();
  } catch (err) {
    showError(err.response?.data?.message || err.response?.data?.errors?.current_password?.[0] || 'Failed to change password');
  } finally {
    changingPassword.value = false;
  }
};

const getInitials = (name) => {
  if (!name) return '?';
  return name
    .split(' ')
    .filter(Boolean)
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
};

const formatDate = (date) => {
  if (!date) return '—';
  return new Date(date).toLocaleDateString('en-PK', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

onMounted(fetchProfile);
</script>
