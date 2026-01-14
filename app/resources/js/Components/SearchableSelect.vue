<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Pilih atau ketik untuk mencari...',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    labelKey: {
        type: String,
        default: 'name',
    },
    valueKey: {
        type: String,
        default: 'id',
    },
    error: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');
const highlightedIndex = ref(0);
const inputRef = ref(null);
const dropdownRef = ref(null);

// Get selected option label
const selectedLabel = computed(() => {
    if (!props.modelValue) return '';
    const selected = props.options.find(opt => opt[props.valueKey] === props.modelValue);
    return selected ? selected[props.labelKey] : '';
});

// Filter options based on search
const filteredOptions = computed(() => {
    if (!searchQuery.value) return props.options;
    const query = searchQuery.value.toLowerCase();
    return props.options.filter(opt => 
        opt[props.labelKey].toLowerCase().includes(query)
    );
});

// Handle input focus
const handleFocus = () => {
    if (!props.disabled) {
        isOpen.value = true;
        searchQuery.value = '';
        highlightedIndex.value = 0;
    }
};

// Handle input blur
const handleBlur = (e) => {
    // Delay closing to allow click on options
    setTimeout(() => {
        isOpen.value = false;
        searchQuery.value = '';
    }, 150);
};

// Select an option
const selectOption = (option) => {
    emit('update:modelValue', option[props.valueKey]);
    isOpen.value = false;
    searchQuery.value = '';
};

// Clear selection
const clearSelection = () => {
    emit('update:modelValue', '');
    if (inputRef.value) {
        inputRef.value.focus();
    }
};

// Handle keyboard navigation
const handleKeydown = (e) => {
    if (!isOpen.value) return;
    
    switch (e.key) {
        case 'ArrowDown':
            e.preventDefault();
            highlightedIndex.value = Math.min(highlightedIndex.value + 1, filteredOptions.value.length - 1);
            break;
        case 'ArrowUp':
            e.preventDefault();
            highlightedIndex.value = Math.max(highlightedIndex.value - 1, 0);
            break;
        case 'Enter':
            e.preventDefault();
            if (filteredOptions.value[highlightedIndex.value]) {
                selectOption(filteredOptions.value[highlightedIndex.value]);
            }
            break;
        case 'Escape':
            isOpen.value = false;
            break;
    }
};

// Reset highlighted index when filtered options change
watch(filteredOptions, () => {
    highlightedIndex.value = 0;
});

// Close dropdown on outside click
const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="relative" ref="dropdownRef">
        <!-- Display Selected or Search Input -->
        <div 
            class="relative"
            :class="{ 'opacity-50': disabled }"
        >
            <!-- Selected Value Display -->
            <div 
                v-if="modelValue && !isOpen"
                @click="handleFocus"
                class="w-full px-4 py-2 pr-10 border rounded-md cursor-pointer bg-white flex items-center justify-between"
                :class="[
                    error ? 'border-red-500' : 'border-gray-300',
                    disabled ? 'bg-gray-100 cursor-not-allowed' : 'hover:border-gray-400'
                ]"
            >
                <span class="truncate">{{ selectedLabel }}</span>
                <button 
                    v-if="!disabled"
                    @click.stop="clearSelection"
                    type="button"
                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                >
                    ✕
                </button>
            </div>
            
            <!-- Search Input -->
            <input
                v-else
                ref="inputRef"
                type="text"
                v-model="searchQuery"
                :placeholder="loading ? 'Memuat...' : placeholder"
                :disabled="disabled || loading"
                @focus="handleFocus"
                @blur="handleBlur"
                @keydown="handleKeydown"
                class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                :class="[
                    error ? 'border-red-500' : 'border-gray-300',
                    disabled ? 'bg-gray-100 cursor-not-allowed' : ''
                ]"
            />
            
            <!-- Loading Spinner -->
            <div 
                v-if="loading"
                class="absolute right-3 top-1/2 -translate-y-1/2"
            >
                <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <!-- Dropdown Arrow -->
            <div 
                v-if="!loading && !modelValue"
                class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
            >
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>
        
        <!-- Dropdown Options -->
        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div 
                v-if="isOpen && !disabled && !loading"
                class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
            >
                <!-- No Results -->
                <div 
                    v-if="filteredOptions.length === 0"
                    class="px-4 py-3 text-gray-500 text-center"
                >
                    {{ options.length === 0 ? 'Tidak ada data' : 'Tidak ditemukan' }}
                </div>
                
                <!-- Options List -->
                <div
                    v-for="(option, index) in filteredOptions"
                    :key="option[valueKey]"
                    @mousedown="selectOption(option)"
                    @mouseenter="highlightedIndex = index"
                    class="px-4 py-2 cursor-pointer transition-colors"
                    :class="{
                        'bg-blue-500 text-white': highlightedIndex === index,
                        'hover:bg-gray-100': highlightedIndex !== index,
                        'bg-blue-50': option[valueKey] === modelValue && highlightedIndex !== index
                    }"
                >
                    {{ option[labelKey] }}
                </div>
            </div>
        </transition>
        
        <!-- Error Message -->
        <p v-if="error" class="mt-1 text-sm text-red-600">
            {{ error }}
        </p>
    </div>
</template>
