<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Asset Management</h1>
      <button
        v-if="can('assets.create')"
        @click="openAssetModal()"
        class="inline-flex items-center px-5 py-2.5 bg-accent hover:bg-accent-dark text-white font-medium rounded-lg transition-colors shadow"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        Add Asset
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Total Assets</p>
        <h3 class="text-2xl font-bold text-gray-900">{{ stats.total }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Available</p>
        <h3 class="text-2xl font-bold text-green-600">{{ stats.available }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Assigned</p>
        <h3 class="text-2xl font-bold text-blue-600">{{ stats.assigned }}</h3>
      </div>
      <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Maintenance</p>
        <h3 class="text-2xl font-bold text-amber-600">{{ stats.maintenance }}</h3>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-4 mb-5">
      <div class="flex flex-wrap items-end gap-4">
        <div class="min-w-[160px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" @change="loadAssets()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
            <option value="">All Status</option>
            <option value="available">Available</option>
            <option value="assigned">Assigned</option>
            <option value="maintenance">Maintenance</option>
            <option value="retired">Retired</option>
          </select>
        </div>
        <div class="min-w-[160px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
          <select v-model="filters.category" @change="loadAssets()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
            <option value="">All Categories</option>
            <option v-for="c in categories" :key="c" :value="c">{{ formatCategory(c) }}</option>
          </select>
        </div>
        <div class="flex-1 min-w-[200px]">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
          <input v-model="filters.search" type="text" placeholder="Name or asset code..." @input="debounceSearch" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-gray-900"></div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
      <p class="font-medium">{{ error }}</p>
      <button @click="loadAssets()" class="mt-2 text-sm underline">Try again</button>
    </div>

    <div v-else-if="assets.length === 0" class="bg-white rounded-lg shadow border border-gray-200 p-12 text-center">
      <h3 class="text-lg font-semibold text-gray-900 mb-1">No Assets</h3>
      <p class="text-gray-500">Click "Add Asset" to create one.</p>
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-lg shadow border border-gray-200 overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Asset Code</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Purchase Date</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cost (PKR)</th>
            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="asset in assets" :key="asset.id" class="hover:bg-gray-50">
            <td class="px-5 py-4 text-sm font-mono text-gray-900">{{ asset.asset_code }}</td>
            <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ asset.name }}</td>
            <td class="px-5 py-4 text-sm text-gray-600">{{ formatCategory(asset.category || asset.type) }}</td>
            <td class="px-5 py-4">
              <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold" :class="statusClass(asset.status)">{{ asset.status }}</span>
            </td>
            <td class="px-5 py-4 text-sm text-gray-600">{{ formatDate(asset.purchase_date) }}</td>
            <td class="px-5 py-4 text-sm text-gray-600">Rs. {{ formatNumber(asset.purchase_cost ?? asset.purchase_price) }}</td>
            <td class="px-5 py-4 text-right space-x-2 whitespace-nowrap">
              <button @click="viewAsset(asset)" class="text-sm text-gray-600 hover:text-gray-900 font-medium">View</button>
              <button v-if="can('assets.update')" @click="openAssetModal(asset)" class="text-sm text-gray-600 hover:text-gray-900 font-medium">Edit</button>
              <button
                v-if="can('assets.assign') && asset.status === 'available'"
                @click="openAssignModal(asset)"
                class="text-sm text-green-700 hover:text-green-800 font-medium"
              >Assign</button>
              <button
                v-if="can('assets.assign') && asset.status === 'assigned'"
                @click="openReturnModal(asset)"
                class="text-sm text-amber-700 hover:text-amber-800 font-medium"
              >Return</button>
              <button v-if="can('assets.delete')" @click="openDeleteModal(asset)" class="text-sm text-red-600 hover:text-red-700 font-medium">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create/Edit Asset Modal -->
    <div v-if="showAssetModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ editingAsset ? 'Edit Asset' : 'Add Asset' }}</h3>
          <button @click="showAssetModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4 overflow-y-auto">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Asset Code *</label>
            <input v-model="assetForm.asset_code" type="text" :disabled="!!editingAsset" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent disabled:bg-gray-100" placeholder="e.g. LAP-001" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Name *</label>
            <input v-model="assetForm.name" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Category *</label>
              <select v-model="assetForm.category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                <option v-for="c in categories" :key="c" :value="c">{{ formatCategory(c) }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Status *</label>
              <select v-model="assetForm.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
                <option value="available">Available</option>
                <option value="assigned">Assigned</option>
                <option value="maintenance">Maintenance</option>
                <option value="retired">Retired</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea v-model="assetForm.description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Purchase Date</label>
              <input v-model="assetForm.purchase_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Purchase Cost</label>
              <input v-model.number="assetForm.purchase_cost" type="number" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Warranty Expiry</label>
            <input v-model="assetForm.warranty_expiry" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showAssetModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveAsset" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">
            {{ saving ? 'Saving...' : (editingAsset ? 'Update' : 'Create') }}
          </button>
        </div>
      </div>
    </div>

    <!-- View Asset Modal -->
    <div v-if="showViewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">{{ viewingAsset?.name }}</h3>
          <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div v-if="viewLoading" class="flex justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
        </div>
        <div v-else-if="viewingAsset" class="px-6 py-5 space-y-4 overflow-y-auto text-sm">
          <div class="flex flex-wrap gap-2">
            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold" :class="statusClass(viewingAsset.status)">{{ viewingAsset.status }}</span>
            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ formatCategory(viewingAsset.category || viewingAsset.type) }}</span>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div><p class="text-gray-500">Asset Code</p><p class="font-mono font-medium text-gray-900">{{ viewingAsset.asset_code }}</p></div>
            <div><p class="text-gray-500">Purchase Date</p><p class="font-medium text-gray-900">{{ formatDate(viewingAsset.purchase_date) }}</p></div>
            <div><p class="text-gray-500">Cost</p><p class="font-medium text-gray-900">Rs. {{ formatNumber(viewingAsset.purchase_cost ?? viewingAsset.purchase_price) }}</p></div>
            <div><p class="text-gray-500">Warranty Expiry</p><p class="font-medium text-gray-900">{{ formatDate(viewingAsset.warranty_expiry) }}</p></div>
          </div>
          <div><p class="font-semibold text-gray-700 mb-1">Description</p><p class="text-gray-600 whitespace-pre-wrap">{{ viewingAsset.description || '—' }}</p></div>

          <div v-if="currentAssignment" class="bg-blue-50 border border-blue-100 rounded-lg p-4 space-y-2">
            <p class="font-semibold text-gray-900">Current Assignment</p>
            <p><span class="text-gray-500">Employee:</span> {{ getEmployeeName(currentAssignment.employee) }}</p>
            <p><span class="text-gray-500">Assigned:</span> {{ formatDate(currentAssignment.assigned_date) }}</p>
            <p v-if="currentAssignment.notes || currentAssignment.remarks"><span class="text-gray-500">Notes:</span> {{ currentAssignment.notes || currentAssignment.remarks }}</p>
          </div>
          <div v-else-if="viewingAsset.status === 'assigned'" class="text-sm text-gray-500">Assignment details unavailable.</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button
            v-if="can('assets.assign') && viewingAsset?.status === 'available'"
            @click="openAssignModal(viewingAsset); showViewModal = false"
            class="px-4 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark"
          >Assign</button>
          <button
            v-if="can('assets.assign') && viewingAsset?.status === 'assigned'"
            @click="openReturnModal(viewingAsset); showViewModal = false"
            class="px-4 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark"
          >Return</button>
          <button @click="showViewModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Close</button>
        </div>
      </div>
    </div>

    <!-- Assign Modal -->
    <div v-if="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">Assign Asset</h3>
          <button @click="showAssignModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4">
          <p class="text-sm text-gray-600">Assigning <span class="font-semibold text-gray-900">{{ assigningAsset?.name }}</span> ({{ assigningAsset?.asset_code }})</p>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Employee *</label>
            <select v-model="assignForm.employee_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
              <option value="">Select employee</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ getEmployeeName(emp) }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Assigned Date *</label>
            <input v-model="assignForm.assigned_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
            <textarea v-model="assignForm.notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showAssignModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveAssign" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">
            {{ saving ? 'Assigning...' : 'Assign' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Return Modal -->
    <div v-if="showReturnModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-900">Return Asset</h3>
          <button @click="showReturnModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>
        <div class="px-6 py-5 space-y-4">
          <p class="text-sm text-gray-600">Returning <span class="font-semibold text-gray-900">{{ returningAsset?.name }}</span></p>
          <div v-if="returnAssignmentInfo" class="text-sm bg-gray-50 rounded-lg p-3">
            Currently with: <span class="font-medium">{{ getEmployeeName(returnAssignmentInfo.employee) }}</span>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Returned Date *</label>
            <input v-model="returnForm.returned_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Condition on Return *</label>
            <select v-model="returnForm.condition_on_return" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
              <option value="good">Good</option>
              <option value="fair">Fair</option>
              <option value="damaged">Damaged</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Return Notes</label>
            <textarea v-model="returnForm.return_notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent"></textarea>
          </div>
          <div v-if="formError" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showReturnModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="saveReturn" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-accent rounded-lg hover:bg-accent-dark disabled:opacity-50">
            {{ saving ? 'Returning...' : 'Return Asset' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
        <div class="px-6 py-5 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
          </div>
          <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Asset</h3>
          <p class="text-sm text-gray-600">Delete <span class="font-semibold">{{ deletingAsset?.name }}</span>? This cannot be undone.</p>
          <div v-if="formError" class="mt-3 text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ formError }}</div>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3 bg-gray-50">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
          <button @click="deleteAsset" :disabled="deleting" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePermissions } from '@/composables/usePermissions';
import axios from 'axios';

const { can } = usePermissions();

const categories = ['laptop', 'desktop', 'phone', 'tablet', 'monitor', 'keyboard', 'mouse', 'other'];

const assets = ref([]);
const employees = ref([]);
const loading = ref(false);
const viewLoading = ref(false);
const error = ref(null);
const saving = ref(false);
const deleting = ref(false);
const formError = ref(null);
const filters = ref({ status: '', category: '', search: '' });
let searchTimer = null;

const showAssetModal = ref(false);
const showViewModal = ref(false);
const showAssignModal = ref(false);
const showReturnModal = ref(false);
const showDeleteModal = ref(false);

const editingAsset = ref(null);
const viewingAsset = ref(null);
const assigningAsset = ref(null);
const returningAsset = ref(null);
const deletingAsset = ref(null);
const returnAssignmentId = ref(null);
const returnAssignmentInfo = ref(null);

const today = () => new Date().toISOString().split('T')[0];

const emptyAssetForm = () => ({
  asset_code: '',
  name: '',
  category: 'laptop',
  description: '',
  purchase_date: '',
  purchase_cost: null,
  warranty_expiry: '',
  status: 'available',
});

const assetForm = ref(emptyAssetForm());
const assignForm = ref({ employee_id: '', assigned_date: today(), notes: '' });
const returnForm = ref({ returned_date: today(), condition_on_return: 'good', return_notes: '' });

const extractList = (payload) => {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  return [];
};

const stats = computed(() => {
  const list = assets.value || [];
  return {
    total: list.length,
    available: list.filter(a => a.status === 'available').length,
    assigned: list.filter(a => a.status === 'assigned').length,
    maintenance: list.filter(a => a.status === 'maintenance').length,
  };
});

const currentAssignment = computed(() =>
  viewingAsset.value?.current_assignment || viewingAsset.value?.currentAssignment || null
);

const getEmployeeName = (emp) =>
  emp?.user?.name || `${emp?.first_name || ''} ${emp?.last_name || ''}`.trim() || 'N/A';

const formatDate = (date) => (date ? new Date(date).toLocaleDateString('en-PK') : '—');

const formatNumber = (num) =>
  num != null ? new Intl.NumberFormat('en-PK').format(num) : '0';

const formatCategory = (c) =>
  c ? c.replace(/_/g, ' ').replace(/\b\w/g, (ch) => ch.toUpperCase()) : '—';

const statusClass = (status) => ({
  available: 'bg-green-100 text-green-800',
  assigned: 'bg-blue-100 text-blue-800',
  maintenance: 'bg-amber-100 text-amber-800',
  retired: 'bg-red-100 text-red-700',
}[status] || 'bg-gray-100 text-gray-700');

const loadAssets = async () => {
  loading.value = true;
  error.value = null;
  try {
    const params = {};
    if (filters.value.status) params.status = filters.value.status;
    if (filters.value.category) params.category = filters.value.category;
    if (filters.value.search) params.search = filters.value.search;
    const response = await axios.get('/assets', { params });
    assets.value = extractList(response.data);
  } catch (err) {
    error.value = 'Failed to load assets';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const loadEmployees = async () => {
  try {
    const response = await axios.get('/employees/dropdown');
    employees.value = extractList(response.data);
  } catch (err) {
    console.error('Failed to load employees:', err);
  }
};

const debounceSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => loadAssets(), 400);
};

const openAssetModal = (asset = null) => {
  editingAsset.value = asset;
  formError.value = null;
  if (asset) {
    const cat = asset.category || asset.type || 'other';
    assetForm.value = {
      asset_code: asset.asset_code || '',
      name: asset.name || '',
      category: categories.includes(cat) ? cat : 'other',
      description: asset.description || '',
      purchase_date: asset.purchase_date ? String(asset.purchase_date).substring(0, 10) : '',
      purchase_cost: asset.purchase_cost ?? asset.purchase_price ?? null,
      warranty_expiry: asset.warranty_expiry ? String(asset.warranty_expiry).substring(0, 10) : '',
      status: asset.status || 'available',
    };
  } else {
    assetForm.value = emptyAssetForm();
  }
  showAssetModal.value = true;
};

const saveAsset = async () => {
  formError.value = null;
  const f = assetForm.value;
  if (!editingAsset.value && !f.asset_code.trim()) { formError.value = 'Asset code is required'; return; }
  if (!f.name.trim()) { formError.value = 'Name is required'; return; }
  saving.value = true;
  try {
    if (editingAsset.value) {
      await axios.put(`/assets/${editingAsset.value.id}`, {
        name: f.name,
        category: f.category,
        description: f.description || null,
        purchase_date: f.purchase_date || null,
        purchase_cost: f.purchase_cost,
        warranty_expiry: f.warranty_expiry || null,
        status: f.status,
      });
    } else {
      await axios.post('/assets', f);
    }
    showAssetModal.value = false;
    await loadAssets();
  } catch (err) {
    formError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {}).flat().join(' ') || 'Failed to save asset';
  } finally {
    saving.value = false;
  }
};

const viewAsset = async (asset) => {
  viewingAsset.value = asset;
  showViewModal.value = true;
  viewLoading.value = true;
  try {
    const response = await axios.get(`/assets/${asset.id}`);
    viewingAsset.value = response.data?.data || response.data;
  } catch (err) {
    console.error(err);
  } finally {
    viewLoading.value = false;
  }
};

const openAssignModal = (asset) => {
  assigningAsset.value = asset;
  formError.value = null;
  assignForm.value = { employee_id: '', assigned_date: today(), notes: '' };
  showAssignModal.value = true;
};

const saveAssign = async () => {
  formError.value = null;
  if (!assignForm.value.employee_id) { formError.value = 'Please select an employee'; return; }
  if (!assignForm.value.assigned_date) { formError.value = 'Assigned date is required'; return; }
  saving.value = true;
  try {
    await axios.post('/assets/assign', {
      asset_id: assigningAsset.value.id,
      employee_id: assignForm.value.employee_id,
      assigned_date: assignForm.value.assigned_date,
      notes: assignForm.value.notes || null,
    });
    showAssignModal.value = false;
    await loadAssets();
  } catch (err) {
    formError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {}).flat().join(' ') || 'Failed to assign asset';
  } finally {
    saving.value = false;
  }
};

const openReturnModal = async (asset) => {
  returningAsset.value = asset;
  returnAssignmentId.value = null;
  returnAssignmentInfo.value = null;
  formError.value = null;
  returnForm.value = { returned_date: today(), condition_on_return: 'good', return_notes: '' };
  showReturnModal.value = true;

  try {
    const detailRes = await axios.get(`/assets/${asset.id}`);
    const detail = detailRes.data?.data || detailRes.data;
    const assignment = detail?.current_assignment || detail?.currentAssignment;
    if (assignment?.id) {
      returnAssignmentId.value = assignment.id;
      returnAssignmentInfo.value = assignment;
      return;
    }
  } catch (err) {
    console.error(err);
  }

  try {
    const listRes = await axios.get('/assets/assignments/list', { params: { status: 'active' } });
    const list = extractList(listRes.data);
    const match = list.find(a => a.asset_id === asset.id || a.asset?.id === asset.id);
    if (match) {
      returnAssignmentId.value = match.id;
      returnAssignmentInfo.value = match;
    } else {
      formError.value = 'No active assignment found for this asset.';
    }
  } catch (err) {
    formError.value = 'Could not load assignment details.';
    console.error(err);
  }
};

const saveReturn = async () => {
  formError.value = null;
  if (!returnAssignmentId.value) {
    formError.value = 'No active assignment found for this asset.';
    return;
  }
  if (!returnForm.value.returned_date) { formError.value = 'Returned date is required'; return; }
  saving.value = true;
  try {
    await axios.post(`/assets/assignments/${returnAssignmentId.value}/return`, {
      returned_date: returnForm.value.returned_date,
      condition_on_return: returnForm.value.condition_on_return,
      return_notes: returnForm.value.return_notes || null,
    });
    showReturnModal.value = false;
    await loadAssets();
  } catch (err) {
    formError.value = err.response?.data?.message || Object.values(err.response?.data?.errors || {}).flat().join(' ') || 'Failed to return asset';
  } finally {
    saving.value = false;
  }
};

const openDeleteModal = (asset) => {
  deletingAsset.value = asset;
  formError.value = null;
  showDeleteModal.value = true;
};

const deleteAsset = async () => {
  deleting.value = true;
  formError.value = null;
  try {
    await axios.delete(`/assets/${deletingAsset.value.id}`);
    showDeleteModal.value = false;
    await loadAssets();
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to delete asset';
  } finally {
    deleting.value = false;
  }
};

onMounted(() => {
  loadAssets();
  loadEmployees();
});
</script>
