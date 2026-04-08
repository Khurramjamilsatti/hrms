import { defineStore } from 'pinia'
import axios from 'axios'

export const useRoleStore = defineStore('role', {
  state: () => ({
    roles: [],
    currentRole: null,
    permissions: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchRoles(params = {}) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/roles', { params })
        this.roles = response.data.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch roles'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchRole(roleId) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`/roles/${roleId}`)
        this.currentRole = response.data.data.role
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch role'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createRole(roleData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post('/roles', roleData)
        this.roles.push(response.data.data)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create role'
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateRole(roleId, roleData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.put(`/roles/${roleId}`, roleData)
        const index = this.roles.findIndex(r => r.id === roleId)
        if (index !== -1) {
          this.roles[index] = response.data.data
        }
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update role'
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteRole(roleId) {
      this.loading = true
      this.error = null
      
      try {
        await axios.delete(`/roles/${roleId}`)
        this.roles = this.roles.filter(r => r.id !== roleId)
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete role'
        throw error
      } finally {
        this.loading = false
      }
    },

    async syncRolePermissions(roleId, permissionIds) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post(`/roles/${roleId}/permissions/sync`, {
          permission_ids: permissionIds
        })
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to sync permissions'
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchAllPermissions(params = {}) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/permissions', { params })
        this.permissions = response.data.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch permissions'
        throw error
      } finally {
        this.loading = false
      }
    },

    async assignRoleToUser(userId, roleId) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post(`/users/${userId}/assign-role`, {
          role_id: roleId
        })
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to assign role'
        throw error
      } finally {
        this.loading = false
      }
    },

    async removeRoleFromUser(userId) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.delete(`/users/${userId}/remove-role`)
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to remove role'
        throw error
      } finally {
        this.loading = false
      }
    },

    async grantPermissionToUser(userId, permissionId) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post(`/users/${userId}/grant-permission`, {
          permission_id: permissionId
        })
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to grant permission'
        throw error
      } finally {
        this.loading = false
      }
    },

    async revokePermissionFromUser(userId, permissionId) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post(`/users/${userId}/revoke-permission`, {
          permission_id: permissionId
        })
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to revoke permission'
        throw error
      } finally {
        this.loading = false
      }
    },

    async getUserPermissions(userId) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`/users/${userId}/permissions`)
        return response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch user permissions'
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})
