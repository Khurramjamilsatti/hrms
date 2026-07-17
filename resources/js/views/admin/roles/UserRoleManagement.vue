<template>
  <div class="p-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">User Role Management</h1>
        <p class="text-sm text-gray-500 mt-1">Assign roles, review permissions, and manage account access</p>
      </div>
      <router-link
        to="/admin/roles"
        class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
      >
        Manage Roles & Permissions
      </router-link>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Users</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">With Role</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.withRole }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Active</p>
        <h3 class="text-2xl font-bold text-green-600">{{ stats.active }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Inactive</p>
        <h3 class="text-2xl font-bold text-red-600">{{ stats.inactive }}</h3>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[220px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Name, email, or employee code..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"
            @input="debounceSearch"
          />
        </div>
        <div class="min-w-[180px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
          <select
            v-model="filters.role_id"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"
            @change="fetchUsers(1)"
          >
            <option value="">All Roles</option>
            <option v-for="role in availableRoles" :key="role.id" :value="role.id">{{ role.name }}</option>
          </select>
        </div>
        <div class="min-w-[140px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <select
            v-model="filters.is_active"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"
            @change="fetchUsers(1)"
          >
            <option value="">All</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>
        <button
          @click="resetFilters"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg"
        >
          Reset
        </button>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
      <p class="font-medium">{{ error }}</p>
      <button @click="fetchUsers()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <div v-else-if="users.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Users Found</h3>
      <p class="text-gray-500">Try adjusting your search or filters.</p>
    </div>

    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-100 border-b border-gray-300">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Employee</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Permissions</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ user.name }}</div>
                <div class="text-xs text-gray-500">{{ user.email }}</div>
              </td>
              <td class="px-6 py-4">
                <template v-if="user.employee">
                  <div class="text-sm text-gray-900">{{ user.employee.full_name || `${user.employee.first_name} ${user.employee.last_name}` }}</div>
                  <div class="text-xs text-gray-500">{{ user.employee.employee_code }} · {{ user.employee.department?.name || 'No dept' }}</div>
                </template>
                <span v-else class="text-sm text-gray-400">No employee profile</span>
              </td>
              <td class="px-6 py-4">
                <span
                  v-if="user.assigned_role"
                  class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-accent text-white"
                >
                  {{ user.assigned_role.name }}
                </span>
                <span v-else class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                  {{ formatRoleSlug(user.role) || 'Unassigned' }}
                </span>
              </td>
              <td class="px-6 py-4 text-center text-sm text-gray-700">
                {{ user.permissions_count ?? '—' }}
              </td>
              <td class="px-6 py-4 text-center">
                <span
                  :class="user.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                  class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full"
                >
                  {{ user.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-wrap items-center justify-center gap-3 text-sm font-medium">
                  <button @click="openAssignRoleModal(user)" class="text-gray-900 hover:text-gray-700">Assign Role</button>
                  <button @click="viewUserPermissions(user)" class="text-blue-600 hover:text-blue-800">Permissions</button>
                  <button @click="toggleActive(user)" class="text-amber-700 hover:text-amber-900">
                    {{ user.is_active ? 'Deactivate' : 'Activate' }}
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="pagination.last_page > 1" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <p class="text-sm text-gray-600">
          Showing {{ pagination.from }}–{{ pagination.to }} of {{ pagination.total }}
        </p>
        <div class="flex gap-2">
          <button
            @click="fetchUsers(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="px-4 py-2 text-sm font-medium rounded-lg disabled:opacity-50 bg-accent text-white hover:bg-accent-dark"
          >
            Previous
          </button>
          <button
            @click="fetchUsers(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="px-4 py-2 text-sm font-medium rounded-lg disabled:opacity-50 bg-accent text-white hover:bg-accent-dark"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Assign Role Modal -->
    <div v-if="showAssignRoleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
          <div>
            <h3 class="text-lg font-bold text-gray-900">Assign Role</h3>
            <p class="text-sm text-gray-500">{{ selectedUser?.name }} · {{ selectedUser?.email }}</p>
          </div>
          <button @click="closeAssignRoleModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>

        <form @submit.prevent="assignRole" class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Role *</label>
            <select
              v-model="selectedRoleId"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"
              @change="loadRolePermissions(selectedRoleId)"
            >
              <option value="">Select a role</option>
              <option v-for="role in availableRoles" :key="role.id" :value="role.id">
                {{ role.name }} ({{ role.permissions?.length || 0 }} permissions)
              </option>
            </select>
          </div>

          <div v-if="selectedRoleId" class="border border-gray-200 rounded-lg p-4 max-h-56 overflow-y-auto bg-gray-50">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Permission Preview</p>
            <div v-if="Object.keys(selectedRolePermissions).length" class="space-y-3">
              <div v-for="(perms, module) in selectedRolePermissions" :key="module">
                <p class="text-sm font-semibold text-gray-900 capitalize mb-1">{{ module }}</p>
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="perm in perms"
                    :key="perm.id"
                    class="px-2 py-0.5 text-xs rounded-full bg-white border border-gray-200 text-gray-700"
                  >
                    {{ perm.name }}
                  </span>
                </div>
              </div>
            </div>
            <p v-else class="text-sm text-gray-500">This role has no permissions (or is super admin with full access).</p>
          </div>

          <div class="flex justify-end gap-3 pt-2">
            <button
              v-if="selectedUser?.assigned_role || selectedUser?.role"
              type="button"
              @click="removeRole"
              :disabled="saving"
              class="px-4 py-2 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg disabled:opacity-50"
            >
              Reset to Employee
            </button>
            <button type="button" @click="closeAssignRoleModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">
              Cancel
            </button>
            <button
              type="submit"
              :disabled="saving || !selectedRoleId"
              class="px-5 py-2 text-sm font-medium text-white bg-accent hover:bg-accent-dark rounded-lg disabled:opacity-50"
            >
              {{ saving ? 'Saving...' : 'Assign Role' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Permissions Modal -->
    <div v-if="showPermissionsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <div>
            <h3 class="text-lg font-bold text-gray-900">User Permissions</h3>
            <p class="text-sm text-gray-500">{{ selectedUser?.name }}</p>
          </div>
          <button @click="showPermissionsModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>

        <div class="px-6 py-5 overflow-y-auto space-y-5 flex-1">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
              <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Current Role</p>
              <p class="text-sm font-semibold text-gray-900">{{ userPermissionsData?.role?.name || formatRoleSlug(selectedUser?.role) || 'None' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
              <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Direct Overrides</p>
              <p class="text-sm font-semibold text-gray-900">{{ userPermissionsData?.direct_permissions?.length || 0 }}</p>
            </div>
          </div>

          <div>
            <p class="text-sm font-semibold text-gray-900 mb-2">Allowed Modules</p>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="module in userPermissionsData?.allowed_modules || []"
                :key="module"
                class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 capitalize"
              >
                {{ module }}
              </span>
              <span v-if="!(userPermissionsData?.allowed_modules || []).length" class="text-sm text-gray-500">No modules</span>
            </div>
          </div>

          <div>
            <div class="flex items-center justify-between mb-2">
              <p class="text-sm font-semibold text-gray-900">Effective Permissions</p>
              <button
                type="button"
                class="text-sm font-medium text-gray-900 hover:underline"
                @click="showGrantPanel = !showGrantPanel"
              >
                {{ showGrantPanel ? 'Hide grant panel' : 'Grant / revoke override' }}
              </button>
            </div>

            <div v-if="showGrantPanel" class="mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50 space-y-3">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Permission</label>
                <select
                  v-model="overridePermissionId"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"
                >
                  <option value="">Select permission</option>
                  <option v-for="perm in allPermissions" :key="perm.id" :value="perm.id">
                    {{ perm.module }} · {{ perm.name }}
                  </option>
                </select>
              </div>
              <div class="flex gap-2">
                <button
                  type="button"
                  :disabled="!overridePermissionId || savingOverride"
                  @click="grantOverride"
                  class="px-4 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50"
                >
                  Grant
                </button>
                <button
                  type="button"
                  :disabled="!overridePermissionId || savingOverride"
                  @click="revokeOverride"
                  class="px-4 py-2 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 disabled:opacity-50"
                >
                  Revoke
                </button>
              </div>
            </div>

            <div class="space-y-3 max-h-80 overflow-y-auto border border-gray-200 rounded-lg p-4">
              <div v-for="(permissions, module) in userPermissionsData?.grouped_permissions || {}" :key="module">
                <p class="text-sm font-semibold text-gray-900 capitalize">{{ module }}</p>
                <div class="mt-1 flex flex-wrap gap-1.5">
                  <span
                    v-for="permission in permissions"
                    :key="permission.id"
                    class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800"
                  >
                    {{ permission.name }}
                  </span>
                </div>
              </div>
              <p
                v-if="!Object.keys(userPermissionsData?.grouped_permissions || {}).length"
                class="text-sm text-gray-500 text-center py-6"
              >
                No permissions assigned
              </p>
            </div>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end">
          <button
            @click="showPermissionsModal = false"
            class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
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
import { useRoleStore } from '@/stores/role';
import { useDialog } from '@/composables/useDialog';
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';

const roleStore = useRoleStore();
const { confirm } = useDialog();
const { success, error: showError } = useNotification();

const users = ref([]);
const availableRoles = ref([]);
const allPermissions = ref([]);
const loading = ref(false);
const saving = ref(false);
const savingOverride = ref(false);
const error = ref(null);
const showAssignRoleModal = ref(false);
const showPermissionsModal = ref(false);
const showGrantPanel = ref(false);
const selectedUser = ref(null);
const selectedRoleId = ref('');
const selectedRolePermissions = ref({});
const userPermissionsData = ref(null);
const overridePermissionId = ref('');
const statsSnapshot = ref({ total: 0, withRole: 0, active: 0, inactive: 0 });

const filters = ref({
  search: '',
  role_id: '',
  is_active: '',
});

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0,
});

let searchTimer = null;

const stats = computed(() => statsSnapshot.value);

const formatRoleSlug = (slug) => {
  if (!slug) return '';
  return String(slug).replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
};

const debounceSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => fetchUsers(1), 350);
};

const resetFilters = () => {
  filters.value = { search: '', role_id: '', is_active: '' };
  fetchUsers(1);
};

const fetchUsers = async (page = pagination.value.current_page || 1) => {
  loading.value = true;
  error.value = null;
  try {
    const params = {
      page,
      per_page: 15,
      search: filters.value.search || undefined,
      role_id: filters.value.role_id || undefined,
      is_active: filters.value.is_active === '' ? undefined : filters.value.is_active,
    };
    const response = await axios.get('/users', { params });
    users.value = response.data.data || [];
    pagination.value = {
      current_page: response.data.current_page || 1,
      last_page: response.data.last_page || 1,
      per_page: response.data.per_page || 15,
      total: response.data.total || 0,
      from: response.data.from || 0,
      to: response.data.to || 0,
    };
    if (response.data.stats) {
      statsSnapshot.value = {
        total: response.data.stats.total || 0,
        withRole: response.data.stats.with_role || 0,
        active: response.data.stats.active || 0,
        inactive: response.data.stats.inactive || 0,
      };
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load users';
    users.value = [];
  } finally {
    loading.value = false;
  }
};

const fetchRoles = async () => {
  try {
    const response = await roleStore.fetchRoles({ is_active: true });
    availableRoles.value = response.data || [];
  } catch (err) {
    console.error('Error fetching roles:', err);
  }
};

const fetchPermissionsCatalog = async () => {
  try {
    const response = await axios.get('/permissions');
    allPermissions.value = response.data.data || [];
  } catch (err) {
    console.error('Error fetching permissions:', err);
  }
};

const openAssignRoleModal = async (user) => {
  selectedUser.value = user;
  selectedRoleId.value = user.role_id || '';
  selectedRolePermissions.value = {};
  if (selectedRoleId.value) {
    await loadRolePermissions(selectedRoleId.value);
  }
  showAssignRoleModal.value = true;
};

const closeAssignRoleModal = () => {
  showAssignRoleModal.value = false;
  selectedUser.value = null;
  selectedRoleId.value = '';
  selectedRolePermissions.value = {};
};

const loadRolePermissions = async (roleId) => {
  if (!roleId) {
    selectedRolePermissions.value = {};
    return;
  }
  try {
    const response = await roleStore.fetchRole(roleId);
    selectedRolePermissions.value = response.grouped_permissions || {};
  } catch (err) {
    console.error('Error loading role permissions:', err);
    selectedRolePermissions.value = {};
  }
};

const assignRole = async () => {
  saving.value = true;
  try {
    await roleStore.assignRoleToUser(selectedUser.value.id, selectedRoleId.value);
    success('Role assigned successfully');
    closeAssignRoleModal();
    await fetchUsers();
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to assign role');
  } finally {
    saving.value = false;
  }
};

const removeRole = async () => {
  if (!(await confirm({
    title: 'Reset role?',
    message: `Reset ${selectedUser.value.name} to the Employee role?`,
    confirmText: 'Reset',
    cancelText: 'Cancel',
    variant: 'danger',
  }))) {
    return;
  }

  saving.value = true;
  try {
    await roleStore.removeRoleFromUser(selectedUser.value.id);
    success('Role reset to Employee');
    closeAssignRoleModal();
    await fetchUsers();
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to reset role');
  } finally {
    saving.value = false;
  }
};

const viewUserPermissions = async (user) => {
  selectedUser.value = user;
  showGrantPanel.value = false;
  overridePermissionId.value = '';
  try {
    userPermissionsData.value = await roleStore.getUserPermissions(user.id);
    showPermissionsModal.value = true;
    if (!allPermissions.value.length) {
      await fetchPermissionsCatalog();
    }
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to load user permissions');
  }
};

const grantOverride = async () => {
  savingOverride.value = true;
  try {
    await roleStore.grantPermissionToUser(selectedUser.value.id, overridePermissionId.value);
    success('Permission granted');
    userPermissionsData.value = await roleStore.getUserPermissions(selectedUser.value.id);
    await fetchUsers(pagination.value.current_page);
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to grant permission');
  } finally {
    savingOverride.value = false;
  }
};

const revokeOverride = async () => {
  savingOverride.value = true;
  try {
    await roleStore.revokePermissionFromUser(selectedUser.value.id, overridePermissionId.value);
    success('Permission revoked');
    userPermissionsData.value = await roleStore.getUserPermissions(selectedUser.value.id);
    await fetchUsers(pagination.value.current_page);
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to revoke permission');
  } finally {
    savingOverride.value = false;
  }
};

const toggleActive = async (user) => {
  const action = user.is_active ? 'deactivate' : 'activate';
  if (!(await confirm({
    title: `${action === 'deactivate' ? 'Deactivate' : 'Activate'} user?`,
    message: `Are you sure you want to ${action} ${user.name}?`,
    confirmText: action === 'deactivate' ? 'Deactivate' : 'Activate',
    cancelText: 'Cancel',
    variant: action === 'deactivate' ? 'danger' : 'success',
  }))) {
    return;
  }

  try {
    await axios.post(`/users/${user.id}/toggle-active`);
    success(`User ${action}d successfully`);
    await fetchUsers(pagination.value.current_page);
  } catch (err) {
    showError(err.response?.data?.message || `Failed to ${action} user`);
  }
};

onMounted(async () => {
  await Promise.all([fetchRoles(), fetchUsers(1)]);
});
</script>
