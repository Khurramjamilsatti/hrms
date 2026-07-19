import { defineStore } from 'pinia'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

/**
 * Default modules for system roles when RBAC permissions are missing/empty.
 * Kept in sync with RolesAndPermissionsSeeder role definitions.
 */
const ROLE_DEFAULT_MODULES = {
  manager: [
    'dashboard', 'employees', 'attendance', 'leaves', 'short_leaves', 'overtime', 'payroll',
    'departments', 'performance', 'assets', 'announcements', 'timesheets',
    'training', 'travel', 'shifts', 'helpdesk', 'files', 'calendar',
    'organization', 'loans', 'salary_advances', 'notifications',
  ],
  section_head: [
    'dashboard', 'employees', 'attendance', 'leaves', 'short_leaves', 'overtime',
    'departments', 'performance', 'announcements', 'timesheets',
    'training', 'travel', 'shifts', 'helpdesk', 'files', 'calendar',
    'organization', 'notifications',
  ],
  employee: [
    'dashboard', 'employees', 'attendance', 'leaves', 'short_leaves', 'overtime', 'payroll',
    'performance', 'announcements', 'timesheets', 'training', 'travel',
    'shifts', 'helpdesk', 'files', 'calendar', 'organization', 'loans',
    'salary_advances', 'notifications',
  ],
  admin: null, // null = all modules from permissions / unrestricted fallback below
  hr_admin: null,
  super_admin: null,
}

export const usePermissionStore = defineStore('permission', {
  state: () => ({
    permissions: [],
    groupedPermissions: {},
    allowedModules: [],
    role: null,
    isSuperAdmin: false,
    loaded: false,
    loading: false,
    error: null,
  }),

  getters: {
    hasPermission: (state) => (permissionSlug) => {
      if (state.isSuperAdmin) return true
      return state.permissions.some(p => p.slug === permissionSlug)
    },

    hasAnyPermission: (state) => (permissionSlugs) => {
      if (state.isSuperAdmin) return true
      return permissionSlugs.some(slug =>
        state.permissions.some(p => p.slug === slug)
      )
    },

    hasAllPermissions: (state) => (permissionSlugs) => {
      if (state.isSuperAdmin) return true
      return permissionSlugs.every(slug =>
        state.permissions.some(p => p.slug === slug)
      )
    },

    canAccessModule: (state) => (module) => {
      // Announcements are visible to every authenticated user
      if (module === 'announcements') return true
      if (state.isSuperAdmin) return true
      if (state.allowedModules.includes(module)) return true

      // Fallback when role permissions were never synced (empty allowed_modules)
      if (state.loaded && state.allowedModules.length === 0 && state.permissions.length === 0) {
        const authStore = useAuthStore()
        const roleSlug = state.role?.slug || authStore.user?.role
        if (!roleSlug) return false
        if (['admin', 'hr_admin', 'super_admin'].includes(roleSlug)) return true
        return ROLE_DEFAULT_MODULES[roleSlug]?.includes(module) ?? false
      }

      return false
    },

    getModulePermissions: (state) => (module) => {
      return state.groupedPermissions[module] || []
    },
  },

  actions: {
    async fetchMyPermissions() {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get('/my-permissions')
        const data = response.data.data

        this.permissions = data.permissions || []
        this.groupedPermissions = data.grouped_permissions || {}
        this.allowedModules = data.allowed_modules || []
        this.role = data.role
        this.isSuperAdmin = data.is_super_admin
        this.loaded = true

        return data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch permissions'
        throw error
      } finally {
        this.loading = false
      }
    },

    async checkPermission(permissionSlug) {
      try {
        const response = await axios.post('/check-permission', {
          permission: permissionSlug
        })
        return response.data.data.has_permission
      } catch (error) {
        console.error('Error checking permission:', error)
        return false
      }
    },

    clearPermissions() {
      this.permissions = []
      this.groupedPermissions = {}
      this.allowedModules = []
      this.role = null
      this.isSuperAdmin = false
      this.loaded = false
    },
  },
})
