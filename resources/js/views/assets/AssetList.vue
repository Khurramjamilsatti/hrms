<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">Asset Management</h2>
      <button @click="showCreateAsset = true" class="btn btn-primary">
        Add Asset
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="card">
        <h3 class="text-sm font-semibold text-gray-600 mb-2">Total Assets</h3>
        <p class="text-3xl font-bold text-primary-600">{{ stats.total }}</p>
      </div>
      <div class="card">
        <h3 class="text-sm font-semibold text-gray-600 mb-2">Available</h3>
        <p class="text-3xl font-bold text-green-600">{{ stats.available }}</p>
      </div>
      <div class="card">
        <h3 class="text-sm font-semibold text-gray-600 mb-2">Assigned</h3>
        <p class="text-3xl font-bold text-blue-600">{{ stats.assigned }}</p>
      </div>
      <div class="card">
        <h3 class="text-sm font-semibold text-gray-600 mb-2">Maintenance</h3>
        <p class="text-3xl font-bold text-yellow-600">{{ stats.maintenance }}</p>
      </div>
    </div>

    <div class="card">
      <div class="mb-4 flex space-x-4">
        <select v-model="filters.status" @change="loadAssets" class="input w-48">
          <option value="">All Status</option>
          <option value="available">Available</option>
          <option value="assigned">Assigned</option>
          <option value="maintenance">Maintenance</option>
          <option value="retired">Retired</option>
        </select>
        <select v-model="filters.category" @change="loadAssets" class="input w-48">
          <option value="">All Categories</option>
          <option value="laptop">Laptop</option>
          <option value="desktop">Desktop</option>
          <option value="phone">Phone</option>
          <option value="tablet">Tablet</option>
          <option value="monitor">Monitor</option>
          <option value="other">Other</option>
        </select>
      </div>

      <table class="table">
        <thead>
          <tr>
            <th>Asset Code</th>
            <th>Name</th>
            <th>Category</th>
            <th>Status</th>
            <th>Purchase Date</th>
            <th>Cost (PKR)</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="asset in assets" :key="asset.id">
            <td class="font-mono">{{ asset.asset_code }}</td>
            <td>{{ asset.name }}</td>
            <td>{{ asset.category }}</td>
            <td>
              <span :class="getStatusBadge(asset.status)">
                {{ asset.status }}
              </span>
            </td>
            <td>{{ formatDate(asset.purchase_date) }}</td>
            <td>Rs. {{ formatNumber(asset.purchase_cost) }}</td>
            <td>
              <div class="flex space-x-2">
                <button @click="viewAsset(asset)" class="text-blue-600 hover:underline">
                  View
                </button>
                <button v-if="asset.status === 'available'" @click="assignAsset(asset)" class="text-green-600 hover:underline">
                  Assign
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const assets = ref([]);
const stats = ref({ total: 0, available: 0, assigned: 0, maintenance: 0 });
const filters = ref({ status: '', category: '' });
const showCreateAsset = ref(false);

const loadAssets = async () => {
  try {
    const response = await axios.get('/assets', { params: filters.value });
    assets.value = response.data.data;
    
    stats.value.total = assets.value.length;
    stats.value.available = assets.value.filter(a => a.status === 'available').length;
    stats.value.assigned = assets.value.filter(a => a.status === 'assigned').length;
    stats.value.maintenance = assets.value.filter(a => a.status === 'maintenance').length;
  } catch (error) {
    console.error('Failed to load assets:', error);
  }
};

const formatDate = (date) => {
  return date ? new Date(date).toLocaleDateString('en-PK') : 'N/A';
};

const formatNumber = (num) => {
  return num ? new Intl.NumberFormat('en-PK').format(num) : '0';
};

const getStatusBadge = (status) => {
  const badges = {
    available: 'badge badge-success',
    assigned: 'badge badge-primary',
    maintenance: 'badge badge-warning',
    retired: 'badge badge-danger',
  };
  return badges[status] || 'badge';
};

const viewAsset = (asset) => {
  alert(`View asset: ${asset.name}`);
};

const assignAsset = (asset) => {
  alert(`Assign asset: ${asset.name}`);
};

onMounted(() => {
  loadAssets();
});
</script>
