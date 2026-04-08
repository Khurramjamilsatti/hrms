import { defineStore } from 'pinia';
import axios from 'axios';

export const useEmployeeStore = defineStore('employee', {
  state: () => ({
    employees: [],
    employee: null,
    loading: false,
    error: null,
    pagination: null,
  }),

  actions: {
    async fetchEmployees(params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get('/employees', { params });
        this.employees = response.data.data;
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          per_page: response.data.per_page,
          total: response.data.total,
        };
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch employees';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchEmployee(id) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get(`/employees/${id}`);
        this.employee = response.data;
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch employee';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async createEmployee(data) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.post('/employees', data);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create employee';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateEmployee(id, data) {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.put(`/employees/${id}`, data);
        return response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update employee';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async deleteEmployee(id) {
      this.loading = true;
      this.error = null;
      try {
        await axios.delete(`/employees/${id}`);
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete employee';
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
});
