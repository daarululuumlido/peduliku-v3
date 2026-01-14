<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
const props = defineProps({
    modelValue: {
        type: String, // kartu_keluarga_id
        default: null,
    },
    initialLabel: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const searchQuery = ref(props.initialLabel);
const results = ref([]);
const loading = ref(false);
const showResults = ref(false);

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const performSearch = async (query) => {
    if (!query || query.length < 3) {
        results.value = [];
        return;
    }

    loading.value = true;
    try {
        const response = await axios.get(route('admin.kartu-keluarga.search'), {
            params: { query }
        });
        results.value = response.data;
        showResults.value = true;
    } catch (e) {
        console.error("KK search error", e);
    } finally {
        loading.value = false;
    }
};

const search = debounce(performSearch, 300);

watch(searchQuery, (newVal) => {
    // If user deleted text, clear selection
    if (!newVal) {
        emit('update:modelValue', null);
        emit('change', null);
    }
    search(newVal);
});

const selectItem = (item) => {
    searchQuery.value = item.text;
    emit('update:modelValue', item.id);
    emit('change', item);
    showResults.value = false;
    results.value = [];
};

const onBlur = () => {
    setTimeout(() => {
        showResults.value = false;
    }, 200);
};

</script>

<template>
    <div class="relative">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Cari Kartu Keluarga (No. KK / Kepala Keluarga)
        </label>
        <input
            type="text"
            v-model="searchQuery"
            @focus="showResults = true"
            @blur="onBlur"
            :disabled="disabled"
            placeholder="Ketik minimal 3 karakter..."
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 dark:bg-slate-800 dark:border-slate-700 dark:text-gray-300 font-mono"
            :class="{ 'border-red-500': error }"
        />
        <div v-if="error" class="text-red-500 text-sm mt-1">{{ error }}</div>

        <!-- Dropdown -->
        <div 
            v-if="showResults && results.length > 0"
            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-y-auto dark:bg-slate-800 dark:border-slate-700"
        >
            <ul class="py-1">
                <li
                    v-for="item in results"
                    :key="item.id"
                    @click="selectItem(item)"
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer dark:text-gray-300 dark:hover:bg-slate-700"
                >
                    <div class="font-medium text-sm font-mono">{{ item.text }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ item.preview }}</div>
                </li>
            </ul>
        </div>
        <div 
            v-if="showResults && searchQuery.length >= 3 && results.length === 0 && !loading"
            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg p-4 text-center text-gray-500 dark:bg-slate-800 dark:border-slate-700 dark:text-gray-400"
        >
            Tidak ada data ditemukan.
        </div>
         <div 
            v-if="showResults && loading"
            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg p-4 text-center text-gray-500 dark:bg-slate-800 dark:border-slate-700 dark:text-gray-400"
        >
            Mencari...
        </div>
    </div>
</template>
