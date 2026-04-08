<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h3 class="text-lg font-semibold">Helpdesk Tickets</h3>
      <button @click="showForm = true" class="btn btn-primary">Create Ticket</button>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <select v-model="filters.status" class="form-input">
          <option value="">All Status</option>
          <option value="open">Open</option>
          <option value="in_progress">In Progress</option>
          <option value="resolved">Resolved</option>
          <option value="closed">Closed</option>
        </select>
        <select v-model="filters.priority" class="form-input">
          <option value="">All Priority</option>
          <option value="low">Low</option>
          <option value="medium">Medium</option>
          <option value="high">High</option>
          <option value="urgent">Urgent</option>
        </select>
        <input type="text" v-model="filters.search" placeholder="Search..." class="form-input" />
        <button @click="fetchTickets" class="btn btn-primary">Filter</button>
      </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket #</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="ticket in tickets" :key="ticket.id">
            <td class="px-6 py-4 text-sm font-medium">{{ ticket.ticket_number }}</td>
            <td class="px-6 py-4 text-sm">{{ ticket.subject }}</td>
            <td class="px-6 py-4 text-sm">{{ ticket.category?.name }}</td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs rounded-full" :class="getPriorityClass(ticket.priority)">
                {{ ticket.priority }}
              </span>
            </td>
            <td class="px-6 py-4">
              <span class="px-2 py-1 text-xs rounded-full" :class="getStatusClass(ticket.status)">
                {{ ticket.status.replace('_', ' ') }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm">{{ formatDate(ticket.created_at) }}</td>
            <td class="px-6 py-4 text-sm">
              <button @click="viewTicket(ticket)" class="text-primary-600 hover:text-primary-900">View</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create Ticket Modal -->
    <div v-if="showForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
        <h3 class="text-xl font-semibold mb-4">Create New Ticket</h3>
        
        <form @submit.prevent="createTicket">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <select v-model="form.category_id" required class="form-input">
                <option value="">Select Category</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
              <select v-model="form.priority" required class="form-input">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
              <input type="text" v-model="form.subject" required class="form-input" />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
              <textarea v-model="form.description" rows="5" required class="form-input"></textarea>
            </div>
          </div>
          
          <div class="flex justify-end space-x-3 mt-6">
            <button type="button" @click="showForm = false" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">Create Ticket</button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Ticket Modal -->
    <div v-if="selectedTicket" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-screen overflow-y-auto">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-xl font-semibold">{{ selectedTicket.ticket_number }}</h3>
            <p class="text-gray-600">{{ selectedTicket.subject }}</p>
          </div>
          <button @click="selectedTicket = null" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <div class="bg-gray-50 p-4 rounded mb-4">
          <div class="grid grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-600">Status:</span> <span class="font-medium">{{ selectedTicket.status }}</span></div>
            <div><span class="text-gray-600">Priority:</span> <span class="font-medium">{{ selectedTicket.priority }}</span></div>
            <div><span class="text-gray-600">Category:</span> <span class="font-medium">{{ selectedTicket.category?.name }}</span></div>
          </div>
        </div>

        <div class="mb-4">
          <h4 class="font-semibold mb-2">Description</h4>
          <p class="text-gray-700">{{ selectedTicket.description }}</p>
        </div>

        <div>
          <h4 class="font-semibold mb-3">Replies</h4>
          <div class="space-y-3">
            <div v-for="reply in selectedTicket.replies" :key="reply.id" class="border-l-4 pl-4 py-2" :class="reply.is_internal ? 'border-yellow-500 bg-yellow-50' : 'border-primary-500 bg-gray-50'">
              <div class="flex justify-between items-start mb-1">
                <span class="font-medium text-sm">{{ reply.replier?.name }}</span>
                <span class="text-xs text-gray-500">{{ formatDate(reply.created_at) }}</span>
              </div>
              <p class="text-sm text-gray-700">{{ reply.message }}</p>
            </div>
          </div>

          <div class="mt-4">
            <textarea v-model="replyMessage" rows="3" placeholder="Add a reply..." class="form-input mb-2"></textarea>
            <button @click="addReply" class="btn btn-primary">Add Reply</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const tickets = ref([]);
const categories = ref([]);
const showForm = ref(false);
const selectedTicket = ref(null);
const replyMessage = ref('');

const filters = reactive({
  status: '',
  priority: '',
  search: ''
});

const form = reactive({
  category_id: '',
  priority: 'medium',
  subject: '',
  description: ''
});

const fetchTickets = async () => {
  try {
    const params = new URLSearchParams();
    if (filters.status) params.append('status', filters.status);
    if (filters.priority) params.append('priority', filters.priority);
    if (filters.search) params.append('search', filters.search);
    
    const response = await axios.get(`/api/helpdesk/tickets?${params.toString()}`);
    tickets.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch tickets:', error);
  }
};

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/helpdesk/categories');
    categories.value = response.data.data || response.data;
  } catch (error) {
    console.error('Failed to fetch categories:', error);
  }
};

const createTicket = async () => {
  try {
    const data = { ...form, employee_id: authStore.user.employee?.id || authStore.user.id };
    await axios.post('/api/helpdesk/tickets', data);
    showForm.value = false;
    Object.assign(form, { category_id: '', priority: 'medium', subject: '', description: '' });
    fetchTickets();
  } catch (error) {
    console.error('Failed to create ticket:', error);
    alert('Error creating ticket');
  }
};

const viewTicket = async (ticket) => {
  try {
    const response = await axios.get(`/api/helpdesk/tickets/${ticket.id}`);
    selectedTicket.value = response.data;
  } catch (error) {
    console.error('Failed to fetch ticket details:', error);
  }
};

const addReply = async () => {
  if (!replyMessage.value.trim()) return;
  
  try {
    await axios.post(`/api/helpdesk/tickets/${selectedTicket.value.id}/replies`, {
      message: replyMessage.value,
      is_internal: false
    });
    replyMessage.value = '';
    viewTicket(selectedTicket.value);
  } catch (error) {
    console.error('Failed to add reply:', error);
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PK');
};

const getPriorityClass = (priority) => {
  const classes = {
    low: 'bg-gray-100 text-gray-800',
    medium: 'bg-blue-100 text-blue-800',
    high: 'bg-orange-100 text-orange-800',
    urgent: 'bg-red-100 text-red-800'
  };
  return classes[priority] || 'bg-gray-100 text-gray-800';
};

const getStatusClass = (status) => {
  const classes = {
    open: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-yellow-100 text-yellow-800',
    resolved: 'bg-green-100 text-green-800',
    closed: 'bg-gray-100 text-gray-800',
    reopened: 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
  fetchTickets();
  fetchCategories();
});
</script>
