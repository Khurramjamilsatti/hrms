<template>
  <div>
    <div class="mb-6">
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Recruitment</h2>
        <button @click="showCreatePosition = true" class="btn btn-primary">
          Post New Job
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
      <div class="card">
        <h3 class="text-lg font-semibold mb-2">Open Positions</h3>
        <p class="text-3xl font-bold text-primary-600">{{ stats.openPositions }}</p>
      </div>
      <div class="card">
        <h3 class="text-lg font-semibold mb-2">Pending Applications</h3>
        <p class="text-3xl font-bold text-yellow-600">{{ stats.pendingApplications }}</p>
      </div>
    </div>

    <div class="card">
      <div class="mb-4">
        <ul class="flex border-b">
          <li class="-mb-px mr-1">
            <button 
              @click="activeTab = 'positions'" 
              :class="['inline-block py-2 px-4 font-semibold', activeTab === 'positions' ? 'border-b-2 border-primary-500 text-primary-600' : 'text-gray-600']"
            >
              Job Positions
            </button>
          </li>
          <li class="mr-1">
            <button 
              @click="activeTab = 'applications'" 
              :class="['inline-block py-2 px-4 font-semibold', activeTab === 'applications' ? 'border-b-2 border-primary-500 text-primary-600' : 'text-gray-600']"
            >
              Applications
            </button>
          </li>
        </ul>
      </div>

      <div v-if="activeTab === 'positions'">
        <table class="table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Department</th>
              <th>Type</th>
              <th>Positions</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="position in positions" :key="position.id">
              <td>{{ position.title }}</td>
              <td>{{ position.department?.name }}</td>
              <td>{{ position.employment_type }}</td>
              <td>{{ position.positions_available }}</td>
              <td>
                <span :class="getStatusBadge(position.status)">
                  {{ position.status }}
                </span>
              </td>
              <td>
                <button @click="viewPosition(position)" class="text-blue-600 hover:underline">
                  View
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="activeTab === 'applications'">
        <table class="table">
          <thead>
            <tr>
              <th>Applicant</th>
              <th>Position</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="application in applications" :key="application.id">
              <td>{{ application.applicant_name }}</td>
              <td>{{ application.job_position?.title }}</td>
              <td>{{ application.email }}</td>
              <td>{{ application.phone }}</td>
              <td>
                <span :class="getApplicationStatusBadge(application.status)">
                  {{ application.status }}
                </span>
              </td>
              <td>
                <button @click="viewApplication(application)" class="text-blue-600 hover:underline">
                  Review
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const activeTab = ref('positions');
const positions = ref([]);
const applications = ref([]);
const stats = ref({ openPositions: 0, pendingApplications: 0 });
const showCreatePosition = ref(false);

const loadData = async () => {
  try {
    const [positionsRes, applicationsRes] = await Promise.all([
      axios.get('/recruitment/positions'),
      axios.get('/recruitment/applications')
    ]);
    
    positions.value = positionsRes.data.data;
    applications.value = applicationsRes.data.data;
    
    stats.value.openPositions = positions.value.filter(p => p.status === 'open').length;
    stats.value.pendingApplications = applications.value.filter(a => a.status === 'applied').length;
  } catch (error) {
    console.error('Failed to load recruitment data:', error);
  }
};

const getStatusBadge = (status) => {
  const badges = {
    draft: 'badge badge-secondary',
    open: 'badge badge-success',
    closed: 'badge badge-danger',
  };
  return badges[status] || 'badge';
};

const getApplicationStatusBadge = (status) => {
  const badges = {
    applied: 'badge badge-info',
    screening: 'badge badge-warning',
    interview: 'badge badge-primary',
    offered: 'badge badge-success',
    hired: 'badge badge-success',
    rejected: 'badge badge-danger',
  };
  return badges[status] || 'badge';
};

const viewPosition = (position) => {
  alert(`View job position: ${position.title}`);
};

const viewApplication = (application) => {
  alert(`Review application from: ${application.applicant_name}`);
};

onMounted(() => {
  loadData();
});
</script>
