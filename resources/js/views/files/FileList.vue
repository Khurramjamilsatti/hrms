<template>
    <div class="p-6 space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">File Management</h1>
                <p class="text-gray-600 mt-1">Manage and organize company documents</p>
            </div>
            <button @click="openUploadModal"
                class="flex items-center space-x-2 px-4 py-2 bg-black hover:bg-gray-800 text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <span>Upload File</span>
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs font-medium">Total Files</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.total_files || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-xs font-medium">Recent Uploads</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.recent_files || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-xs font-medium">Confidential</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.confidential_files || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-xs font-medium">Categories</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.total_categories || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-xs font-medium">Expiring Soon</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.expiring_soon || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-xs font-medium">Storage (MB)</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.total_size || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"/>
                            <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"/>
                            <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input v-model="filters.search" @input="fetchFiles" type="text" 
                        placeholder="Search files..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select v-model="filters.category_id" @change="fetchFiles"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Categories</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confidential</label>
                    <select v-model="filters.is_confidential" @change="fetchFiles"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Files</option>
                        <option value="1">Confidential Only</option>
                        <option value="0">Public Only</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select v-model="filters.status" @change="fetchFiles"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="expiring">Expiring Soon</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                    <select v-model="pagination.perPage" @change="changePerPage"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option :value="20">20</option>
                        <option :value="40">40</option>
                        <option :value="60">60</option>
                        <option :value="80">80</option>
                        <option :value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Files Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="file in files" :key="file.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-blue-100">
                                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ file.title }}</div>
                                    <div class="text-sm text-gray-500">{{ file.file_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ file.category?.name || 'Uncategorized' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            v{{ file.version }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ formatFileSize(file.file_size) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ formatDate(file.created_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span v-if="file.is_confidential" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Confidential
                            </span>
                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Public
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button @click="viewFile(file)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                <button @click="downloadFile(file)" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Download">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </button>
                                <button @click="editFile(file)" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button @click="confirmDelete(file)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="files.length === 0">
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            No files found. Upload your first file to get started.
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.total > 0" class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ pagination.from }}</span> to <span class="font-medium">{{ pagination.to }}</span> of <span class="font-medium">{{ pagination.total }}</span> files
                </div>
                <div class="flex space-x-2">
                    <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
                        class="px-3 py-1 border border-gray-300 rounded-lg disabled:opacity-50 hover:bg-gray-50">
                        Previous
                    </button>
                    <button v-for="page in displayedPages" :key="page" @click="changePage(page)"
                        :class="page === pagination.current_page ? 'bg-black text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                        class="px-3 py-1 border border-gray-300 rounded-lg">
                        {{ page }}
                    </button>
                    <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
                        class="px-3 py-1 border border-gray-300 rounded-lg disabled:opacity-50 hover:bg-gray-50">
                        Next
                    </button>
                </div>
            </div>
        </div>

        <!-- Upload Modal -->
        <div v-if="showUploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-2xl font-bold mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Upload New File
                </h3>
                
                <form @submit.prevent="uploadFile">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" v-model="uploadForm.title" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea v-model="uploadForm.description" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                            <select v-model="uploadForm.category_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Category</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">File (Max 10MB) *</label>
                            <input type="file" @change="handleFileSelect" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            <p v-if="selectedFile" class="mt-2 text-sm text-gray-600">
                                Selected: {{ selectedFile.name }} ({{ formatFileSize(selectedFile.size) }})
                            </p>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" v-model="uploadForm.is_confidential" id="confidential" 
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                            <label for="confidential" class="text-sm text-gray-700">Mark as Confidential</label>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date (Optional)</label>
                            <input type="date" v-model="uploadForm.expiry_date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="closeUploadModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" :disabled="uploading"
                            class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 disabled:opacity-50">
                            {{ uploading ? 'Uploading...' : 'Upload File' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-2xl font-bold mb-4">Edit File Information</h3>
                
                <form @submit.prevent="updateFile">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" v-model="editForm.title" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea v-model="editForm.description" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                            <select v-model="editForm.category_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Category</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" v-model="editForm.is_confidential" id="edit-confidential" 
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" />
                            <label for="edit-confidential" class="text-sm text-gray-700">Mark as Confidential</label>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date (Optional)</label>
                            <input type="date" v-model="editForm.expiry_date"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="closeEditModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">
                            Update File
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Modal -->
        <div v-if="showViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-3xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold">File Details</h3>
                    <button @click="closeViewModal" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div v-if="selectedFileView" class="space-y-4">
                    <div class="bg-blue-50 rounded-lg p-4 flex items-center">
                        <div class="flex-shrink-0 h-16 w-16 flex items-center justify-center rounded-lg bg-blue-100">
                            <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="text-lg font-bold text-gray-900">{{ selectedFileView.title }}</h4>
                            <p class="text-sm text-gray-600">{{ selectedFileView.file_name }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Category</label>
                            <p class="mt-1 text-sm text-gray-900">{{ selectedFileView.category?.name || 'Uncategorized' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Version</label>
                            <p class="mt-1 text-sm text-gray-900">v{{ selectedFileView.version }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">File Size</label>
                            <p class="mt-1 text-sm text-gray-900">{{ formatFileSize(selectedFileView.file_size) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <p class="mt-1">
                                <span v-if="selectedFileView.is_confidential" class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Confidential
                                </span>
                                <span v-else class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Public
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Uploaded By</label>
                            <p class="mt-1 text-sm text-gray-900">{{ selectedFileView.uploaded_by?.name || 'Unknown' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Upload Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedFileView.created_at) }}</p>
                        </div>
                        <div v-if="selectedFileView.expiry_date" class="col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Expiry Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedFileView.expiry_date) }}</p>
                        </div>
                    </div>
                    
                    <div v-if="selectedFileView.description">
                        <label class="block text-sm font-medium text-gray-500">Description</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedFileView.description }}</p>
                    </div>
                    
                    <div class="border-t pt-4 flex space-x-3">
                        <button @click="downloadFile(selectedFileView)"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </button>
                        <button @click="editFile(selectedFileView)"
                            class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="ml-4 text-lg font-medium text-gray-900">Delete File</h3>
                </div>
                <p class="text-sm text-gray-500 mb-6">
                    Are you sure you want to delete "{{ fileToDelete?.title }}"? This action cannot be undone.
                </p>
                <div class="flex space-x-3">
                    <button @click="closeDeleteModal"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="deleteFile"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Notification Toast -->
        <div v-if="notification.show" 
            :class="notification.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
            class="fixed bottom-4 right-4 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in">
            {{ notification.message }}
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const files = ref([]);
const categories = ref([]);
const showUploadModal = ref(false);
const showEditModal = ref(false);
const showViewModal = ref(false);
const showDeleteModal = ref(false);
const uploading = ref(false);
const selectedFile = ref(null);
const selectedFileView = ref(null);
const fileToDelete = ref(null);

const statistics = reactive({
    total_files: 0,
    recent_files: 0,
    confidential_files: 0,
    total_categories: 0,
    expiring_soon: 0,
    total_size: 0
});

const filters = reactive({
    category_id: '',
    search: '',
    is_confidential: '',
    status: ''
});

const pagination = reactive({
    current_page: 1,
    last_page: 1,
    perPage: 20,
    total: 0,
    from: 0,
    to: 0
});

const uploadForm = reactive({
    title: '',
    description: '',
    category_id: '',
    is_confidential: false,
    expiry_date: ''
});

const editForm = reactive({
    id: null,
    title: '',
    description: '',
    category_id: '',
    is_confidential: false,
    expiry_date: ''
});

const notification = reactive({
    show: false,
    message: '',
    type: 'success'
});

const displayedPages = computed(() => {
    const pages = [];
    const current = pagination.current_page;
    const last = pagination.last_page;
    
    if (last <= 7) {
        for (let i = 1; i <= last; i++) pages.push(i);
    } else {
        if (current <= 4) {
            for (let i = 1; i <= 5; i++) pages.push(i);
            pages.push('...');
            pages.push(last);
        } else if (current >= last - 3) {
            pages.push(1);
            pages.push('...');
            for (let i = last - 4; i <= last; i++) pages.push(i);
        } else {
            pages.push(1);
            pages.push('...');
            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
            pages.push('...');
            pages.push(last);
        }
    }
    return pages;
});

const fetchFiles = async () => {
    try {
        const params = new URLSearchParams();
        params.append('page', pagination.current_page);
        params.append('per_page', pagination.perPage);
        if (filters.category_id) params.append('category_id', filters.category_id);
        if (filters.search) params.append('search', filters.search);
        if (filters.is_confidential) params.append('is_confidential', filters.is_confidential);
        if (filters.status) params.append('status', filters.status);
        
        const response = await axios.get(`files?${params.toString()}`);
        const data = response.data;
        
        if (data.data) {
            files.value = data.data;
            pagination.current_page = data.current_page || 1;
            pagination.last_page = data.last_page || 1;
            pagination.total = data.total || 0;
            pagination.from = data.from || 0;
            pagination.to = data.to || 0;
        } else {
            files.value = data;
        }
        fetchStatistics();
    } catch (error) {
        console.error('Failed to fetch files:', error);
        showNotification('Failed to fetch files', 'error');
    }
};

const fetchStatistics = () => {
    statistics.total_files = pagination.total || files.value.length;
    statistics.confidential_files = files.value.filter(f => f.is_confidential).length;
    statistics.total_categories = categories.value.length;
    
    const sevenDaysAgo = new Date();
    sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
    statistics.recent_files = files.value.filter(f => new Date(f.created_at) > sevenDaysAgo).length;
    
    const thirtyDaysFromNow = new Date();
    thirtyDaysFromNow.setDate(thirtyDaysFromNow.getDate() + 30);
    statistics.expiring_soon = files.value.filter(f => f.expiry_date && new Date(f.expiry_date) < thirtyDaysFromNow).length;
    
    const totalBytes = files.value.reduce((sum, f) => sum + (f.file_size || 0), 0);
    statistics.total_size = Math.round(totalBytes / (1024 * 1024));
};

const fetchCategories = async () => {
    try {
        const response = await axios.get('files/categories');
        categories.value = response.data.data || response.data || [
            { id: 1, name: 'Documents' },
            { id: 2, name: 'Policies' },
            { id: 3, name: 'Contracts' },
            { id: 4, name: 'Reports' }
        ];
        statistics.total_categories = categories.value.length;
    } catch (error) {
        console.error('Failed to fetch categories:', error);
        categories.value = [
            { id: 1, name: 'Documents' },
            { id: 2, name: 'Policies' },
            { id: 3, name: 'Contracts' },
            { id: 4, name: 'Reports' }
        ];
        statistics.total_categories = 4;
    }
};

const handleFileSelect = (event) => {
    selectedFile.value = event.target.files[0];
};

const openUploadModal = () => {
    showUploadModal.value = true;
};

const closeUploadModal = () => {
    showUploadModal.value = false;
    selectedFile.value = null;
    Object.assign(uploadForm, { 
        title: '', 
        description: '', 
        category_id: '', 
        is_confidential: false, 
        expiry_date: '' 
    });
};

const uploadFile = async () => {
    if (!selectedFile.value) {
        showNotification('Please select a file', 'error');
        return;
    }
    
    uploading.value = true;
    const formData = new FormData();
    formData.append('file', selectedFile.value);
    formData.append('title', uploadForm.title);
    formData.append('description', uploadForm.description || '');
    formData.append('category_id', uploadForm.category_id);
    formData.append('employee_id', authStore.user.employee?.id || authStore.user.id);
    formData.append('is_confidential', uploadForm.is_confidential ? '1' : '0');
    if (uploadForm.expiry_date) formData.append('expiry_date', uploadForm.expiry_date);
    
    try {
        await axios.post('files', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        closeUploadModal();
        fetchFiles();
        showNotification('File uploaded successfully', 'success');
    } catch (error) {
        console.error('Failed to upload file:', error);
        showNotification('Failed to upload file', 'error');
    } finally {
        uploading.value = false;
    }
};

const editFile = (file) => {
    closeViewModal();
    editForm.id = file.id;
    editForm.title = file.title;
    editForm.description = file.description || '';
    editForm.category_id = file.category_id;
    editForm.is_confidential = file.is_confidential;
    editForm.expiry_date = file.expiry_date || '';
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    Object.assign(editForm, { 
        id: null,
        title: '', 
        description: '', 
        category_id: '', 
        is_confidential: false, 
        expiry_date: '' 
    });
};

const updateFile = async () => {
    try {
        await axios.put(`files/${editForm.id}`, {
            title: editForm.title,
            description: editForm.description,
            category_id: editForm.category_id,
            is_confidential: editForm.is_confidential ? 1 : 0,
            expiry_date: editForm.expiry_date || null
        });
        closeEditModal();
        fetchFiles();
        showNotification('File updated successfully', 'success');
    } catch (error) {
        console.error('Failed to update file:', error);
        showNotification('Failed to update file', 'error');
    }
};

const viewFile = async (file) => {
    try {
        const response = await axios.get(`files/${file.id}`);
        selectedFileView.value = response.data;
        showViewModal.value = true;
    } catch (error) {
        console.error('Failed to fetch file details:', error);
        showNotification('Failed to load file details', 'error');
    }
};

const closeViewModal = () => {
    showViewModal.value = false;
    selectedFileView.value = null;
};

const confirmDelete = (file) => {
    fileToDelete.value = file;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    fileToDelete.value = null;
};

const deleteFile = async () => {
    try {
        await axios.delete(`files/${fileToDelete.value.id}`);
        closeDeleteModal();
        fetchFiles();
        showNotification('File deleted successfully', 'success');
    } catch (error) {
        console.error('Failed to delete file:', error);
        showNotification('Failed to delete file', 'error');
    }
};

const downloadFile = async (file) => {
    try {
        const response = await axios.get(`files/${file.id}/download`, { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', file.file_name);
        document.body.appendChild(link);
        link.click();
        link.remove();
        showNotification('File downloaded successfully', 'success');
    } catch (error) {
        console.error('Failed to download file:', error);
        showNotification('Failed to download file', 'error');
    }
};

const changePage = (page) => {
    if (page >= 1 && page <= pagination.last_page) {
        pagination.current_page = page;
        fetchFiles();
    }
};

const changePerPage = () => {
    pagination.current_page = 1; // Reset to first page when changing items per page
    fetchFiles();
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-PK');
};

const formatFileSize = (bytes) => {
    if (!bytes) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const showNotification = (message, type = 'success') => {
    notification.message = message;
    notification.type = type;
    notification.show = true;
    setTimeout(() => {
        notification.show = false;
    }, 3000);
};

onMounted(() => {
    fetchCategories();
    fetchFiles();
});
</script>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
