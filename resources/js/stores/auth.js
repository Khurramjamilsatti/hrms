import { defineStore } from 'pinia';
import axios from 'axios';
import { usePermissionStore } from './permission';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token'),
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
    isManager: (state) => state.user?.role === 'manager',
    isEmployee: (state) => state.user?.role === 'employee',
    isSuperAdmin: (state) => state.user?.role === 'super_admin',
  },

  actions: {
    async login(credentials) {
      try {
        const response = await axios.post('/login', credentials);
        this.token = response.data.token;
        this.user = response.data.user;
        localStorage.setItem('auth_token', this.token);
        localStorage.setItem('user', JSON.stringify(this.user));
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        
        // Fetch user permissions after login
        const permissionStore = usePermissionStore();
        await permissionStore.fetchMyPermissions();
        
        return response.data;
      } catch (error) {
        throw error;
      }
    },

    async logout() {
      try {
        await axios.post('/logout');
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.user = null;
        this.token = null;
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        delete axios.defaults.headers.common['Authorization'];
        
        // Clear permissions on logout
        const permissionStore = usePermissionStore();
        permissionStore.clearPermissions();
      }
    },

    async fetchUser() {
      try {
        const response = await axios.get('/me');
        this.user = response.data;
        localStorage.setItem('user', JSON.stringify(this.user));
        
        // Fetch permissions when fetching user
        const permissionStore = usePermissionStore();
        await permissionStore.fetchMyPermissions();
        
        return response.data;
      } catch (error) {
        this.logout();
        throw error;
      }
    },

    async checkAuth() {
      const token = localStorage.getItem('auth_token');
      const user = localStorage.getItem('user');
      
      if (token && user) {
        this.token = token;
        this.user = JSON.parse(user);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        
        // Fetch permissions on app initialization
        try {
          const permissionStore = usePermissionStore();
          await permissionStore.fetchMyPermissions();
        } catch (error) {
          console.error('Failed to fetch permissions on initialization:', error);
        }
      }
    },
  },
});
