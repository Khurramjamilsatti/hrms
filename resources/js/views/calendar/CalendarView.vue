<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Company Calendar</h1>
        <p class="text-sm text-gray-500 mt-1">Meetings, trainings, interviews, holidays, birthdays, and company events</p>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <button
          @click="goToToday"
          class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
        >
          Today
        </button>
        <button
          v-if="canManage"
          @click="openCreateModal()"
          class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Create Event
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
      <div v-for="stat in statCards" :key="stat.key" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="flex items-center gap-3">
          <span class="w-2.5 h-2.5 rounded-full shrink-0" :class="stat.dot"></span>
          <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ stat.label }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ statistics[stat.key] || 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
      <!-- Main calendar -->
      <div class="xl:col-span-3 space-y-4">
        <!-- Toolbar -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-2">
              <button @click="previousMonth" class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50" title="Previous month">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <h2 class="min-w-[180px] text-center text-xl font-bold text-gray-900">{{ currentMonthName }} {{ currentYear }}</h2>
              <button @click="nextMonth" class="p-2 rounded-lg border border-gray-200 hover:bg-gray-50" title="Next month">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>

            <div class="flex flex-wrap gap-2">
              <button
                v-for="type in eventTypes"
                :key="type.value"
                type="button"
                @click="toggleTypeFilter(type.value)"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition-all"
                :class="isTypeActive(type.value) ? type.activeClass : 'bg-white text-gray-500 border-gray-200 hover:border-gray-300'"
              >
                <span class="w-2 h-2 rounded-full" :class="type.dot"></span>
                {{ type.label }}
              </button>
            </div>
          </div>
        </div>

        <!-- Grid -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
          <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50">
            <div
              v-for="day in weekDays"
              :key="day"
              class="px-2 py-3 text-center text-xs font-bold uppercase tracking-wider text-gray-500"
            >
              {{ day }}
            </div>
          </div>

          <div class="grid grid-cols-7">
            <div
              v-for="(cell, index) in calendarCells"
              :key="index"
              @click="onDayClick(cell)"
              class="min-h-[110px] md:min-h-[130px] border-b border-r border-gray-100 p-1.5 md:p-2 cursor-pointer transition-all relative group"
              :class="dayCellClass(cell)"
            >
              <div class="flex items-center justify-between mb-1">
                <span
                  class="inline-flex items-center justify-center w-7 h-7 text-sm font-semibold rounded-full"
                  :class="dayNumberClass(cell)"
                >
                  {{ cell.date.getDate() }}
                </span>
                <button
                  v-if="canManage && cell.inMonth"
                  type="button"
                  @click.stop="openCreateModal(cell.date)"
                  class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-gray-900 transition-opacity"
                  title="Add event"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>
              </div>

              <div class="space-y-1">
                <button
                  v-for="event in cell.visibleEvents"
                  :key="event.id"
                  type="button"
                  @click.stop="viewEvent(event)"
                  class="w-full text-left text-[11px] md:text-xs font-medium rounded-md px-1.5 py-1 truncate shadow-sm hover:shadow transition-shadow border"
                  :class="eventChipClass(event.event_type)"
                  :title="event.title"
                >
                  <span v-if="!event.is_all_day" class="opacity-90">{{ formatTime(event.start_datetime) }}</span>
                  {{ event.title }}
                </button>
                <button
                  v-if="cell.moreCount > 0"
                  type="button"
                  @click.stop="selectDay(cell.date)"
                  class="w-full text-[11px] font-semibold text-gray-600 hover:text-gray-900 px-1"
                >
                  +{{ cell.moreCount }} more
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Legend -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Event Colors</p>
          <div class="flex flex-wrap gap-3">
            <div v-for="type in eventTypes" :key="`legend-${type.value}`" class="inline-flex items-center gap-2 text-sm text-gray-700">
              <span class="w-3 h-3 rounded" :class="type.swatch"></span>
              {{ type.label }}
            </div>
          </div>
        </div>
      </div>

      <!-- Side panel -->
      <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-900">
              {{ selectedDayLabel }}
            </h3>
            <button
              v-if="canManage"
              type="button"
              @click="openCreateModal(selectedDay)"
              class="text-sm font-medium text-gray-900 hover:underline"
            >
              Add
            </button>
          </div>

          <div v-if="selectedDayEvents.length" class="space-y-3 max-h-[360px] overflow-y-auto pr-1">
            <button
              v-for="event in selectedDayEvents"
              :key="`side-${event.id}`"
              type="button"
              @click="viewEvent(event)"
              class="w-full text-left rounded-lg border border-gray-100 p-3 hover:border-gray-300 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-start gap-3">
                <span class="mt-1 w-2.5 h-2.5 rounded-full shrink-0" :class="typeMeta(event.event_type).dot"></span>
                <div class="min-w-0">
                  <p class="text-sm font-semibold text-gray-900 truncate">{{ event.title }}</p>
                  <p class="text-xs text-gray-500 mt-0.5">
                    {{ event.is_all_day ? 'All day' : `${formatTime(event.start_datetime)} – ${formatTime(event.end_datetime)}` }}
                  </p>
                  <p v-if="event.location" class="text-xs text-gray-500 mt-1 truncate">{{ event.location }}</p>
                </div>
              </div>
            </button>
          </div>
          <p v-else class="text-sm text-gray-500 py-6 text-center">No events on this day</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
          <h3 class="text-base font-bold text-gray-900 mb-4">Upcoming</h3>
          <div v-if="upcomingEvents.length" class="space-y-3">
            <button
              v-for="event in upcomingEvents"
              :key="`up-${event.id}`"
              type="button"
              @click="viewEvent(event)"
              class="w-full text-left border-l-4 pl-3 py-2 hover:bg-gray-50 rounded-r-lg transition-colors"
              :class="typeMeta(event.event_type).border"
            >
              <p class="text-sm font-semibold text-gray-900 truncate">{{ event.title }}</p>
              <p class="text-xs text-gray-500 mt-0.5">{{ formatDateTime(event.start_datetime) }}</p>
            </button>
          </div>
          <p v-else class="text-sm text-gray-500 py-6 text-center">No upcoming events</p>
        </div>
      </div>
    </div>

    <!-- Create / Edit Modal -->
    <div v-if="showEventForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white">
          <h3 class="text-lg font-bold text-gray-900">{{ editingEventId ? 'Edit Event' : 'Create Event' }}</h3>
          <button type="button" @click="closeEventForm" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>

        <form @submit.prevent="saveEvent" class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Title *</label>
            <input v-model="eventForm.title" required type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Event Type *</label>
              <select v-model="eventForm.event_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900">
                <option v-for="type in creatableEventTypes" :key="type.value" :value="type.value">{{ type.label }}</option>
              </select>
              <div class="mt-2 inline-flex items-center gap-2 text-xs text-gray-600">
                <span class="w-3 h-3 rounded" :class="typeMeta(eventForm.event_type).swatch"></span>
                Will appear as {{ typeMeta(eventForm.event_type).label }}
              </div>
            </div>
            <div class="flex items-end">
              <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input v-model="eventForm.is_all_day" type="checkbox" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900" />
                All-day event
              </label>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Start *</label>
              <input
                v-model="eventForm.start_datetime"
                :type="eventForm.is_all_day ? 'date' : 'datetime-local'"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
              />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">End *</label>
              <input
                v-model="eventForm.end_datetime"
                :type="eventForm.is_all_day ? 'date' : 'datetime-local'"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
              <input v-model="eventForm.location" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Meeting link</label>
              <input v-model="eventForm.meeting_link" type="url" placeholder="https://" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
            <textarea v-model="eventForm.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"></textarea>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Attendees</label>
            <div ref="attendeePickerRef" class="relative">
              <input
                v-model="attendeeSearch"
                @input="filterAttendees"
                @focus="openAttendeeDropdown"
                type="text"
                placeholder="Search employees to add..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
                autocomplete="off"
              />
              <div
                v-if="showAttendeeDropdown && filteredAttendees.length"
                class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto"
              >
                <button
                  v-for="emp in filteredAttendees"
                  :key="emp.id"
                  type="button"
                  @mousedown.prevent="addAttendee(emp)"
                  class="w-full text-left px-4 py-2 hover:bg-gray-100 text-sm"
                >
                  {{ emp.first_name }} {{ emp.last_name }}
                  <span class="text-xs text-gray-500"> · {{ emp.employee_code }}</span>
                </button>
              </div>
            </div>
            <div v-if="selectedAttendees.length" class="flex flex-wrap gap-2 mt-2">
              <span
                v-for="emp in selectedAttendees"
                :key="`att-${emp.id}`"
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 text-xs font-medium text-gray-800"
              >
                {{ emp.first_name }} {{ emp.last_name }}
                <button type="button" @click="removeAttendee(emp.id)" class="text-gray-500 hover:text-red-600">×</button>
              </span>
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-2">
            <button type="button" @click="closeEventForm" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
            <button type="submit" :disabled="saving" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-50">
              {{ saving ? 'Saving...' : (editingEventId ? 'Update Event' : 'Create Event') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Event Modal -->
    <div v-if="selectedEvent" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-start">
          <div class="pr-4">
            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full border mb-2" :class="eventChipClass(selectedEvent.event_type)">
              {{ typeMeta(selectedEvent.event_type).label }}
            </span>
            <h3 class="text-xl font-bold text-gray-900">{{ selectedEvent.title }}</h3>
          </div>
          <button type="button" @click="closeEventView" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
          </button>
        </div>

        <div class="px-6 py-5 space-y-3 text-sm">
          <p><span class="font-semibold text-gray-700">When:</span> {{ formatDateTime(selectedEvent.start_datetime) }} – {{ formatDateTime(selectedEvent.end_datetime) }}</p>
          <p v-if="selectedEvent.location"><span class="font-semibold text-gray-700">Location:</span> {{ selectedEvent.location }}</p>
          <p v-if="selectedEvent.meeting_link">
            <span class="font-semibold text-gray-700">Link:</span>
            <a :href="selectedEvent.meeting_link" target="_blank" class="text-blue-600 hover:underline ml-1">Join meeting</a>
          </p>
          <p v-if="selectedEvent.description"><span class="font-semibold text-gray-700">Details:</span> {{ selectedEvent.description }}</p>
          <div v-if="!isReadonlyEvent(selectedEvent)">
            <p class="font-semibold text-gray-700 mb-2">Attendees ({{ selectedEvent.attendees?.length || 0 }})</p>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="att in selectedEvent.attendees || []"
                :key="att.id"
                class="px-2.5 py-1 rounded-full bg-gray-100 text-xs text-gray-800"
              >
                {{ att.employee?.first_name }} {{ att.employee?.last_name }}
                <span class="text-gray-500">({{ att.status }})</span>
              </span>
              <span v-if="!(selectedEvent.attendees || []).length" class="text-gray-500 text-xs">No attendees</span>
            </div>
          </div>
        </div>

        <div v-if="!isReadonlyEvent(selectedEvent)" class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-wrap gap-2 justify-between">
          <div class="flex flex-wrap gap-2">
            <button type="button" @click="respondToEvent('accepted')" class="px-3 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Accept</button>
            <button type="button" @click="respondToEvent('declined')" class="px-3 py-2 text-sm font-medium text-white bg-rose-600 rounded-lg hover:bg-rose-700">Decline</button>
            <button type="button" @click="respondToEvent('maybe')" class="px-3 py-2 text-sm font-medium text-gray-800 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">Maybe</button>
          </div>
          <div v-if="canManage" class="flex gap-2">
            <button type="button" @click="editEvent(selectedEvent)" class="px-3 py-2 text-sm font-medium text-gray-800 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">Edit</button>
            <button type="button" @click="deleteEvent(selectedEvent)" class="px-3 py-2 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100">Delete</button>
          </div>
        </div>
        <div v-else class="px-6 py-4 border-t border-gray-200 bg-gray-50">
          <p class="text-sm text-gray-500">Birthdays are generated from employee profiles and cannot be edited here.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted, onBeforeUnmount, watch } from 'vue';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';
import { useNotification } from '@/composables/useNotification';
import { useDialog } from '@/composables/useDialog';

const authStore = useAuthStore();
const { success, error: showError } = useNotification();
const { confirm } = useDialog();

const events = ref([]);
const employees = ref([]);
const filteredAttendees = ref([]);
const selectedAttendees = ref([]);
const attendeeSearch = ref('');
const showAttendeeDropdown = ref(false);
const attendeePickerRef = ref(null);
const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());
const selectedDay = ref(new Date());
const showEventForm = ref(false);
const selectedEvent = ref(null);
const editingEventId = ref(null);
const saving = ref(false);
const activeTypeFilters = ref([]);

const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const eventTypes = [
  { value: 'meeting', label: 'Meeting', dot: 'bg-sky-500', swatch: 'bg-sky-500', border: 'border-sky-500', activeClass: 'bg-sky-50 text-sky-800 border-sky-300', chip: 'bg-sky-500 text-white border-sky-600', dayTint: 'bg-sky-50/80' },
  { value: 'training', label: 'Training', dot: 'bg-violet-500', swatch: 'bg-violet-500', border: 'border-violet-500', activeClass: 'bg-violet-50 text-violet-800 border-violet-300', chip: 'bg-violet-500 text-white border-violet-600', dayTint: 'bg-violet-50/80' },
  { value: 'interview', label: 'Interview', dot: 'bg-emerald-500', swatch: 'bg-emerald-500', border: 'border-emerald-500', activeClass: 'bg-emerald-50 text-emerald-800 border-emerald-300', chip: 'bg-emerald-500 text-white border-emerald-600', dayTint: 'bg-emerald-50/80' },
  { value: 'holiday', label: 'Holiday', dot: 'bg-rose-500', swatch: 'bg-rose-500', border: 'border-rose-500', activeClass: 'bg-rose-50 text-rose-800 border-rose-300', chip: 'bg-rose-500 text-white border-rose-600', dayTint: 'bg-rose-50/70' },
  { value: 'birthday', label: 'Birthday', dot: 'bg-pink-500', swatch: 'bg-pink-500', border: 'border-pink-500', activeClass: 'bg-pink-50 text-pink-800 border-pink-300', chip: 'bg-pink-500 text-white border-pink-600', dayTint: 'bg-pink-50/80' },
  { value: 'company_event', label: 'Company', dot: 'bg-amber-500', swatch: 'bg-amber-500', border: 'border-amber-500', activeClass: 'bg-amber-50 text-amber-900 border-amber-300', chip: 'bg-amber-500 text-white border-amber-600', dayTint: 'bg-amber-50/80' },
  { value: 'other', label: 'Other', dot: 'bg-slate-500', swatch: 'bg-slate-500', border: 'border-slate-500', activeClass: 'bg-slate-50 text-slate-800 border-slate-300', chip: 'bg-slate-500 text-white border-slate-600', dayTint: 'bg-slate-50/80' },
];

const creatableEventTypes = computed(() => eventTypes.filter((t) => t.value !== 'birthday'));

const statistics = reactive({
  total_events: 0,
  meetings: 0,
  upcoming_events: 0,
  trainings: 0,
  interviews: 0,
  birthdays: 0,
});

const eventForm = reactive({
  title: '',
  event_type: 'meeting',
  start_datetime: '',
  end_datetime: '',
  location: '',
  meeting_link: '',
  description: '',
  is_all_day: false,
  attendees: [],
});

const canManage = computed(() =>
  ['admin', 'super_admin', 'hr_admin', 'manager', 'section_head'].includes(authStore.user?.role)
);

const statCards = computed(() => [
  { key: 'total_events', label: 'Total', dot: 'bg-gray-800' },
  { key: 'meetings', label: 'Meetings', dot: 'bg-sky-500' },
  { key: 'upcoming_events', label: 'Upcoming', dot: 'bg-amber-500' },
  { key: 'trainings', label: 'Training', dot: 'bg-violet-500' },
  { key: 'interviews', label: 'Interviews', dot: 'bg-emerald-500' },
  { key: 'birthdays', label: 'Birthdays', dot: 'bg-pink-500' },
]);

const currentMonthName = computed(() =>
  new Date(currentYear.value, currentMonth.value).toLocaleString('en-US', { month: 'long' })
);

const selectedDayLabel = computed(() =>
  selectedDay.value.toLocaleDateString('en-PK', { weekday: 'long', month: 'short', day: 'numeric' })
);

const filteredEvents = computed(() => {
  if (!activeTypeFilters.value.length) return events.value;
  return events.value.filter((e) => activeTypeFilters.value.includes(e.event_type));
});

const upcomingEvents = computed(() => {
  const now = new Date();
  return filteredEvents.value
    .filter((e) => new Date(e.start_datetime) >= now)
    .sort((a, b) => new Date(a.start_datetime) - new Date(b.start_datetime))
    .slice(0, 8);
});

const selectedDayEvents = computed(() => getEventsForDate(selectedDay.value));

const calendarCells = computed(() => {
  const firstDay = new Date(currentYear.value, currentMonth.value, 1);
  const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0);
  const cells = [];

  for (let i = firstDay.getDay() - 1; i >= 0; i--) {
    cells.push(buildCell(new Date(currentYear.value, currentMonth.value, -i), false));
  }
  for (let d = 1; d <= lastDay.getDate(); d++) {
    cells.push(buildCell(new Date(currentYear.value, currentMonth.value, d), true));
  }
  const remaining = 42 - cells.length;
  for (let i = 1; i <= remaining; i++) {
    cells.push(buildCell(new Date(currentYear.value, currentMonth.value + 1, i), false));
  }
  return cells;
});

const typeMeta = (type) => eventTypes.find((t) => t.value === type) || eventTypes[eventTypes.length - 1];

const eventChipClass = (type) => typeMeta(type).chip;

const isTypeActive = (type) => !activeTypeFilters.value.length || activeTypeFilters.value.includes(type);

const toggleTypeFilter = (type) => {
  if (!activeTypeFilters.value.length) {
    activeTypeFilters.value = [type];
    return;
  }
  if (activeTypeFilters.value.includes(type)) {
    activeTypeFilters.value = activeTypeFilters.value.filter((t) => t !== type);
  } else {
    activeTypeFilters.value = [...activeTypeFilters.value, type];
  }
};

const toLocalDateKey = (date) => {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, '0');
  const d = String(date.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
};

const eventDateKey = (datetime) => {
  const date = new Date(datetime);
  if (Number.isNaN(date.getTime())) return String(datetime).slice(0, 10);
  return toLocalDateKey(date);
};

const getEventsForDate = (date) => {
  const key = toLocalDateKey(date);
  return filteredEvents.value
    .filter((event) => {
      const startKey = eventDateKey(event.start_datetime);
      const endKey = eventDateKey(event.end_datetime || event.start_datetime);
      return key >= startKey && key <= endKey;
    })
    .sort((a, b) => new Date(a.start_datetime) - new Date(b.start_datetime));
};

const buildCell = (date, inMonth) => {
  const dayEvents = getEventsForDate(date);
  return {
    date,
    inMonth,
    events: dayEvents,
    visibleEvents: dayEvents.slice(0, 3),
    moreCount: Math.max(0, dayEvents.length - 3),
    primaryType: dayEvents[0]?.event_type || null,
  };
};

const isToday = (date) => date.toDateString() === new Date().toDateString();
const isSelectedDay = (date) => date.toDateString() === selectedDay.value.toDateString();

const dayCellClass = (cell) => {
  const classes = [];
  if (!cell.inMonth) classes.push('bg-gray-50/70 text-gray-400');
  if (cell.inMonth && cell.events.length && cell.primaryType) {
    classes.push(typeMeta(cell.primaryType).dayTint);
  }
  if (isToday(cell.date)) classes.push('ring-2 ring-inset ring-gray-900');
  if (isSelectedDay(cell.date)) classes.push('bg-gray-100');
  if (cell.inMonth) classes.push('hover:bg-gray-50');
  return classes;
};

const dayNumberClass = (cell) => {
  if (isToday(cell.date)) return 'bg-gray-900 text-white';
  if (!cell.inMonth) return 'text-gray-400';
  if (cell.events.length) return 'text-gray-900';
  return 'text-gray-700';
};

const formatTime = (datetime) =>
  new Date(datetime).toLocaleTimeString('en-PK', { hour: '2-digit', minute: '2-digit' });

const formatDateTime = (datetime) =>
  new Date(datetime).toLocaleString('en-PK', { dateStyle: 'medium', timeStyle: 'short' });

const toInputValue = (datetime, allDay = false) => {
  const d = new Date(datetime);
  if (Number.isNaN(d.getTime())) return '';
  const pad = (n) => String(n).padStart(2, '0');
  const date = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
  if (allDay) return date;
  return `${date}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
};

const normalizePayloadDates = () => {
  const payload = {
    title: eventForm.title,
    event_type: eventForm.event_type,
    location: eventForm.location || null,
    meeting_link: eventForm.meeting_link || null,
    description: eventForm.description || null,
    is_all_day: !!eventForm.is_all_day,
    attendees: eventForm.attendees,
  };

  if (eventForm.is_all_day) {
    payload.start_datetime = `${eventForm.start_datetime}T00:00:00`;
    payload.end_datetime = `${eventForm.end_datetime}T23:59:00`;
  } else {
    payload.start_datetime = eventForm.start_datetime;
    payload.end_datetime = eventForm.end_datetime;
  }
  return payload;
};

const fetchEvents = async () => {
  try {
    const startDate = `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-01`;
    const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
    const endDate = `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;

    const response = await axios.get('/calendar/events', {
      params: { start_date: startDate, end_date: endDate, calendar: 1 },
    });
    events.value = response.data.data || response.data || [];
    calculateStatistics();
  } catch (err) {
    console.error(err);
    showError('Failed to load calendar events');
  }
};

const calculateStatistics = () => {
  statistics.total_events = events.value.length;
  statistics.meetings = events.value.filter((e) => e.event_type === 'meeting').length;
  statistics.trainings = events.value.filter((e) => e.event_type === 'training').length;
  statistics.interviews = events.value.filter((e) => e.event_type === 'interview').length;
  statistics.birthdays = events.value.filter((e) => e.event_type === 'birthday').length;
  const now = new Date();
  statistics.upcoming_events = events.value.filter((e) => new Date(e.start_datetime) >= now).length;
};

const isReadonlyEvent = (event) =>
  !!event?.readonly || event?.event_type === 'birthday' || !!event?.is_system || String(event?.id || '').startsWith('birthday-');

const fetchEmployees = async () => {
  try {
    const response = await axios.get('/employees', { params: { per_page: 200, employment_status: 'active' } });
    employees.value = response.data.data || response.data || [];
    filteredAttendees.value = employees.value.slice(0, 40);
  } catch (err) {
    console.error(err);
  }
};

const filterAttendees = (keepOpen = true) => {
  const q = attendeeSearch.value.toLowerCase().trim();
  const selectedIds = new Set(selectedAttendees.value.map((e) => e.id));
  filteredAttendees.value = employees.value
    .filter((emp) => !selectedIds.has(emp.id))
    .filter((emp) => {
      if (!q) return true;
      const name = `${emp.first_name || ''} ${emp.last_name || ''}`.toLowerCase();
      return name.includes(q) || String(emp.employee_code || '').toLowerCase().includes(q);
    })
    .slice(0, 40);
  if (keepOpen) {
    showAttendeeDropdown.value = true;
  }
};

const openAttendeeDropdown = () => {
  filterAttendees(true);
};

const closeAttendeeDropdown = () => {
  showAttendeeDropdown.value = false;
};

const handleOutsideClick = (event) => {
  if (!showAttendeeDropdown.value) return;
  const root = attendeePickerRef.value;
  if (root && !root.contains(event.target)) {
    closeAttendeeDropdown();
  }
};

const addAttendee = (emp) => {
  if (!selectedAttendees.value.find((e) => e.id === emp.id)) {
    selectedAttendees.value.push(emp);
    eventForm.attendees = selectedAttendees.value.map((e) => e.id);
  }
  attendeeSearch.value = '';
  filterAttendees(false);
  closeAttendeeDropdown();
};

const removeAttendee = (id) => {
  selectedAttendees.value = selectedAttendees.value.filter((e) => e.id !== id);
  eventForm.attendees = selectedAttendees.value.map((e) => e.id);
  filterAttendees(showAttendeeDropdown.value);
};

const openCreateModal = (date = null) => {
  editingEventId.value = null;
  const base = date ? new Date(date) : new Date(selectedDay.value);
  const pad = (n) => String(n).padStart(2, '0');
  const dateStr = `${base.getFullYear()}-${pad(base.getMonth() + 1)}-${pad(base.getDate())}`;
  Object.assign(eventForm, {
    title: '',
    event_type: 'meeting',
    start_datetime: `${dateStr}T10:00`,
    end_datetime: `${dateStr}T11:00`,
    location: '',
    meeting_link: '',
    description: '',
    is_all_day: false,
    attendees: [],
  });
  selectedAttendees.value = [];
  attendeeSearch.value = '';
  showEventForm.value = true;
};

const editEvent = (event) => {
  editingEventId.value = event.id;
  Object.assign(eventForm, {
    title: event.title,
    event_type: event.event_type,
    start_datetime: toInputValue(event.start_datetime, event.is_all_day),
    end_datetime: toInputValue(event.end_datetime, event.is_all_day),
    location: event.location || '',
    meeting_link: event.meeting_link || '',
    description: event.description || '',
    is_all_day: !!event.is_all_day,
    attendees: (event.attendees || []).map((a) => a.employee_id || a.employee?.id).filter(Boolean),
  });
  selectedAttendees.value = (event.attendees || [])
    .map((a) => a.employee)
    .filter(Boolean);
  selectedEvent.value = null;
  showEventForm.value = true;
};

const closeEventForm = () => {
  showEventForm.value = false;
  editingEventId.value = null;
  closeAttendeeDropdown();
};

const saveEvent = async () => {
  saving.value = true;
  try {
    const payload = normalizePayloadDates();
    if (editingEventId.value) {
      await axios.put(`/calendar/events/${editingEventId.value}`, payload);
      success('Event updated');
    } else {
      await axios.post('/calendar/events', payload);
      success('Event created');
    }
    closeEventForm();
    await fetchEvents();
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to save event');
  } finally {
    saving.value = false;
  }
};

const viewEvent = async (event) => {
  if (isReadonlyEvent(event)) {
    selectedEvent.value = event;
    return;
  }
  try {
    const response = await axios.get(`/calendar/events/${event.id}`);
    selectedEvent.value = response.data;
  } catch (err) {
    showError('Failed to load event details');
  }
};

const closeEventView = () => {
  selectedEvent.value = null;
};

const respondToEvent = async (status) => {
  try {
    await axios.post(`/calendar/events/${selectedEvent.value.id}/respond`, { status });
    success(`Response saved: ${status}`);
    closeEventView();
    await fetchEvents();
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to update response');
  }
};

const deleteEvent = async (event) => {
  if (!(await confirm({
    title: 'Delete event?',
    message: `Delete "${event.title}" permanently?`,
    confirmText: 'Delete',
    cancelText: 'Cancel',
    variant: 'danger',
  }))) return;

  try {
    await axios.delete(`/calendar/events/${event.id}`);
    success('Event deleted');
    closeEventView();
    await fetchEvents();
  } catch (err) {
    showError(err.response?.data?.message || 'Failed to delete event');
  }
};

const onDayClick = (cell) => {
  selectDay(cell.date);
};

const selectDay = (date) => {
  selectedDay.value = new Date(date.getFullYear(), date.getMonth(), date.getDate());
};

const previousMonth = () => {
  if (currentMonth.value === 0) {
    currentMonth.value = 11;
    currentYear.value -= 1;
  } else {
    currentMonth.value -= 1;
  }
};

const nextMonth = () => {
  if (currentMonth.value === 11) {
    currentMonth.value = 0;
    currentYear.value += 1;
  } else {
    currentMonth.value += 1;
  }
};

const goToToday = () => {
  const today = new Date();
  currentMonth.value = today.getMonth();
  currentYear.value = today.getFullYear();
  selectedDay.value = today;
};

watch([currentMonth, currentYear], () => {
  fetchEvents();
});

watch(
  () => eventForm.is_all_day,
  (allDay) => {
    if (!eventForm.start_datetime) return;
    if (allDay) {
      eventForm.start_datetime = String(eventForm.start_datetime).slice(0, 10);
      eventForm.end_datetime = String(eventForm.end_datetime || eventForm.start_datetime).slice(0, 10);
    } else if (eventForm.start_datetime.length === 10) {
      eventForm.start_datetime = `${eventForm.start_datetime}T09:00`;
      eventForm.end_datetime = `${eventForm.end_datetime || eventForm.start_datetime.slice(0, 10)}T10:00`;
    }
  }
);

onMounted(async () => {
  selectedDay.value = new Date();
  document.addEventListener('mousedown', handleOutsideClick);
  await Promise.all([fetchEvents(), fetchEmployees()]);
});

onBeforeUnmount(() => {
  document.removeEventListener('mousedown', handleOutsideClick);
});
</script>
