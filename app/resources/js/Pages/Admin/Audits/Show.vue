<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    audit: Object,
    oldValues: Object,
    newValues: Object,
});

const getEventColor = (eventType) => {
    switch (eventType) {
        case 'created':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'updated':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        case 'deleted':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        case 'restored':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }
};

const getEventLabel = (eventType) => {
    switch (eventType) {
        case 'created':
            return 'Dibuat';
        case 'updated':
            return 'Diubah';
        case 'deleted':
            return 'Dihapus';
        case 'restored':
            return 'Dikembalikan';
        default:
            return eventType;
    }
};

const getAuditableLabel = (type) => {
    switch (type) {
        case 'App\Models\Orang':
            return 'Orang';
        case 'App\Models\KartuKeluarga':
            return 'Kartu Keluarga';
        case 'App\Models\KartuKeluargaAnggota':
            return 'Anggota KK';
        case 'App\Models\Alamat':
            return 'Alamat';
        case 'App\Models\User':
            return 'User';
        default:
            return type;
    }
};

const getAllKeys = () => {
    const oldKeys = Object.keys(props.oldValues || {});
    const newKeys = Object.keys(props.newValues || {});
    return [...new Set([...oldKeys, ...newKeys])];
};

const getValueDisplay = (key, oldOrNew) => {
    const oldVal = props.oldValues?.[key];
    const newVal = props.newValues?.[key];
    
    if (oldOrNew === 'old') {
        return oldVal === null || oldVal === undefined ? '<em>null</em>' : formatValue(oldVal);
    } else {
        return newVal === null || newVal === undefined ? '<em>null</em>' : formatValue(newVal);
    }
};

const formatValue = (value) => {
    if (typeof value === 'object' && value !== null) {
        return JSON.stringify(value, null, 2);
    }
    return value;
};

const isChanged = (key) => {
    const oldVal = props.oldValues?.[key];
    const newVal = props.newValues?.[key];
    return JSON.stringify(oldVal) !== JSON.stringify(newVal);
};

const isNew = (key) => {
    return props.oldValues?.[key] === undefined && props.newValues?.[key] !== undefined;
};

const isRemoved = (key) => {
    return props.oldValues?.[key] !== undefined && props.newValues?.[key] === undefined;
};
</script>

<template>
    <Head :title="`Audit Log #${audit.id}`" />

    <ModuleLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Audit Log #{{ audit.id }}
            </h2>
        </template>

        <div class="mb-6">
            <Link
                :href="route('admin.audits.index')"
                class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
            >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Daftar
            </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white shadow-sm rounded-xl dark:bg-slate-800">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail Perubahan</h3>
                    </div>
                    <div class="p-6">
                        <div v-if="getAllKeys().length === 0" class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">Tidak ada data perubahan</p>
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="key in getAllKeys()" :key="key" class="border-l-4 pl-4" :class="{
                                'border-green-500': isNew(key),
                                'border-red-500': isRemoved(key),
                                'border-blue-500': isChanged(key) && !isNew(key) && !isRemoved(key),
                            }">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        {{ key }}
                                    </span>
                                    <span v-if="isNew(key)" class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-300">
                                        Baru
                                    </span>
                                    <span v-else-if="isRemoved(key)" class="text-xs px-2 py-1 bg-red-100 text-red-800 rounded-full dark:bg-red-900 dark:text-red-300">
                                        Dihapus
                                    </span>
                                    <span v-else-if="isChanged(key)" class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                        Diubah
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div v-if="!isNew(key)" :class="{ 'opacity-50': isRemoved(key) }">
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Lama</div>
                                        <div class="text-sm bg-red-50 p-2 rounded text-gray-900 dark:bg-red-900/30 dark:text-gray-200 whitespace-pre-wrap break-all font-mono" v-html="getValueDisplay(key, 'old')"></div>
                                    </div>
                                    <div v-if="!isRemoved(key)" :class="{ 'opacity-50': isNew(key) }">
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Baru</div>
                                        <div class="text-sm bg-green-50 p-2 rounded text-gray-900 dark:bg-green-900/30 dark:text-gray-200 whitespace-pre-wrap break-all font-mono" v-html="getValueDisplay(key, 'new')"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white shadow-sm rounded-xl dark:bg-slate-800">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                Event
                            </label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full mt-1" :class="getEventColor(audit.event)">
                                {{ getEventLabel(audit.event) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                Waktu
                            </label>
                            <div class="text-sm text-gray-900 dark:text-white mt-1">
                                {{ new Date(audit.created_at).toLocaleString('id-ID', { 
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit'
                                }) }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                Model
                            </label>
                            <div class="text-sm text-gray-900 dark:text-white mt-1">
                                {{ getAuditableLabel(audit.auditable_type) }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-1">
                                ID: {{ audit.auditable_id }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                User
                            </label>
                            <div v-if="audit.user" class="mt-1">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ audit.user.name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ audit.user.email }}
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                -
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                IP Address
                            </label>
                            <div class="text-sm text-gray-900 dark:text-white font-mono mt-1">
                                {{ audit.ip_address || '-' }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                URL
                            </label>
                            <div class="text-sm text-gray-900 dark:text-white break-all mt-1">
                                {{ audit.url || '-' }}
                            </div>
                        </div>

                        <div v-if="audit.user_agent">
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                User Agent
                            </label>
                            <div class="text-xs text-gray-500 dark:text-gray-400 break-all mt-1">
                                {{ audit.user_agent }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
