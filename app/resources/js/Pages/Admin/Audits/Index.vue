<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    audits: Object,
    filters: Object,
    auditableTypes: Array,
    events: Array,
});

const event = ref(props.filters.event || '');
const auditableType = ref(props.filters.auditable_type || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');
const sort = ref(props.filters.sort || 'created_at');
const direction = ref(props.filters.direction || 'desc');

const applyFilters = () => {
    router.get(route('admin.audits.index'), {
        event: event.value || undefined,
        auditable_type: auditableType.value || undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
        sort: sort.value,
        direction: direction.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const handleSort = (field) => {
    if (sort.value === field) {
        direction.value = direction.value === 'asc' ? 'desc' : 'asc';
    } else {
        sort.value = field;
        direction.value = 'desc';
    }
    applyFilters();
};

const getSortIcon = (field) => {
    if (sort.value !== field) return null;
    return direction.value === 'asc' ? '↑' : '↓';
};

const resetFilters = () => {
    event.value = '';
    auditableType.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    sort.value = 'created_at';
    direction.value = 'desc';
    applyFilters();
};

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
        case 'App\\Models\\Orang':
            return 'Orang';
        case 'App\\Models\\KartuKeluarga':
            return 'Kartu Keluarga';
        case 'App\\Models\\KartuKeluargaAnggota':
            return 'Anggota KK';
        case 'App\\Models\\Alamat':
            return 'Alamat';
        case 'App\\Models\\User':
            return 'User';
        default:
            return type;
    }
};

const formatChangedFields = (oldValues, newValues) => {
    if (!oldValues && !newValues) return '-';
    
    const oldKeys = Object.keys(oldValues || {});
    const newKeys = Object.keys(newValues || {});
    const allKeys = [...new Set([...oldKeys, ...newKeys])];
    
    const changed = allKeys.filter(key => {
        const oldVal = oldValues?.[key];
        const newVal = newValues?.[key];
        return JSON.stringify(oldVal) !== JSON.stringify(newVal);
    });
    
    return changed.length > 0 ? `${changed.length} field berubah` : '-';
};
</script>

<template>
    <Head title="Audit Log" />

    <ModuleLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Audit Log
            </h2>
        </template>

        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Log Aktivitas Sistem</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Pantau semua perubahan data dalam sistem.
            </p>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Jenis Event
                        </label>
                        <select
                            v-model="event"
                            @change="applyFilters"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
                        >
                            <option value="">Semua Event</option>
                            <option v-for="evt in events" :key="evt" :value="evt">
                                {{ getEventLabel(evt) }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Model
                        </label>
                        <select
                            v-model="auditableType"
                            @change="applyFilters"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
                        >
                            <option value="">Semua Model</option>
                            <option v-for="type in auditableTypes" :key="type" :value="type">
                                {{ type }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Dari Tanggal
                        </label>
                        <input
                            v-model="dateFrom"
                            type="date"
                            @change="applyFilters"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Sampai Tanggal
                        </label>
                        <input
                            v-model="dateTo"
                            type="date"
                            @change="applyFilters"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white"
                        />
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button
                        @click="resetFilters"
                        class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition dark:bg-slate-700 dark:border-slate-600 dark:text-gray-200 dark:hover:bg-slate-600"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Reset Filter
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                    <thead class="bg-gray-50 dark:bg-slate-900">
                        <tr>
                            <th 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-800"
                                @click="handleSort('created_at')"
                            >
                                <div class="flex items-center gap-1">
                                    Tanggal
                                    <span v-if="sort === 'created_at'" class="text-indigo-600 dark:text-indigo-400">{{ getSortIcon('created_at') }}</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                User
                            </th>
                            <th 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-800"
                                @click="handleSort('event')"
                            >
                                <div class="flex items-center gap-1">
                                    Event
                                    <span v-if="sort === 'event'" class="text-indigo-600 dark:text-indigo-400">{{ getSortIcon('event') }}</span>
                                </div>
                            </th>
                            <th 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-800"
                                @click="handleSort('auditable_type')"
                            >
                                <div class="flex items-center gap-1">
                                    Model
                                    <span v-if="sort === 'auditable_type'" class="text-indigo-600 dark:text-indigo-400">{{ getSortIcon('auditable_type') }}</span>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Perubahan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                IP Address
                            </th>
                            <th class="relative px-6 py-3">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-slate-800 dark:divide-slate-700">
                        <tr v-for="audit in audits.data" :key="audit.id" class="hover:bg-gray-50 dark:hover:bg-slate-700 cursor-pointer" @click="router.visit(route('admin.audits.show', audit.id))">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ new Date(audit.created_at).toLocaleString('id-ID') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ audit.user ? audit.user.name : '-' }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ audit.user ? audit.user.email : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getEventColor(audit.event)">
                                    {{ getEventLabel(audit.event) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ getAuditableLabel(audit.auditable_type) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                                    {{ audit.auditable_id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ formatChangedFields(audit.old_values, audit.new_values) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono">
                                {{ audit.ip_address || '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link
                                    :href="route('admin.audits.show', audit.id)"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    @click.stop
                                >
                                    Detail
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="audits.data.length === 0">
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-gray-500 dark:text-gray-400">Tidak ada data audit</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan {{ audits.from }} sampai {{ audits.to }} dari {{ audits.total }} data
                </div>
                <div v-if="audits.links" class="flex gap-2">
                    <template v-for="(link, index) in audits.links" :key="index">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            class="px-3 py-1 rounded border border-gray-300 text-sm dark:border-slate-600 dark:text-white"
                            :class="{
                                'bg-indigo-600 text-white border-indigo-600': link.active,
                                'hover:bg-gray-50 dark:hover:bg-slate-700': link.url && !link.active,
                            }"
                        />
                        <span
                            v-else
                            v-html="link.label"
                            class="px-3 py-1 rounded border border-gray-300 text-sm opacity-50 cursor-not-allowed dark:border-slate-600 dark:text-white"
                            :class="{
                                'bg-indigo-600 text-white border-indigo-600': link.active,
                            }"
                        />
                    </template>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
