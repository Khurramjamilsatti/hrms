import { defineStore } from 'pinia'
import axios from 'axios'

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
      if (state.isSuperAdmin) return true
      return state.allowedModules.includes(module)
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
        
        this.permissions = data.permissions
        this.groupedPermissions = data.grouped_permissions
        this.allowedModules = data.allowed_modules
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
