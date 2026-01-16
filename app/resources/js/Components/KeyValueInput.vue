<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({}),
    },
    label: {
        type: String,
        default: 'Custom Attributes',
    },
    error: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

// Convert object to array of {key, value} pairs
const pairs = ref(
    Object.entries(props.modelValue || {}).map(([key, value]) => ({
        key,
        value,
    }))
);

// If no pairs, add one empty pair by default
if (pairs.value.length === 0) {
    pairs.value.push({ key: '', value: '' });
}

// Watch for changes in pairs and emit the updated object
watch(
    pairs,
    (newPairs) => {
        const obj = {};
        newPairs.forEach((pair) => {
            if (pair.key.trim()) {
                obj[pair.key.trim()] = pair.value;
            }
        });
        emit('update:modelValue', obj);
    },
    { deep: true }
);

const addPair = () => {
    pairs.value.push({ key: '', value: '' });
};

const removePair = (index) => {
    if (pairs.value.length > 1) {
        pairs.value.splice(index, 1);
    }
};

const updateKey = (index, value) => {
    pairs.value[index].key = value;
};

const updateValue = (index, value) => {
    pairs.value[index].value = value;
};
</script>

<template>
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <label class="block text-sm font-medium text-gray-700">
                {{ label }}
            </label>
            <button
                type="button"
                @click="addPair"
                class="text-sm text-blue-600 hover:text-blue-800 font-medium"
            >
                + Tambah
            </button>
        </div>

        <div
            v-for="(pair, index) in pairs"
            :key="index"
            class="flex gap-2 items-start"
        >
            <div class="flex-1">
                <input
                    type="text"
                    :value="pair.key"
                    @input="updateKey(index, $event.target.value)"
                    placeholder="Key"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                    :class="{ 'border-red-500': error }"
                />
            </div>
            <div class="flex-1">
                <input
                    type="text"
                    :value="pair.value"
                    @input="updateValue(index, $event.target.value)"
                    placeholder="Value"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                    :class="{ 'border-red-500': error }"
                />
            </div>
            <button
                type="button"
                @click="removePair(index)"
                :disabled="pairs.length === 1"
                class="px-3 py-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                title="Hapus"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <p v-if="error" class="mt-1 text-sm text-red-600">
            {{ error }}
        </p>
        <p class="text-xs text-gray-500">
            Tambahkan atribut kustom sebagai pasangan key-value. Contoh: santri_id, kelas, dll.
        </p>
    </div>
</template>
