<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold text-gray-900">User Role Management</h1>
      <p class="mt-2 text-sm text-gray-700">
        Assign roles and permissions to users
      </p>
    </div>

    <!-- Users Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search users..."
          class="px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
        />
      </div>

      <div v-if="loading" class="p-6 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                User
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Current Role
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in filteredUsers" :key="user.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                  <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  v-if="user.assigned_role"
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800"
                >
                  {{ user.assigned_role.name }}
                </span>
                <span v-else class="text-sm text-gray-500">No role assigned</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                    user.is_active
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ user.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  @click="openAssignRoleModal(user)"
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  Assign Role
                </button>
                <button
                  @click="viewUserPermissions(user)"
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  View Permissions
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Assign Role Modal -->
    <div
      v-if="showAssignRoleModal"
      class="fixed z-10 inset-0 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeAssignRoleModal"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <form @submit.prevent="assignRole">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Assign Role to {{ selectedUser?.name }}
              </h3>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Role</label>
                <select
                  v-model="selectedRoleId"
                  required
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                  <option value="">-- Select a role --</option>
                  <option v-for="role in availableRoles" :key="role.id" :value="role.id">
                    {{ role.name }}
                  </option>
                </select>
              </div>

              <div v-if="selectedRoleId" class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Role Permissions Preview</label>
                <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-md p-4">
                  <div v-for="(perms, module) in selectedRolePermissions" :key="module" class="mb-2">
                    <h4 class="font-medium text-gray-900 capitalize text-sm">{{ module }}</h4>
                    <div class="ml-2 flex flex-wrap gap-1">
                      <span
                        v-for="perm in perms"
                        :key="perm.id"
                        class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-800"
                      >
                        {{ perm.name }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="submit"
                :disabled="saving || !selectedRoleId"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                {{ saving ? 'Assigning...' : 'Assign Role' }}
              </button>
              <button
                v-if="selectedUser?.assigned_role"
                type="button"
                @click="removeRole"
                :disabled="saving"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-red-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
              >
                Remove Role
              </button>
              <button
                type="button"
                @click="closeAssignRoleModal"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- View User Permissions Modal -->
    <div
      v-if="showPermissionsModal"
      class="fixed z-10 inset-0 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showPermissionsModal = false"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Permissions for {{ selectedUser?.name }}
            </h3>

            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Current Role</label>
                <p class="mt-1 text-sm text-gray-900">
                  {{ userPermissionsData?.role?.name || 'No role assigned' }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Allowed Modules</label>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="module in userPermissionsData?.allowed_modules"
                    :key="module"
                    class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800 capitalize"
                  >
                    {{ module }}
                  </span>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">All Permissions</label>
                <div class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-md p-4">
                  <div v-for="(permissions, module) in userPermissionsData?.grouped_permissions" :key="module">
                    <h4 class="font-medium text-gray-900 capitalize">{{ module }}</h4>
                    <div class="ml-4 mt-1 flex flex-wrap gap-2">
                      <span
                        v-for="permission in permissions"
                        :key="permission.id"
                        class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800"
                      >
                        {{ permission.name }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="showPermissionsModal = false"
              class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoleStore } from '../../../stores/role'
import axios from 'axios'

const roleStore = useRoleStore()

const users = ref([])
const availableRoles = ref([])
const loading = ref(false)
const saving = ref(false)
const searchQuery = ref('')
const showAssignRoleModal = ref(false)
const showPermissionsModal = ref(false)
const selectedUser = ref(null)
const selectedRoleId = ref('')
const selectedRolePermissions = ref({})
const userPermissionsData = ref(null)

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value

  const query = searchQuery.value.toLowerCase()
  return users.value.filter(user =>
    user.name.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query)
  )
})

const fetchUsers = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/employees/all')
    users.value = response.data.data
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    loading.value = false
  }
}

const fetchRoles = async () => {
  try {
    const response = await roleStore.fetchRoles({ is_active: true })
    availableRoles.value = response.data
  } catch (error) {
    console.error('Error fetching roles:', error)
  }
}

const openAssignRoleModal = async (user) => {
  selectedUser.value = user
  selectedRoleId.value = user.role_id || ''
  if (selectedRoleId.value) {
    await loadRolePermissions(selectedRoleId.value)
  }
  showAssignRoleModal.value = true
}

const closeAssignRoleModal = () => {
  showAssignRoleModal.value = false
  selectedUser.value = null
  selectedRoleId.value = ''
  selectedRolePermissions.value = {}
}

const loadRolePermissions = async (roleId) => {
  try {
    const response = await roleStore.fetchRole(roleId)
    selectedRolePermissions.value = response.grouped_permissions || {}
  } catch (error) {
    console.error('Error loading role permissions:', error)
  }
}

const assignRole = async () => {
  saving.value = true
  try {
    await roleStore.assignRoleToUser(selectedUser.value.id, selectedRoleId.value)
    alert('Role assigned successfully!')
    closeAssignRoleModal()
    await fetchUsers()
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to assign role')
  } finally {
    saving.value = false
  }
}

const removeRole = async () => {
  if (!confirm(`Are you sure you want to remove the role from ${selectedUser.value.name}?`)) {
    return
  }

  saving.value = true
  try {
    await roleStore.removeRoleFromUser(selectedUser.value.id)
    alert('Role removed successfully!')
    closeAssignRoleModal()
    await fetchUsers()
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to remove role')
  } finally {
    saving.value = false
  }
}

const viewUserPermissions = async (user) => {
  selectedUser.value = user
  try {
    userPermissionsData.value = await roleStore.getUserPermissions(user.id)
    showPermissionsModal.value = true
  } catch (error) {
    alert('Failed to load user permissions')
  }
}

onMounted(() => {
  fetchUsers()
  fetchRoles()
})
</script>
