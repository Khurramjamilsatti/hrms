<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">Announcements</h2>
      <button v-if="canManage" @click="showCreateAnnouncement = true" class="btn btn-primary">
        New Announcement
      </button>
    </div>

    <div class="space-y-4">
      <div v-for="announcement in announcements" :key="announcement.id" class="card">
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <div class="flex items-center space-x-2 mb-2">
              <h3 class="text-xl font-bold">{{ announcement.title }}</h3>
              <span :class="getPriorityBadge(announcement.priority)">
                {{ announcement.priority }}
              </span>
            </div>
            <p class="text-gray-700 whitespace-pre-wrap mb-3">{{ announcement.content }}</p>
            <div class="flex items-center text-sm text-gray-600">
              <span>Posted: {{ formatDate(announcement.created_at) }}</span>
              <span v-if="announcement.expiry_date" class="ml-4">
                Expires: {{ formatDate(announcement.expiry_date) }}
              </span>
            </div>
          </div>
          <div v-if="canManage" class="ml-4 flex space-x-2">
            <button @click="editAnnouncement(announcement)" class="text-blue-600 hover:underline">
              Edit
            </button>
            <button @click="deleteAnnouncement(announcement.id)" class="text-red-600 hover:underline">
              Delete
            </button>
          </div>
        </div>
      </div>
      
      <div v-if="announcements.length === 0" class="card text-center py-8 text-gray-500">
        No announcements available
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';

const authStore = useAuthStore();
const announcements = ref([]);
const showCreateAnnouncement = ref(false);

const canManage = computed(() => authStore.isAdmin || authStore.isManager);

const loadAnnouncements = async () => {
  try {
    const response = await axios.get('/announcements');
    announcements.value = response.data.data;
  } catch (error) {
    console.error('Failed to load announcements:', error);
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PK', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const getPriorityBadge = (priority) => {
  const badges = {
    low: 'badge badge-secondary',
    medium: 'badge badge-info',
    high: 'badge badge-warning',
    urgent: 'badge badge-danger',
  };
  return badges[priority] || 'badge';
};

const editAnnouncement = (announcement) => {
  alert(`Edit announcement: ${announcement.title}`);
};

const deleteAnnouncement = async (id) => {
  if (confirm('Delete this announcement?')) {
    try {
      await axios.delete(`/announcements/${id}`);
      loadAnnouncements();
    } catch (error) {
      alert('Failed to delete announcement');
    }
  }
};

onMounted(() => {
  loadAnnouncements();
});
</script>
