<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    strukturs: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const applyFilters = () => {
    router.get(route('hris.struktur-organisasi.index'), {
        search: search.value,
        status: status.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const deleteStruktur = (id, nama) => {
    if (confirm(`Apakah Anda yakin ingin menghapus periode "${nama}"?`)) {
        router.delete(route('hris.struktur-organisasi.destroy', id));
    }
};

const cloneStruktur = (id) => {
    if (confirm('Apakah Anda yakin ingin mengkloning struktur organisasi ini?')) {
        router.post(route('hris.struktur-organisasi.clone', id));
    }
};

const getStatusBadge = (isActive) => {
    return isActive
        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
        : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
};

const getStatusLabel = (isActive) => {
    return isActive ? 'Aktif' : 'Tidak Aktif';
};

// Debounce search
let searchTimeout = null;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300);
});
</script>

<template>
    <Head title="Struktur Organisasi" />

    <ModuleLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Struktur Organisasi
            </h2>
        </template>

        <!-- Page Header with Actions -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Periode Struktur Organisasi</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola periode dan struktur organisasi pesantren.</p>
            </div>
            <Link
                :href="route('hris.struktur-organisasi.create')"
                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm transition btn-primary"
            >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Periode
            </Link>
        </div>

        <!-- Card Container -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
            <div class="p-6">
                <!-- Filters -->
                <div class="flex flex-wrap gap-4 mb-6">
                    <div class="flex-1 min-w-64">
                        <input
                            type="text"
                            v-model="search"
                            placeholder="Cari periode..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                        />
                    </div>
                    <div>
                        <select
                            v-model="status"
                            @change="applyFilters"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                        >
                            <option value="">Semua Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-900">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Periode
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Tanggal
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Unit
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Status
                                </th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-slate-800 dark:divide-slate-700">
                            <tr v-for="item in strukturs.data" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                <td class="px-3 py-2">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ item.nama_periode }}
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                    <div>{{ new Date(item.tgl_mulai).toLocaleDateString('id-ID') }}</div>
                                    <div class="text-gray-400">s/d</div>
                                    <div>{{ new Date(item.tgl_selesai).toLocaleDateString('id-ID') }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                    {{ item.units_count || 0 }} unit
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                        :class="getStatusBadge(item.is_active)"
                                    >
                                        {{ getStatusLabel(item.is_active) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link
                                            :href="route('hris.struktur-organisasi.show', item.id)"
                                            class="p-1.5 text-blue-600 hover:text-blue-900 rounded hover:bg-blue-50 dark:text-blue-400 dark:hover:text-blue-300 dark:hover:bg-slate-700"
                                            title="Lihat Detail"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1-.855.588l-6.11 3.376a1.012 1.012 0 0 1-.433-.816L.3 8.76a1.012 1.012 0 0 0 .433-.816L10.845 6.246a1.012 1.012 0 0 0 .855.588l5.994 3.31a1.012 1.012 0 0 1 .433.816l-6.11 3.376a1.012 1.012 0 0 1-.433-.816L1.6 11.74a1.012 1.012 0 0 1 .433.816l5.994 3.31a1.012 1.012 0 0 1-.855.588l-6.11 3.376a1.012 1.012 0 0 1-.433-.816L2.036 12.322Z" />
                                            </svg>
                                        </Link>
                                        <Link
                                            :href="route('hris.struktur-organisasi.edit', item.id)"
                                            class="p-1.5 text-indigo-600 hover:text-indigo-900 rounded hover:bg-indigo-50 dark:text-indigo-400 dark:hover:text-indigo-300 dark:hover:bg-slate-700"
                                            title="Edit"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1-2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-2.4 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 1-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </Link>
                                        <button
                                            @click="cloneStruktur(item.id)"
                                            class="p-1.5 text-green-600 hover:text-green-900 rounded hover:bg-green-50 dark:text-green-400 dark:hover:text-green-300 dark:hover:bg-slate-700"
                                            title="Kloning"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteStruktur(item.id, item.nama_periode)"
                                            class="p-1.5 text-red-600 hover:text-red-900 rounded hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-slate-700"
                                            title="Hapus"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 1-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="strukturs.data.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400">Tidak ada struktur organisasi ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 flex justify-between items-center" v-if="strukturs.links.length > 3">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Menampilkan {{ strukturs.from }} - {{ strukturs.to }} dari {{ strukturs.total }} data
                    </div>
                    <div class="flex gap-1">
                        <template v-for="link in strukturs.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="px-3 py-1.5 border text-sm rounded-lg transition-colors"
                                :class="{
                                    'bg-indigo-600 text-white border-indigo-600': link.active,
                                    'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-slate-700 dark:text-gray-200 dark:border-slate-600 dark:hover:bg-slate-600': !link.active
                                }"
                                v-html="link.label"
                            />
                            <span
                                v-else
                                v-html="link.label"
                                class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm text-gray-400 cursor-not-allowed bg-white dark:bg-slate-700 dark:border-slate-600 dark:text-gray-500"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
