import { defineStore } from 'pinia';
import axios from 'axios';

export const useCmsAuthStore = defineStore('cmsAuth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('cms_user') || 'null'),
    token: localStorage.getItem('cms_auth_token') || null,
    loaded: false,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
  },

  actions: {
    async login(credentials) {
      const { data } = await axios.post('/cms/login', credentials);
      this.token = data.token;
      this.user = data.user;
      localStorage.setItem('cms_auth_token', data.token);
      localStorage.setItem('cms_user', JSON.stringify(data.user));
      this.loaded = true;
      return data;
    },

    async fetchMe() {
      if (!this.token) {
        this.loaded = true;
        return null;
      }
      try {
        const { data } = await axios.get('/cms/me');
        this.user = data.user;
        localStorage.setItem('cms_user', JSON.stringify(data.user));
        this.loaded = true;
        return data.user;
      } catch (error) {
        this.clearSession();
        this.loaded = true;
        throw error;
      }
    },

    async logout() {
      try {
        if (this.token) {
          await axios.post('/cms/logout');
        }
      } catch (_) {
        // ignore
      } finally {
        this.clearSession();
      }
    },

    clearSession() {
      this.token = null;
      this.user = null;
      localStorage.removeItem('cms_auth_token');
      localStorage.removeItem('cms_user');
    },
  },
});
