<template>
  <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75" v-if="show">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
      <div class="inline-block w-full max-w-6xl bg-white rounded-lg shadow-xl transform transition-all border-2 border-gray-200">
        <!-- Header -->
        <div class="bg-black px-6 py-4 rounded-t-lg border-b-2 border-gray-200">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="bg-white rounded-full p-2">
                <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-white">Assign Employees to Shift</h2>
                <p class="text-gray-300 text-sm">{{ shift?.name }} ({{ formatTime(shift?.start_time) }} - {{ formatTime(shift?.end_time) }})</p>
              </div>
            </div>
            <button @click="close" class="text-white hover:text-gray-300">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6">
          <!-- Tabs -->
          <div class="border-b border-gray-200 mb-6">
            <div class="flex space-x-8">
              <button 
                @click="activeTab = 'assign'"
                :class="[
                  'pb-4 px-1 border-b-2 font-medium text-sm',
                  activeTab === 'assign'
                    ? 'border-black text-black'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]">
                Assign New Employees
              </button>
              <button 
                @click="activeTab = 'current'"
                :class="[
                  'pb-4 px-1 border-b-2 font-medium text-sm',
                  activeTab === 'current'
                    ? 'border-black text-black'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]">
                Current Assignments ({{ currentAssignments.length }})
              </button>
              <button 
                @click="activeTab = 'history'"
                :class="[
                  'pb-4 px-1 border-b-2 font-medium text-sm',
                  activeTab === 'history'
                    ? 'border-black text-black'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]">
                Assignment History
              </button>
            </div>
          </div>

          <!-- Assign New Employees Tab -->
          <div v-if="activeTab === 'assign'" class="space-y-6">
            <!-- Assignment Form -->
            <div class="bg-gray-50 rounded-lg p-4 space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Effective From *</label>
                  <input 
                    v-model="assignmentForm.effective_from"
                    type="date"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Effective To (Optional)</label>
                  <input 
                    v-model="assignmentForm.effective_to"
                    type="date"
                    :min="assignmentForm.effective_from"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
                  <p class="text-xs text-gray-500 mt-1">Leave empty for permanent assignment</p>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Available Employees</label>
                <input 
                  v-model="employeeSearch"
                  @input="searchAvailableEmployees"
                  type="text"
                  placeholder="Search by name or employee code..."
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
              </div>

              <!-- Selected Employees Count -->
              <div v-if="selectedEmployees.length > 0" class="bg-gray-100 border border-gray-300 rounded-lg p-3">
                <p class="text-sm font-medium text-black">
                  {{ selectedEmployees.length }} employee(s) selected for assignment
                </p>
              </div>

              <!-- Assign Button -->
              <button 
                @click="bulkAssignEmployees"
                :disabled="selectedEmployees.length === 0 || assigning"
                class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-black hover:bg-gray-800 text-white rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>{{ assigning ? 'Assigning...' : `Assign ${selectedEmployees.length} Employee(s)` }}</span>
              </button>
            </div>

            <!-- Available Employees List -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
              <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700">Available Employees</h3>
                <p class="text-xs text-gray-500 mt-1">Click to select employees for assignment</p>
              </div>
              
              <div v-if="loadingEmployees" class="p-8 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-200 border-t-black"></div>
                <p class="text-gray-600 mt-2">Loading employees...</p>
              </div>

              <div v-else-if="availableEmployees.length === 0" class="p-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="font-medium">No available employees found</p>
                <p class="text-sm">All active employees are already assigned to shifts during this period</p>
              </div>

              <div v-else class="max-h-96 overflow-y-auto">
                <div 
                  v-for="employee in availableEmployees" 
                  :key="employee.id"
                  @click="toggleEmployeeSelection(employee)"
                  :class="[
                    'flex items-center justify-between p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors',
                    selectedEmployees.includes(employee.id) ? 'bg-gray-100 border-l-4 border-l-black' : ''
                  ]">
                  <div class="flex items-center space-x-3">
                    <div :class="[
                      'w-10 h-10 rounded-full flex items-center justify-center font-semibold',
                      selectedEmployees.includes(employee.id) ? 'bg-black text-white' : 'bg-gray-200 text-gray-600'
                    ]">
                      {{ employee.first_name.charAt(0) }}{{ employee.last_name.charAt(0) }}
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">{{ employee.first_name }} {{ employee.last_name }}</p>
                      <p class="text-sm text-gray-500">{{ employee.employee_code }} • {{ employee.department?.name || 'N/A' }}</p>
                    </div>
                  </div>
                  <div v-if="selectedEmployees.includes(employee.id)" class="text-black">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Current Assignments Tab -->
          <div v-if="activeTab === 'current'">
            <div v-if="loadingAssignments" class="p-8 text-center">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-200 border-t-black"></div>
              <p class="text-gray-600 mt-2">Loading assignments...</p>
            </div>

            <div v-else-if="currentAssignments.length === 0" class="p-8 text-center text-gray-500">
              <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              <p class="font-medium">No employees assigned yet</p>
              <p class="text-sm">Start by assigning employees from the "Assign New Employees" tab</p>
            </div>

            <div v-else class="space-y-2">
              <div 
                v-for="assignment in currentAssignments" 
                :key="assignment.id"
                class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-black flex items-center justify-center text-white font-semibold">
                      {{ assignment.employee.first_name.charAt(0) }}{{ assignment.employee.last_name.charAt(0) }}
                    </div>
                    <div>
                      <p class="font-semibold text-gray-900">{{ assignment.employee.first_name }} {{ assignment.employee.last_name }}</p>
                      <p class="text-sm text-gray-600">{{ assignment.employee.employee_code }}</p>
                      <p class="text-xs text-gray-500">{{ assignment.employee.department?.name || 'N/A' }} • {{ assignment.employee.designation?.title || 'N/A' }}</p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-4">
                    <div class="text-right">
                      <p class="text-xs text-gray-500">Effective Period</p>
                      <p class="text-sm font-medium text-gray-900">
                        {{ formatDate(assignment.effective_from) }}
                        <span class="text-gray-400">to</span>
                        {{ assignment.effective_to ? formatDate(assignment.effective_to) : 'Permanent' }}
                      </p>
                    </div>
                    <button 
                      @click="confirmRemoveAssignment(assignment)"
                      class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      title="Remove Assignment">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Assignment History Tab -->
          <div v-if="activeTab === 'history'">
            <div v-if="loadingHistory" class="p-8 text-center">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-200 border-t-black"></div>
              <p class="text-gray-600 mt-2">Loading history...</p>
            </div>

            <div v-else-if="assignmentHistory.length === 0" class="p-8 text-center text-gray-500">
              <p>No assignment history available</p>
            </div>

            <div v-else class="space-y-2 max-h-96 overflow-y-auto">
              <div 
                v-for="assignment in assignmentHistory" 
                :key="assignment.id"
                class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold text-sm">
                      {{ assignment.employee.first_name.charAt(0) }}{{ assignment.employee.last_name.charAt(0) }}
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">{{ assignment.employee.first_name }} {{ assignment.employee.last_name }}</p>
                      <p class="text-xs text-gray-500">
                        {{ formatDate(assignment.effective_from) }} - {{ assignment.effective_to ? formatDate(assignment.effective_to) : 'Permanent' }}
                      </p>
                    </div>
                  </div>
                  <span :class="[
                    'px-3 py-1 text-xs font-medium rounded-full',
                    assignment.effective_to && new Date(assignment.effective_to) < new Date()
                      ? 'bg-gray-200 text-gray-700'
                      : 'bg-green-100 text-green-700'
                  ]">
                    {{ assignment.effective_to && new Date(assignment.effective_to) < new Date() ? 'Completed' : 'Active' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end">
          <button 
            @click="close"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Remove Assignment Confirmation -->
    <div v-if="showRemoveModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
          <div class="flex items-start">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-medium text-gray-900">Remove Assignment</h3>
              <p class="mt-2 text-sm text-gray-500">
                Are you sure you want to remove <strong>{{ assignmentToRemove?.employee.first_name }} {{ assignmentToRemove?.employee.last_name }}</strong> from this shift?
              </p>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 rounded-b-lg">
          <button 
            @click="showRemoveModal = false"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
            Cancel
          </button>
          <button 
            @click="removeAssignment"
            :disabled="removing"
            class="px-4 py-2 bg-black hover:bg-gray-800 text-white rounded-lg disabled:opacity-50">
            {{ removing ? 'Removing...' : 'Remove' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { useNotification } from '@/composables/useNotification';

const props = defineProps({
  show: Boolean,
  shift: Object
});

const emit = defineEmits(['close', 'assigned']);

const { success, error: showError } = useNotification();

const activeTab = ref('assign');
const employeeSearch = ref('');
const availableEmployees = ref([]);
const selectedEmployees = ref([]);
const currentAssignments = ref([]);
const assignmentHistory = ref([]);
const loadingEmployees = ref(false);
const loadingAssignments = ref(false);
const loadingHistory = ref(false);
const assigning = ref(false);
const removing = ref(false);
const showRemoveModal = ref(false);
const assignmentToRemove = ref(null);

const assignmentForm = ref({
  effective_from: new Date().toISOString().split('T')[0],
  effective_to: null
});

watch(() => props.show, (show) => {
  if (show && props.shift) {
    fetchAvailableEmployees();
    fetchCurrentAssignments();
  }
});

watch(() => activeTab.value, (tab) => {
  if (tab === 'history' && assignmentHistory.value.length === 0) {
    fetchAssignmentHistory();
  }
});

watch(() => [assignmentForm.value.effective_from, assignmentForm.value.effective_to], () => {
  if (assignmentForm.value.effective_from) {
    fetchAvailableEmployees();
  }
});

const fetchAvailableEmployees = async () => {
  if (!props.shift) return;
  
  loadingEmployees.value = true;
  try {
    const params = {
      effective_from: assignmentForm.value.effective_from,
      effective_to: assignmentForm.value.effective_to || undefined,
      search: employeeSearch.value || undefined
    };
    
    const response = await axios.get(`/shifts/${props.shift.id}/available-employees`, { params });
    availableEmployees.value = response.data.data || response.data;
  } catch (err) {
    console.error('Error fetching available employees:', err);
  } finally {
    loadingEmployees.value = false;
  }
};

const searchAvailableEmployees = () => {
  fetchAvailableEmployees();
};

const fetchCurrentAssignments = async () => {
  if (!props.shift) return;
  
  loadingAssignments.value = true;
  try {
    const response = await axios.get(`/shifts/${props.shift.id}/assignments`);
    currentAssignments.value = response.data.data || response.data;
  } catch (err) {
    console.error('Error fetching assignments:', err);
  } finally {
    loadingAssignments.value = false;
  }
};

const fetchAssignmentHistory = async () => {
  if (!props.shift) return;
  
  loadingHistory.value = true;
  try {
    const response = await axios.get(`/shifts/${props.shift.id}/assignment-history`);
    assignmentHistory.value = response.data.data || response.data;
  } catch (err) {
    console.error('Error fetching assignment history:', err);
  } finally {
    loadingHistory.value = false;
  }
};

const toggleEmployeeSelection = (employee) => {
  const index = selectedEmployees.value.indexOf(employee.id);
  if (index > -1) {
    selectedEmployees.value.splice(index, 1);
  } else {
    selectedEmployees.value.push(employee.id);
  }
};

const bulkAssignEmployees = async () => {
  if (selectedEmployees.value.length === 0) return;
  
  assigning.value = true;
  try {
    const payload = {
      employee_ids: selectedEmployees.value,
      effective_from: assignmentForm.value.effective_from,
      effective_to: assignmentForm.value.effective_to || null
    };
    
    const response = await axios.post(`/shifts/${props.shift.id}/assignments/bulk`, payload);
    
    // Show success message
    success(`Successfully assigned ${response.data.success_count} employee(s)`);
    
    // Show conflicts if any
    if (response.data.conflicts && response.data.conflicts.length > 0) {
      showError(`${response.data.conflicts.length} employee(s) could not be assigned due to conflicts`);
    }
    
    // Reset and refresh
    selectedEmployees.value = [];
    await fetchAvailableEmployees();
    await fetchCurrentAssignments();
    activeTab.value = 'current';
    
    emit('assigned');
  } catch (err) {
    console.error('Error assigning employees:', err);
    showError(err.response?.data?.message || 'Failed to assign employees');
  } finally {
    assigning.value = false;
  }
};

const confirmRemoveAssignment = (assignment) => {
  assignmentToRemove.value = assignment;
  showRemoveModal.value = true;
};

const removeAssignment = async () => {
  if (!assignmentToRemove.value) return;
  
  removing.value = true;
  try {
    await axios.delete(`/shifts/${props.shift.id}/assignments/${assignmentToRemove.value.id}`);
    success('Assignment removed successfully');
    await fetchCurrentAssignments();
    await fetchAvailableEmployees();
    showRemoveModal.value = false;
    assignmentToRemove.value = null;
    emit('assigned'); // Trigger refresh in parent component
  } catch (err) {
    console.error('Error removing assignment:', err);
    showError(err.response?.data?.message || 'Failed to remove assignment');
  } finally {
    removing.value = false;
  }
};

const close = () => {
  selectedEmployees.value = [];
  availableEmployees.value = [];
  currentAssignments.value = [];
  assignmentHistory.value = [];
  activeTab.value = 'assign';
  assignmentForm.value = {
    effective_from: new Date().toISOString().split('T')[0],
    effective_to: null
  };
  emit('close');
};

const formatTime = (time) => {
  if (!time) return '';
  return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  });
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};
</script>
