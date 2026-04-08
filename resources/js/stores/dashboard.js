import { defineStore } from 'pinia';
import axios from 'axios';

export const useDashboardStore = defineStore('dashboard', {
  state: () => ({
    stats: null,
    loading: false,
    error: null,
  }),

  actions: {
    async fetchDashboardData() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get('/dashboard');
        this.stats = response.data;
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch dashboard data';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchStats(year) {
      try {
        const response = await axios.get('/dashboard/stats', { params: { year } });
        return response.data;
      } catch (error) {
        throw error;
      }
    },
  },
});
