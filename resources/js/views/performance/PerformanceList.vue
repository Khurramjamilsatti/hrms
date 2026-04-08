<template>
  <div>
    <div class="mb-6">
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Performance Management</h2>
        <button v-if="canManage" @click="showCreateReview = true" class="btn btn-primary">
          Create Review
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="card">
        <h3 class="text-sm font-semibold text-gray-600 mb-2">Total Reviews</h3>
        <p class="text-3xl font-bold text-primary-600">{{ stats.totalReviews }}</p>
      </div>
      <div class="card">
        <h3 class="text-sm font-semibold text-gray-600 mb-2">Active Goals</h3>
        <p class="text-3xl font-bold text-green-600">{{ stats.activeGoals }}</p>
      </div>
      <div class="card">
        <h3 class="text-sm font-semibold text-gray-600 mb-2">Average Rating</h3>
        <p class="text-3xl font-bold text-yellow-600">{{ stats.avgRating }}</p>
      </div>
    </div>

    <div class="card mb-6">
      <div class="mb-4">
        <ul class="flex border-b">
          <li class="-mb-px mr-1">
            <button 
              @click="activeTab = 'reviews'" 
              :class="['inline-block py-2 px-4 font-semibold', activeTab === 'reviews' ? 'border-b-2 border-primary-500 text-primary-600' : 'text-gray-600']"
            >
              Performance Reviews
            </button>
          </li>
          <li class="mr-1">
            <button 
              @click="activeTab = 'goals'" 
              :class="['inline-block py-2 px-4 font-semibold', activeTab === 'goals' ? 'border-b-2 border-primary-500 text-primary-600' : 'text-gray-600']"
            >
              Goals
            </button>
          </li>
        </ul>
      </div>

      <div v-if="activeTab === 'reviews'">
        <table class="table">
          <thead>
            <tr>
              <th>Employee</th>
              <th>Review Date</th>
              <th>Cycle</th>
              <th>Overall Rating</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="review in reviews" :key="review.id">
              <td>{{ review.employee?.user?.name }}</td>
              <td>{{ formatDate(review.review_date) }}</td>
              <td>{{ review.cycle?.name }}</td>
              <td>
                <div class="flex items-center">
                  <span class="text-yellow-500 mr-1">★</span>
                  {{ review.overall_rating.toFixed(1) }}/5
                </div>
              </td>
              <td>
                <span :class="getStatusBadge(review.status)">
                  {{ review.status }}
                </span>
              </td>
              <td>
                <button @click="viewReview(review)" class="text-blue-600 hover:underline">
                  View
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="activeTab === 'goals'">
        <table class="table">
          <thead>
            <tr>
              <th>Employee</th>
              <th>Title</th>
              <th>Target Date</th>
              <th>Progress</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="goal in goals" :key="goal.id">
              <td>{{ goal.employee?.user?.name }}</td>
              <td>{{ goal.title }}</td>
              <td>{{ formatDate(goal.target_date) }}</td>
              <td>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                  <div 
                    class="bg-primary-600 h-2.5 rounded-full" 
                    :style="{ width: goal.progress_percentage + '%' }"
                  ></div>
                </div>
                <span class="text-xs text-gray-600">{{ goal.progress_percentage }}%</span>
              </td>
              <td>
                <span :class="getGoalStatusBadge(goal.status)">
                  {{ goal.status }}
                </span>
              </td>
              <td>
                <button @click="editGoal(goal)" class="text-blue-600 hover:underline">
                  Edit
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
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';

const authStore = useAuthStore();
const activeTab = ref('reviews');
const reviews = ref([]);
const goals = ref([]);
const stats = ref({ totalReviews: 0, activeGoals: 0, avgRating: 0 });
const showCreateReview = ref(false);

const canManage = computed(() => authStore.isAdmin || authStore.isManager);

const loadData = async () => {
  try {
    const [reviewsRes, goalsRes] = await Promise.all([
      axios.get('/performance/reviews'),
      axios.get('/performance/goals')
    ]);
    
    reviews.value = reviewsRes.data.data;
    goals.value = goalsRes.data;
    
    stats.value.totalReviews = reviews.value.length;
    stats.value.activeGoals = goals.value.filter(g => g.status === 'in_progress').length;
    
    if (reviews.value.length > 0) {
      const sum = reviews.value.reduce((acc, r) => acc + r.overall_rating, 0);
      stats.value.avgRating = (sum / reviews.value.length).toFixed(1);
    }
  } catch (error) {
    console.error('Failed to load performance data:', error);
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-PK');
};

const getStatusBadge = (status) => {
  const badges = {
    draft: 'badge badge-secondary',
    submitted: 'badge badge-warning',
    acknowledged: 'badge badge-success',
  };
  return badges[status] || 'badge';
};

const getGoalStatusBadge = (status) => {
  const badges = {
    not_started: 'badge badge-secondary',
    in_progress: 'badge badge-primary',
    completed: 'badge badge-success',
    cancelled: 'badge badge-danger',
  };
  return badges[status] || 'badge';
};

const viewReview = (review) => {
  alert(`View review for ${review.employee.user.name}`);
};

const editGoal = (goal) => {
  alert(`Edit goal: ${goal.title}`);
};

onMounted(() => {
  loadData();
});
</script>
