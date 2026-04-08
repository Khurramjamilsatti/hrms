<template>
    <div class="p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
            <button 
                v-if="!isEditing" 
                @click="startEditing" 
                class="inline-flex items-center px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow"
            >
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                </svg>
                Edit Profile
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Days Employed</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ statistics.days_employed }}</h3>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Leave Balance</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ statistics.leave_balance }}</h3>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">This Month Attendance</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ statistics.attendance_count }}</h3>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Pending Requests</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ statistics.pending_requests }}</h3>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Completed Tasks</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ statistics.completed_tasks }}</h3>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Documents</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ statistics.total_documents }}</h3>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Profile Picture & Quick Actions -->
            <div class="space-y-6">
                <!-- Profile Picture Card -->
                <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Profile Picture</h3>
                    <div class="flex flex-col items-center">
                        <div class="relative">
                            <img v-if="profileData.profile_picture" :src="profileData.profile_picture" 
                                class="w-32 h-32 rounded-full object-cover border-4 border-gray-200" alt="Profile" />
                            <div v-else class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold border-4 border-gray-200">
                                {{ getInitials(profileData.name) }}
                            </div>
                            <button v-if="isEditing" @click="triggerFileUpload" class="absolute bottom-0 right-0 bg-gray-900 text-white p-2 rounded-full hover:bg-gray-800 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                            </button>
                        </div>
                        <input ref="fileInput" type="file" accept="image/*" @change="handleProfilePictureChange" class="hidden" />
                        <h4 class="mt-4 text-xl font-semibold text-gray-900">{{ profileData.name }}</h4>
                        <p class="text-sm text-gray-600">{{ profileData.employee?.designation?.title || 'Employee' }}</p>
                        <span class="mt-2 px-3 py-1 text-xs font-semibold rounded-full"
                            :class="profileData.employee?.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                            {{ profileData.employee?.status || 'N/A' }}
                        </span>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <button @click="openPasswordChangeModal" class="w-full px-4 py-2 bg-gray-100 text-gray-900 rounded-lg hover:bg-gray-200 transition-colors text-left flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            Change Password
                        </button>
                        <button @click="navigateToAttendance" class="w-full px-4 py-2 bg-gray-100 text-gray-900 rounded-lg hover:bg-gray-200 transition-colors text-left flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            View Attendance
                        </button>
                        <button @click="navigateToLeaves" class="w-full px-4 py-2 bg-gray-100 text-gray-900 rounded-lg hover:bg-gray-200 transition-colors text-left flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z"/>
                            </svg>
                            My Leaves
                        </button>
                        <button @click="navigateToDocuments" class="w-full px-4 py-2 bg-gray-100 text-gray-900 rounded-lg hover:bg-gray-200 transition-colors text-left flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                            My Documents
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column - Profile Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input v-if="isEditing" v-model="editForm.name" type="text" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            <p v-else class="text-gray-900 py-2">{{ profileData.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <p class="text-gray-900 py-2">{{ profileData.email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input v-if="isEditing" v-model="editForm.phone" type="text" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            <p v-else class="text-gray-900 py-2">{{ profileData.employee?.phone || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input v-if="isEditing" v-model="editForm.date_of_birth" type="date" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            <p v-else class="text-gray-900 py-2">{{ formatDate(profileData.employee?.date_of_birth) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <select v-if="isEditing" v-model="editForm.gender" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <p v-else class="text-gray-900 py-2 capitalize">{{ profileData.employee?.gender || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CNIC/National ID</label>
                            <input v-if="isEditing" v-model="editForm.cnic" type="text" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            <p v-else class="text-gray-900 py-2">{{ profileData.employee?.cnic || 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input v-if="isEditing" v-model="editForm.address" type="text" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            <p v-else class="text-gray-900 py-2">{{ profileData.employee?.address || 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Employment Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                            <p class="text-gray-900 py-2">{{ profileData.employee?.employee_id || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Join Date</label>
                            <p class="text-gray-900 py-2">{{ formatDate(profileData.employee?.join_date) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <p class="text-gray-900 py-2">{{ profileData.employee?.department?.name || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                            <p class="text-gray-900 py-2">{{ profileData.employee?.designation?.title || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employment Type</label>
                            <p class="text-gray-900 py-2 capitalize">{{ profileData.employee?.employment_type || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Manager</label>
                            <p class="text-gray-900 py-2">{{ profileData.employee?.manager?.name || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Work Location</label>
                            <p class="text-gray-900 py-2">{{ profileData.employee?.work_location || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full"
                                :class="profileData.employee?.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                {{ profileData.employee?.status || 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="bg-white rounded-lg shadow border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold mb-4">Emergency Contact</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Name</label>
                            <input v-if="isEditing" v-model="editForm.emergency_contact_name" type="text" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            <p v-else class="text-gray-900 py-2">{{ profileData.employee?.emergency_contact_name || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                            <input v-if="isEditing" v-model="editForm.emergency_contact_phone" type="text" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            <p v-else class="text-gray-900 py-2">{{ profileData.employee?.emergency_contact_phone || 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                            <input v-if="isEditing" v-model="editForm.emergency_contact_relationship" type="text" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            <p v-else class="text-gray-900 py-2">{{ profileData.employee?.emergency_contact_relationship || 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div v-if="isEditing" class="flex space-x-4">
                    <button @click="saveProfile" class="px-6 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow">
                        Save Changes
                    </button>
                    <button @click="cancelEditing" class="px-6 py-2.5 bg-gray-200 text-gray-900 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Password Change Modal -->
        <div v-if="showPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-xl font-semibold mb-4">Change Password</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input v-model="passwordForm.current_password" type="password" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input v-model="passwordForm.new_password" type="password" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input v-model="passwordForm.new_password_confirmation" type="password" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>
                </div>
                <div class="flex space-x-4 mt-6">
                    <button @click="changePassword" class="flex-1 px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors shadow">
                        Change Password
                    </button>
                    <button @click="closePasswordModal" class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-900 font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
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
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const isEditing = ref(false);
const showPasswordModal = ref(false);
const fileInput = ref(null);

const profileData = ref({
    name: '',
    email: '',
    profile_picture: '',
    employee: null
});

const editForm = reactive({
    name: '',
    phone: '',
    date_of_birth: '',
    gender: '',
    cnic: '',
    address: '',
    emergency_contact_name: '',
    emergency_contact_phone: '',
    emergency_contact_relationship: ''
});

const passwordForm = reactive({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
});

const statistics = reactive({
    days_employed: 0,
    leave_balance: 0,
    attendance_count: 0,
    pending_requests: 0,
    completed_tasks: 0,
    total_documents: 0
});

const notification = reactive({
    show: false,
    message: '',
    type: 'success'
});

const fetchProfile = async () => {
    try {
        const response = await axios.get('me');
        profileData.value = response.data;
        
        // Populate edit form
        editForm.name = profileData.value.name;
        editForm.phone = profileData.value.employee?.phone || '';
        editForm.date_of_birth = profileData.value.employee?.date_of_birth || '';
        editForm.gender = profileData.value.employee?.gender || '';
        editForm.cnic = profileData.value.employee?.cnic || '';
        editForm.address = profileData.value.employee?.address || '';
        editForm.emergency_contact_name = profileData.value.employee?.emergency_contact_name || '';
        editForm.emergency_contact_phone = profileData.value.employee?.emergency_contact_phone || '';
        editForm.emergency_contact_relationship = profileData.value.employee?.emergency_contact_relationship || '';
        
        calculateStatistics();
    } catch (error) {
        console.error('Failed to fetch profile:', error);
        showNotification('Failed to load profile', 'error');
    }
};

const calculateStatistics = () => {
    // Calculate days employed
    if (profileData.value.employee?.join_date) {
        const joinDate = new Date(profileData.value.employee.join_date);
        const today = new Date();
        const diffTime = Math.abs(today - joinDate);
        statistics.days_employed = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }
    
    // Mock data for other statistics (replace with actual API calls)
    statistics.leave_balance = 12;
    statistics.attendance_count = 20;
    statistics.pending_requests = 2;
    statistics.completed_tasks = 45;
    statistics.total_documents = 8;
};

const startEditing = () => {
    isEditing.value = true;
};

const cancelEditing = () => {
    isEditing.value = false;
    fetchProfile(); // Reset form
};

const saveProfile = async () => {
    try {
        await axios.put('profile', {
            name: editForm.name,
            employee: {
                phone: editForm.phone,
                date_of_birth: editForm.date_of_birth,
                gender: editForm.gender,
                cnic: editForm.cnic,
                address: editForm.address,
                emergency_contact_name: editForm.emergency_contact_name,
                emergency_contact_phone: editForm.emergency_contact_phone,
                emergency_contact_relationship: editForm.emergency_contact_relationship
            }
        });
        showNotification('Profile updated successfully', 'success');
        isEditing.value = false;
        fetchProfile();
    } catch (error) {
        console.error('Failed to update profile:', error);
        showNotification('Failed to update profile', 'error');
    }
};

const triggerFileUpload = () => {
    fileInput.value.click();
};

const handleProfilePictureChange = async (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    const formData = new FormData();
    formData.append('profile_picture', file);
    
    try {
        const response = await axios.post('profile/picture', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        profileData.value.profile_picture = response.data.profile_picture;
        showNotification('Profile picture updated successfully', 'success');
    } catch (error) {
        console.error('Failed to upload profile picture:', error);
        showNotification('Failed to upload profile picture', 'error');
    }
};

const openPasswordChangeModal = () => {
    showPasswordModal.value = true;
    passwordForm.current_password = '';
    passwordForm.new_password = '';
    passwordForm.new_password_confirmation = '';
};

const closePasswordModal = () => {
    showPasswordModal.value = false;
};

const changePassword = async () => {
    if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
        showNotification('New passwords do not match', 'error');
        return;
    }
    
    try {
        await axios.post('password/change', {
            current_password: passwordForm.current_password,
            new_password: passwordForm.new_password,
            new_password_confirmation: passwordForm.new_password_confirmation
        });
        showNotification('Password changed successfully', 'success');
        closePasswordModal();
    } catch (error) {
        console.error('Failed to change password:', error);
        showNotification(error.response?.data?.message || 'Failed to change password', 'error');
    }
};

const getInitials = (name) => {
    if (!name) return '?';
    return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-PK', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const navigateToAttendance = () => {
    router.push('/attendance');
};

const navigateToLeaves = () => {
    router.push('/leaves');
};

const navigateToDocuments = () => {
    router.push('/files');
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
    fetchProfile();
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
