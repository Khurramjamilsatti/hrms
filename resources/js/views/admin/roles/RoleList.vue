<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Roles & Permissions</h1>
      <button
        @click="showCreateModal = true"
        class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Create Role
      </button>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="flex items-center space-x-3">
        <div class="flex-1 relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search roles by name, slug, description..."
            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
          />
        </div>
      </div>
    </div>

    <div v-if="loading" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
      <div class="text-gray-600">Loading roles...</div>
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-100 border-b border-gray-300">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Role Name
              </th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Description
              </th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Permissions
              </th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="role in filteredRoles" :key="role.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div>
                    <div class="text-sm font-semibold text-gray-900">{{ role.name }}</div>
                    <div class="text-sm text-gray-500">{{ role.slug }}</div>
                  </div>
                  <span
                    v-if="role.is_system_role"
                    class="ml-2 px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-700"
                  >
                    System
                  </span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-600">{{ role.description || '-' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-600">
                  {{ role.permissions?.length || 0 }} permissions
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span
                  :class="[
                    'inline-flex px-2.5 py-1 text-xs font-semibold rounded-full',
                    role.is_active
                      ? 'bg-green-100 text-green-700'
                      : 'bg-red-100 text-red-700'
                  ]"
                >
                  {{ role.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                <button
                  @click="viewRole(role)"
                  class="text-blue-600 hover:text-blue-900 mr-4 font-medium"
                >
                  View
                </button>
                <button
                  @click="editRole(role)"
                  :disabled="role.is_system_role"
                  :class="[
                    'mr-4 font-medium',
                    role.is_system_role
                      ? 'text-gray-400 cursor-not-allowed'
                      : 'text-gray-900 hover:text-gray-700'
                  ]"
                >
                  Edit
                </button>
                <button
                  @click="deleteRole(role)"
                  :disabled="role.is_system_role"
                  :class="[
                    'font-medium',
                    role.is_system_role
                      ? 'text-gray-400 cursor-not-allowed'
                      : 'text-red-600 hover:text-red-900'
                  ]"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Role Modal -->
    <div
      v-if="showCreateModal || showEditModal"
      class="fixed z-50 inset-0 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" @click="closeModal"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-200">
          <form @submit.prevent="saveRole">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-gray-900 to-gray-800 px-6 py-4 border-b border-gray-700">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0 w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-xl font-bold text-white">
                      {{ showEditModal ? 'Edit Role' : 'Create New Role' }}
                    </h3>
                    <p class="text-sm text-gray-300 mt-0.5">
                      {{ showEditModal ? 'Update role details and permissions' : 'Define role details and assign permissions' }}
                    </p>
                  </div>
                </div>
                <button
                  type="button"
                  @click="closeModal"
                  class="text-gray-300 hover:text-white transition-colors rounded-lg p-2 hover:bg-white hover:bg-opacity-10"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Modal Body -->
            <div class="bg-white px-6 py-6">
              <!-- Role Basic Info Section -->
              <div class="mb-8">
                <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center">
                  <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Basic Information
                </h4>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 bg-gray-50 p-5 rounded-lg border border-gray-200">
                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      Role Name <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="form.name"
                      type="text"
                      required
                      placeholder="e.g., HR Manager"
                      class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900 focus:ring-opacity-50 text-sm transition-colors bg-white"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      Slug
                    </label>
                    <input
                      v-model="form.slug"
                      type="text"
                      placeholder="auto-generated-from-name"
                      class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900 focus:ring-opacity-50 text-sm transition-colors bg-white"
                    />
                    <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate</p>
                  </div>

                  <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                      Description
                    </label>
                    <textarea
                      v-model="form.description"
                      rows="3"
                      placeholder="Brief description of role responsibilities..."
                      class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900 focus:ring-opacity-50 text-sm transition-colors bg-white resize-none"
                    ></textarea>
                  </div>

                  <div class="sm:col-span-2">
                    <label class="flex items-center cursor-pointer group">
                      <input
                        v-model="form.is_active"
                        type="checkbox"
                        class="rounded border-gray-300 text-gray-900 shadow-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900 focus:ring-offset-0 h-5 w-5 cursor-pointer"
                      />
                      <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-gray-900">
                        Active Status
                        <span class="block text-xs text-gray-500 font-normal">Role is active and can be assigned to users</span>
                      </span>
                    </label>
                  </div>
                </div>
              </div>

              <!-- Permissions Selection -->
              <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4 flex items-center">
                  <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                  Permissions Assignment
                </h4>
                <div class="bg-gray-50 border border-gray-200 rounded-lg">
                  <div class="p-4 border-b border-gray-200 bg-white rounded-t-lg">
                    <div class="flex items-start space-x-3">
                      <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <div>
                        <p class="text-sm font-medium text-gray-900 mb-1">Permission Assignment Guide</p>
                        <p class="text-sm text-gray-600">
                          Select individual permissions or click module names to toggle all actions within that module. Each module represents a system feature with specific actions (view, create, edit, delete, etc.).
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="p-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <!-- Loading State -->
                    <div v-if="loadingPermissions" class="text-center py-8">
                      <svg class="animate-spin h-10 w-10 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      <p class="text-gray-600 font-medium">Loading permissions...</p>
                    </div>
                    
                    <!-- Empty State -->
                    <div v-else-if="Object.keys(groupedPermissions).length === 0" class="text-center py-8 text-gray-500">
                      <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                      </svg>
                      <p class="font-medium">No permissions available</p>
                      <p class="text-sm mt-1">Permissions need to be created first</p>
                    </div>
                    
                    <!-- Permissions List -->
                    <div v-else class="space-y-4">
                      <div 
                        v-for="(permissions, module) in groupedPermissions" 
                        :key="module" 
                        class="bg-white border-2 border-gray-200 rounded-lg overflow-hidden hover:border-gray-300 transition-all"
                      >
                        <!-- Module Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-3 border-b-2 border-gray-200">
                          <div class="flex items-center justify-between">
                            <label 
                              :for="`module-${module}`" 
                              class="flex items-center cursor-pointer flex-1"
                            >
                              <input
                                :id="`module-${module}`"
                                type="checkbox"
                                :checked="isModuleSelected(module)"
                                @change="toggleModule(module)"
                                class="rounded border-gray-400 text-gray-900 shadow-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900 focus:ring-offset-0 h-5 w-5"
                              />
                              <div class="ml-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                <div>
                                  <span class="font-bold text-gray-900 capitalize text-base">{{ formatModuleName(module) }}</span>
                                  <span class="ml-2 text-xs text-gray-500 font-normal">
                                    ({{ permissions.length }} {{ permissions.length === 1 ? 'action' : 'actions' }})
                                  </span>
                                </div>
                              </div>
                            </label>
                            <button
                              type="button"
                              @click="toggleModuleExpand(module)"
                              class="p-1 hover:bg-gray-200 rounded transition-colors"
                            >
                              <svg 
                                class="w-5 h-5 text-gray-600 transition-transform"
                                :class="{ 'rotate-180': !collapsedModules[module] }"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                              >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                              </svg>
                            </button>
                          </div>
                        </div>
                        
                        <!-- Permissions List -->
                        <div 
                          v-show="!collapsedModules[module]"
                          class="p-4 bg-white"
                        >
                          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            <label
                              v-for="permission in permissions"
                              :key="permission.id"
                              class="flex items-start p-3 rounded-lg border border-gray-200 hover:border-gray-300 hover:bg-gray-50 cursor-pointer transition-all group"
                            >
                              <input
                                v-model="form.permission_ids"
                                :value="permission.id"
                                type="checkbox"
                                class="mt-0.5 rounded border-gray-300 text-gray-900 shadow-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900 focus:ring-offset-0 h-4 w-4"
                              />
                              <div class="ml-3 flex-1">
                                <div class="flex items-center">
                                  <span class="text-sm font-medium text-gray-900 group-hover:text-gray-900">
                                    {{ formatActionName(permission.action) }}
                                  </span>
                                  <span 
                                    class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full"
                                    :class="getActionBadgeClass(permission.action)"
                                  >
                                    {{ permission.action }}
                                  </span>
                                </div>
                                <p v-if="permission.description" class="text-xs text-gray-500 mt-1">
                                  {{ permission.description }}
                                </p>
                              </div>
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
              <button
                type="button"
                @click="closeModal"
                class="inline-flex items-center px-5 py-2.5 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <svg v-if="!saving" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ saving ? 'Saving...' : (showEditModal ? 'Update Role' : 'Create Role') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- View Role Modal -->
    <div
      v-if="showViewModal"
      class="fixed z-10 inset-0 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showViewModal = false"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Role Details
            </h3>

            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <p class="mt-1 text-sm text-gray-900">{{ viewingRole?.name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Slug</label>
                <p class="mt-1 text-sm text-gray-900">{{ viewingRole?.slug }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <p class="mt-1 text-sm text-gray-900">{{ viewingRole?.description || '-' }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <span
                  :class="[
                    'mt-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                    viewingRole?.is_active
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ viewingRole?.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Permissions</label>
                <div v-if="!viewingRole?.grouped_permissions || Object.keys(viewingRole.grouped_permissions).length === 0" 
                     class="text-sm text-gray-500 italic">
                  No permissions assigned
                </div>
                <div v-else class="space-y-3">
                  <div v-for="(permissions, module) in viewingRole?.grouped_permissions" :key="module" 
                       class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                      <h4 class="font-semibold text-gray-900 capitalize flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        {{ formatModuleName(module) }}
                        <span class="ml-2 text-xs font-normal text-gray-500">
                          ({{ permissions.length }} {{ permissions.length === 1 ? 'action' : 'actions' }})
                        </span>
                      </h4>
                    </div>
                    <div class="px-4 py-3 bg-white">
                      <div class="flex flex-wrap gap-2">
                        <span
                          v-for="permission in permissions"
                          :key="permission.id"
                          class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full border"
                          :class="getActionBadgeClass(permission.action)"
                        >
                          <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                          </svg>
                          {{ formatActionName(permission.action) }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="showViewModal = false"
              class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Notification Toast -->
    <div v-if="notification.show" 
        :class="notification.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
        class="fixed bottom-4 right-4 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300">
      <div class="flex items-center space-x-3">
        <svg v-if="notification.type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="font-medium">{{ notification.message }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue'
import { useRoleStore } from '../../../stores/role'

const roleStore = useRoleStore()

const roles = ref([])
const allPermissions = ref([])
const groupedPermissions = ref({})
const loading = ref(false)
const loadingPermissions = ref(false)
const saving = ref(false)
const searchQuery = ref('')
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showViewModal = ref(false)
const viewingRole = ref(null)
const editingRole = ref(null)

const form = ref({
  name: '',
  slug: '',
  description: '',
  is_active: true,
  permission_ids: []
})

const collapsedModules = ref({})

const notification = reactive({
  show: false,
  message: '',
  type: 'success'
})

// Helper functions
const formatModuleName = (module) => {
  // Convert snake_case or kebab-case to Title Case
  return module
    .replace(/[-_]/g, ' ')
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ')
}

const formatActionName = (action) => {
  // Convert action to readable format
  const actionMap = {
    'view': 'View',
    'create': 'Create',
    'edit': 'Edit',
    'update': 'Update',
    'delete': 'Delete',
    'list': 'List',
    'show': 'Show Details',
    'store': 'Store',
    'destroy': 'Remove',
    'approve': 'Approve',
    'reject': 'Reject',
    'manage': 'Manage',
    'assign': 'Assign',
    'export': 'Export',
    'import': 'Import',
    'download': 'Download',
    'upload': 'Upload',
    'publish': 'Publish',
    'archive': 'Archive',
  }
  
  return actionMap[action.toLowerCase()] || action.charAt(0).toUpperCase() + action.slice(1)
}

const getActionBadgeClass = (action) => {
  const lowerAction = action.toLowerCase()
  
  if (['view', 'list', 'show'].includes(lowerAction)) {
    return 'bg-blue-100 text-blue-700'
  } else if (['create', 'store', 'add'].includes(lowerAction)) {
    return 'bg-green-100 text-green-700'
  } else if (['edit', 'update'].includes(lowerAction)) {
    return 'bg-yellow-100 text-yellow-700'
  } else if (['delete', 'destroy', 'remove'].includes(lowerAction)) {
    return 'bg-red-100 text-red-700'
  } else if (['approve', 'publish'].includes(lowerAction)) {
    return 'bg-emerald-100 text-emerald-700'
  } else if (['reject', 'archive'].includes(lowerAction)) {
    return 'bg-orange-100 text-orange-700'
  } else if (['manage', 'assign'].includes(lowerAction)) {
    return 'bg-purple-100 text-purple-700'
  } else if (['export', 'download', 'import', 'upload'].includes(lowerAction)) {
    return 'bg-indigo-100 text-indigo-700'
  }
  
  return 'bg-gray-100 text-gray-700'
}

const toggleModuleExpand = (module) => {
  collapsedModules.value[module] = !collapsedModules.value[module]
}

const filteredRoles = computed(() => {
  if (!searchQuery.value) return roles.value

  const query = searchQuery.value.toLowerCase()
  return roles.value.filter(role =>
    role.name.toLowerCase().includes(query) ||
    role.slug.toLowerCase().includes(query) ||
    role.description?.toLowerCase().includes(query)
  )
})

const isModuleSelected = (module) => {
  const modulePermissionIds = groupedPermissions.value[module]?.map(p => p.id) || []
  return modulePermissionIds.every(id => form.value.permission_ids.includes(id))
}

const toggleModule = (module) => {
  const modulePermissionIds = groupedPermissions.value[module]?.map(p => p.id) || []
  const allSelected = isModuleSelected(module)

  if (allSelected) {
    // Remove all module permissions
    form.value.permission_ids = form.value.permission_ids.filter(
      id => !modulePermissionIds.includes(id)
    )
  } else {
    // Add all module permissions
    form.value.permission_ids = [
      ...new Set([...form.value.permission_ids, ...modulePermissionIds])
    ]
  }
}

const fetchRoles = async () => {
  loading.value = true
  try {
    const response = await roleStore.fetchRoles()
    roles.value = response.data
  } catch (error) {
    console.error('Error fetching roles:', error)
  } finally {
    loading.value = false
  }
}

const fetchPermissions = async () => {
  loadingPermissions.value = true
  try {
    console.log('Fetching permissions...')
    const response = await roleStore.fetchAllPermissions({ grouped: true })
    console.log('Full response object:', response)
    console.log('Response.data:', response.data)
    console.log('Response.data type:', typeof response.data)
    
    // Handle different possible response structures
    // Could be: response.data = [...] OR response.data = { data: [...] }
    let permissionsData
    if (Array.isArray(response.data)) {
      permissionsData = response.data
    } else if (response.data && Array.isArray(response.data.data)) {
      permissionsData = response.data.data
    } else {
      permissionsData = []
    }
    
    console.log('Permissions data array:', permissionsData)
    console.log('Is array?:', Array.isArray(permissionsData))
    console.log('Array length:', permissionsData.length)
    
    if (!Array.isArray(permissionsData) || permissionsData.length === 0) {
      console.warn('No permissions data available')
      allPermissions.value = []
      groupedPermissions.value = {}
      return
    }
    
    allPermissions.value = permissionsData
    
    // Group permissions by module
    const grouped = {}
    permissionsData.forEach(group => {
      if (group && group.module && group.permissions) {
        grouped[group.module] = group.permissions
      } else {
        console.warn('Invalid group structure:', group)
      }
    })
    groupedPermissions.value = grouped
    console.log('Grouped permissions:', grouped)
    console.log('Total modules:', Object.keys(grouped).length)
  } catch (error) {
    console.error('Error fetching permissions:', error)
    console.error('Error response:', error.response)
    console.error('Error details:', error.response?.data)
    showNotification('Failed to load permissions: ' + (error.response?.data?.message || error.message), 'error')
  } finally {
    loadingPermissions.value = false
  }
}

const viewRole = async (role) => {
  try {
    const response = await roleStore.fetchRole(role.id)
    viewingRole.value = response
    showViewModal.value = true
  } catch (error) {
    console.error('Error fetching role:', error)
  }
}

const editRole = (role) => {
  editingRole.value = role
  form.value = {
    name: role.name,
    slug: role.slug,
    description: role.description || '',
    is_active: role.is_active,
    permission_ids: role.permissions?.map(p => p.id) || []
  }
  showEditModal.value = true
}

const deleteRole = async (role) => {
  if (!confirm(`Are you sure you want to delete the role "${role.name}"?`)) {
    return
  }

  try {
    await roleStore.deleteRole(role.id)
    await fetchRoles()
    showNotification('Role deleted successfully!', 'success')
  } catch (error) {
    showNotification(error.response?.data?.message || 'Failed to delete role', 'error')
  }
}

const saveRole = async () => {
  saving.value = true
  try {
    if (showEditModal.value) {
      await roleStore.updateRole(editingRole.value.id, form.value)
      showNotification('Role updated successfully!', 'success')
    } else {
      await roleStore.createRole(form.value)
      showNotification('Role created successfully!', 'success')
    }
    
    closeModal()
    await fetchRoles()
  } catch (error) {
    showNotification(error.response?.data?.message || 'Failed to save role', 'error')
  } finally {
    saving.value = false
  }
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  editingRole.value = null
  form.value = {
    name: '',
    slug: '',
    description: '',
    is_active: true,
    permission_ids: []
  }
}

const showNotification = (message, type = 'success') => {
  notification.message = message
  notification.type = type
  notification.show = true
  setTimeout(() => {
    notification.show = false
  }, 3000)
}

onMounted(() => {
  fetchRoles()
  fetchPermissions()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f3f4f6;
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Smooth transitions for dropdowns */
.rotate-180 {
  transform: rotate(180deg);
}

/* Backdrop blur for modal */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}
</style>
