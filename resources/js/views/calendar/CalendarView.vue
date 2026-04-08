<template>
    <div class="p-6 space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Company Calendar</h1>
                <p class="text-gray-600 mt-1">Manage events, meetings, and schedules</p>
            </div>
            <button v-if="isManager" @click="openCreateModal"
                class="flex items-center space-x-2 px-4 py-2 bg-black hover:bg-gray-800 text-white rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Create Event</span>
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs font-medium">Total Events</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.total_events || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-xs font-medium">Meetings</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.meetings || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                            <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-xs font-medium">Upcoming</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.upcoming_events || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-xs font-medium">Training</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.trainings || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-xs font-medium">Interviews</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.interviews || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-xs font-medium">Holidays</p>
                        <h3 class="text-2xl font-bold mt-1">{{ statistics.holidays || 0 }}</h3>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex justify-between items-center">
                <button @click="previousMonth" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    ← Previous
                </button>
                <h4 class="text-xl font-semibold">{{ currentMonthName }} {{ currentYear }}</h4>
                <button @click="nextMonth" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Next →
                </button>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-7 gap-2">
                <div v-for="day in ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']" :key="day" 
                    class="text-center text-sm font-semibold text-gray-700 py-2">
                    {{ day }}
                </div>
                
                <div v-for="date in calendarDates" :key="date.toString()" 
                    class="border rounded-lg p-2 min-h-32 transition-colors" 
                    :class="[
                        date.getMonth() !== currentMonth ? 'bg-gray-50' : 'bg-white',
                        isToday(date) ? 'bg-blue-50 border-blue-500 border-2' : 'hover:bg-gray-50'
                    ]">
                    <div class="text-sm font-medium mb-2" :class="isToday(date) ? 'text-blue-600' : ''">
                        {{ date.getDate() }}
                    </div>
                    <div class="space-y-1">
                        <div v-for="event in getEventsForDate(date)" :key="event.id" 
                            @click="viewEvent(event)" 
                            class="text-xs rounded px-2 py-1 cursor-pointer truncate transition-all hover:shadow-md" 
                            :class="getEventTypeClass(event.event_type)">
                            {{ event.start_datetime.substr(11, 5) }} {{ event.title }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h4 class="font-semibold text-lg mb-4">Upcoming Events</h4>
            <div class="space-y-3">
                <div v-for="event in upcomingEvents" :key="event.id" 
                    class="border-l-4 pl-4 py-3 cursor-pointer hover:bg-gray-50 rounded-r-lg transition-colors" 
                    :class="getEventTypeBorder(event.event_type)" 
                    @click="viewEvent(event)">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ event.title }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ formatDateTime(event.start_datetime) }}</p>
                            <p v-if="event.location" class="text-xs text-gray-500 mt-1">
                                📍 {{ event.location }}
                            </p>
                        </div>
                        <span class="px-3 py-1 text-xs rounded-full font-semibold" :class="getEventTypeClass(event.event_type)">
                            {{ event.event_type.replace('_', ' ') }}
                        </span>
                    </div>
                </div>
                <div v-if="upcomingEvents.length === 0" class="text-center py-8 text-gray-500">
                    No upcoming events scheduled
                </div>
            </div>
        </div>

        <!-- Create Event Modal -->
        <div v-if="showEventForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <h3 class="text-2xl font-bold mb-4">Create Event</h3>
                
                <form @submit.prevent="createEvent">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Event Title *</label>
                            <input type="text" v-model="eventForm.title" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Event Type *</label>
                            <select v-model="eventForm.event_type" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="meeting">Meeting</option>
                                <option value="training">Training</option>
                                <option value="interview">Interview</option>
                                <option value="holiday">Holiday</option>
                                <option value="company_event">Company Event</option>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time *</label>
                                <input type="datetime-local" v-model="eventForm.start_datetime" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Date & Time *</label>
                                <input type="datetime-local" v-model="eventForm.end_datetime" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location (Optional)</label>
                            <input type="text" v-model="eventForm.location" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meeting Link (Optional)</label>
                            <input type="url" v-model="eventForm.meeting_link" placeholder="https://meet.google.com/..." 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea v-model="eventForm.description" rows="3" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Attendees (Select multiple)</label>
                            <select v-model="eventForm.attendees" multiple 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent h-32">
                                <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                    {{ employee.name }}
                                </option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" @click="closeEventForm" 
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">
                            Create Event
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Event Modal -->
        <div v-if="selectedEvent" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-2xl font-bold">{{ selectedEvent.title }}</h3>
                    <button @click="closeEventView" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-3 text-sm mb-6">
                    <div class="flex items-start">
                        <span class="font-medium w-32">Type:</span> 
                        <span class="px-3 py-1 text-xs rounded-full font-semibold" :class="getEventTypeClass(selectedEvent.event_type)">
                            {{ selectedEvent.event_type.replace('_', ' ') }}
                        </span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-medium w-32">Start:</span> 
                        <span>{{ formatDateTime(selectedEvent.start_datetime) }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-medium w-32">End:</span> 
                        <span>{{ formatDateTime(selectedEvent.end_datetime) }}</span>
                    </div>
                    <div v-if="selectedEvent.location" class="flex items-start">
                        <span class="font-medium w-32">Location:</span> 
                        <span>{{ selectedEvent.location }}</span>
                    </div>
                    <div v-if="selectedEvent.meeting_link" class="flex items-start">
                        <span class="font-medium w-32">Meeting Link:</span> 
                        <a :href="selectedEvent.meeting_link" target="_blank" class="text-blue-600 hover:underline">
                            Join Meeting
                        </a>
                    </div>
                    <div v-if="selectedEvent.description" class="flex items-start">
                        <span class="font-medium w-32">Description:</span> 
                        <span>{{ selectedEvent.description }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="font-medium w-32">Attendees:</span> 
                        <span>{{ selectedEvent.attendees?.length || 0 }} people</span>
                    </div>
                </div>

                <div class="border-t pt-4 flex space-x-3">
                    <button @click="respondToEvent('accepted')" 
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Accept
                    </button>
                    <button @click="respondToEvent('declined')" 
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Decline
                    </button>
                    <button @click="respondToEvent('maybe')" 
                        class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Maybe
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
import { ref, onMounted, computed, reactive } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const events = ref([]);
const employees = ref([]);
const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());
const showEventForm = ref(false);
const selectedEvent = ref(null);

const isManager = computed(() => authStore.isAdmin || authStore.isManager);

const statistics = reactive({
    total_events: 0,
    meetings: 0,
    upcoming_events: 0,
    trainings: 0,
    interviews: 0,
    holidays: 0
});

const notification = reactive({
    show: false,
    message: '',
    type: 'success'
});

const currentMonthName = computed(() => {
    return new Date(currentYear.value, currentMonth.value).toLocaleString('en-US', { month: 'long' });
});

const calendarDates = computed(() => {
    const firstDay = new Date(currentYear.value, currentMonth.value, 1);
    const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0);
    const dates = [];
    
    const startDay = firstDay.getDay();
    for (let i = startDay - 1; i >= 0; i--) {
        dates.push(new Date(currentYear.value, currentMonth.value, -i));
    }
    
    for (let i = 1; i <= lastDay.getDate(); i++) {
        dates.push(new Date(currentYear.value, currentMonth.value, i));
    }
    
    const remainingDays = 42 - dates.length;
    for (let i = 1; i <= remainingDays; i++) {
        dates.push(new Date(currentYear.value, currentMonth.value + 1, i));
    }
    
    return dates;
});

const upcomingEvents = computed(() => {
    const now = new Date();
    return events.value
        .filter(e => new Date(e.start_datetime) >= now)
        .sort((a, b) => new Date(a.start_datetime) - new Date(b.start_datetime))
        .slice(0, 10);
});

const eventForm = reactive({
    title: '',
    event_type: 'meeting',
    start_datetime: '',
    end_datetime: '',
    location: '',
    meeting_link: '',
    description: '',
    attendees: []
});

const fetchEvents = async () => {
    try {
        const startDate = `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-01`;
        const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
        const endDate = `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${lastDay}`;
        
        const response = await axios.get(`calendar/events?start_date=${startDate}&end_date=${endDate}`);
        events.value = response.data.data || response.data;
        calculateStatistics();
    } catch (error) {
        console.error('Failed to fetch events:', error);
        showNotification('Failed to fetch events', 'error');
    }
};

const calculateStatistics = () => {
    statistics.total_events = events.value.length;
    statistics.meetings = events.value.filter(e => e.event_type === 'meeting').length;
    statistics.trainings = events.value.filter(e => e.event_type === 'training').length;
    statistics.interviews = events.value.filter(e => e.event_type === 'interview').length;
    statistics.holidays = events.value.filter(e => e.event_type === 'holiday').length;
    
    const now = new Date();
    statistics.upcoming_events = events.value.filter(e => new Date(e.start_datetime) >= now).length;
};

const fetchEmployees = async () => {
    try {
        const response = await axios.get('employees');
        employees.value = response.data.data || response.data;
    } catch (error) {
        console.error('Failed to fetch employees:', error);
    }
};

const openCreateModal = () => {
    showEventForm.value = true;
};

const closeEventForm = () => {
    showEventForm.value = false;
    Object.assign(eventForm, {
        title: '',
        event_type: 'meeting',
        start_datetime: '',
        end_datetime: '',
        location: '',
        meeting_link: '',
        description: '',
        attendees: []
    });
};

const createEvent = async () => {
    try {
        await axios.post('calendar/events', eventForm);
        closeEventForm();
        fetchEvents();
        showNotification('Event created successfully', 'success');
    } catch (error) {
        console.error('Failed to create event:', error);
        showNotification('Failed to create event', 'error');
    }
};

const viewEvent = async (event) => {
    try {
        const response = await axios.get(`calendar/events/${event.id}`);
        selectedEvent.value = response.data;
    } catch (error) {
        console.error('Failed to fetch event details:', error);
        showNotification('Failed to load event details', 'error');
    }
};

const closeEventView = () => {
    selectedEvent.value = null;
};

const respondToEvent = async (status) => {
    try {
        await axios.post(`calendar/events/${selectedEvent.value.id}/respond`, { status });
        closeEventView();
        fetchEvents();
        showNotification(`Event response updated to: ${status}`, 'success');
    } catch (error) {
        console.error('Failed to respond to event:', error);
        showNotification('Failed to update response', 'error');
    }
};

const previousMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
    fetchEvents();
};

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
    fetchEvents();
};

const getEventsForDate = (date) => {
    const dateStr = date.toISOString().substr(0, 10);
    return events.value.filter(event => event.start_datetime.substr(0, 10) === dateStr);
};

const isToday = (date) => {
    const today = new Date();
    return date.toDateString() === today.toDateString();
};

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString('en-PK', { dateStyle: 'medium', timeStyle: 'short' });
};

const getEventTypeClass = (type) => {
    const classes = {
        meeting: 'bg-blue-100 text-blue-800',
        training: 'bg-purple-100 text-purple-800',
        interview: 'bg-green-100 text-green-800',
        holiday: 'bg-red-100 text-red-800',
        company_event: 'bg-yellow-100 text-yellow-800'
    };
    return classes[type] || 'bg-gray-100 text-gray-800';
};

const getEventTypeBorder = (type) => {
    const borders = {
        meeting: 'border-blue-500',
        training: 'border-purple-500',
        interview: 'border-green-500',
        holiday: 'border-red-500',
        company_event: 'border-yellow-500'
    };
    return borders[type] || 'border-gray-500';
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
    fetchEvents();
    fetchEmployees();
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
