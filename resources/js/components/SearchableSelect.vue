<template>
  <div class="relative" ref="dropdown">
    <div @click="toggleDropdown" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white cursor-pointer hover:border-gray-400 focus-within:ring-2 focus-within:ring-gray-900 focus-within:border-transparent transition-all">
      <div class="flex items-center justify-between">
        <span v-if="selectedOption" class="text-gray-900 truncate">{{ selectedOption.label }}</span>
        <span v-else class="text-gray-400">{{ placeholder }}</span>
        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </div>
    </div>

    <!-- Dropdown -->
    <div v-if="isOpen" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-80 overflow-hidden">
      <!-- Search Input -->
      <div class="p-2 border-b border-gray-200 bg-gray-50">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            ref="searchInput"
            v-model="searchQuery"
            type="text"
            :placeholder="searchPlaceholder"
            class="w-full pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent"
            @click.stop
          />
        </div>
      </div>

      <!-- Options List -->
      <div class="overflow-y-auto max-h-60">
        <div v-if="filteredOptions.length === 0" class="px-4 py-3 text-sm text-gray-500 text-center">
          No results found
        </div>
        <div
          v-for="option in filteredOptions"
          :key="option.value"
          @click="selectOption(option)"
          class="px-4 py-2 text-sm hover:bg-gray-100 cursor-pointer transition-colors"
          :class="{ 'bg-blue-50 text-blue-700 font-medium': modelValue === option.value }"
        >
          <div class="flex items-center">
            <svg v-if="modelValue === option.value" class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="truncate">{{ option.label }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  options: {
    type: Array,
    required: true,
    // Array of { value, label }
  },
  placeholder: {
    type: String,
    default: 'Select option...'
  },
  searchPlaceholder: {
    type: String,
    default: 'Search...'
  }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');
const dropdown = ref(null);
const searchInput = ref(null);

const selectedOption = computed(() => {
  return props.options.find(opt => opt.value === props.modelValue);
});

const filteredOptions = computed(() => {
  if (!searchQuery.value) return props.options;
  const query = searchQuery.value.toLowerCase();
  return props.options.filter(opt => 
    opt.label.toLowerCase().includes(query)
  );
});

const toggleDropdown = async () => {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    await nextTick();
    searchInput.value?.focus();
  }
};

const selectOption = (option) => {
  emit('update:modelValue', option.value);
  isOpen.value = false;
  searchQuery.value = '';
};

const handleClickOutside = (event) => {
  if (dropdown.value && !dropdown.value.contains(event.target)) {
    isOpen.value = false;
    searchQuery.value = '';
  }
};

watch(() => props.options, () => {
  // Reset search when options change
  if (isOpen.value) {
    searchQuery.value = '';
  }
});

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
