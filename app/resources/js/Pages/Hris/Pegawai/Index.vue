<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    pegawai: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const applyFilters = () => {
    router.get(route('hris.pegawai.index'), {
        search: search.value,
        status: status.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const deletePegawai = (id, nama) => {
    if (confirm(`Apakah Anda yakin ingin menonaktifkan pegawai "${nama}"?`)) {
        router.delete(route('hris.pegawai.destroy', id));
    }
};

const getStatusColor = (status) => {
    const colors = {
        'Guru Tetap': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'Guru Kontrak': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'Karyawan Tetap': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'Karyawan Kontrak': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
        'Honorer': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
    };
    return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
};

const getGenderColor = (gender) => {
    return gender === 'L'
        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
        : 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300';
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
    <Head title="Pegawai" />

    <ModuleLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Manajemen Pegawai
            </h2>
        </template>

        <!-- Page Header with Actions -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Data Pegawai</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola semua data pegawai aktif dalam sistem.</p>
            </div>
            <div class="flex gap-2">
                <Link
                    :href="route('hris.pegawai.search-orang')"
                    class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition dark:bg-slate-700 dark:border-slate-600 dark:text-gray-200 dark:hover:bg-slate-600"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg>
                    Aktivasi Orang
                </Link>
                <Link
                    :href="route('hris.pegawai.create')"
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm transition btn-primary"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Pegawai
                </Link>
            </div>
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
                            placeholder="Cari nama atau NIP..."
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
                            <option value="Guru Tetap">Guru Tetap</option>
                            <option value="Guru Kontrak">Guru Kontrak</option>
                            <option value="Karyawan Tetap">Karyawan Tetap</option>
                            <option value="Karyawan Kontrak">Karyawan Kontrak</option>
                            <option value="Honorer">Honorer</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-900">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 sticky left-0 bg-gray-50 dark:bg-slate-900 z-20">
                                    NIP
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 sticky left-24 bg-gray-50 dark:bg-slate-900 z-10">
                                    Nama
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Gender
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Status
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 hidden sm:table-cell">
                                    Jabatan
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 hidden lg:table-cell">
                                    TMT
                                </th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-slate-800 dark:divide-slate-700">
                            <tr v-for="item in pegawai.data" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                <td class="px-3 py-2 whitespace-nowrap text-xs font-mono text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-slate-800 z-20">
                                    {{ item.nip }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap sticky left-24 bg-white dark:bg-slate-800 z-10">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ item.orang.nama_gelar }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400" v-if="item.orang.no_whatsapp">
                                        📱 {{ item.orang.no_whatsapp }}
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-1.5 py-0.5 text-xs font-medium rounded-full"
                                        :class="getGenderColor(item.orang.gender)"
                                    >
                                        {{ item.orang.gender === 'L' ? 'L' : 'P' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                        :class="getStatusColor(item.status_kepegawaian)"
                                    >
                                        {{ item.status_kepegawaian }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                                    <div v-if="item.current_jabatan" class="truncate max-w-xs" :title="item.current_jabatan.nama_jabatan">
                                        {{ item.current_jabatan.nama_jabatan }}
                                    </div>
                                    <span v-else class="text-gray-400 dark:text-gray-500">-</span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400 hidden lg:table-cell">
                                    {{ new Date(item.tmt).toLocaleDateString('id-ID') }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link
                                            :href="route('hris.pegawai.show', item.id)"
                                            class="p-1.5 text-blue-600 hover:text-blue-900 rounded hover:bg-blue-50 dark:text-blue-400 dark:hover:text-blue-300 dark:hover:bg-slate-700"
                                            title="Lihat Detail"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1-.855.588l-6.11 3.376a1.012 1.012 0 0 1-.433-.816L.3 8.76a1.012 1.012 0 0 0 .433-.816L10.845 6.246a1.012 1.012 0 0 0 .855.588l5.994 3.31a1.012 1.012 0 0 1 .433.816l-6.11 3.376a1.012 1.012 0 0 1-.433-.816L1.6 11.74a1.012 1.012 0 0 1 .433.816l5.994 3.31a1.012 1.012 0 0 1-.855.588l-6.11 3.376a1.012 1.012 0 0 1-.433-.816L2.036 12.322Z" />
                                            </svg>
                                        </Link>
                                        <Link
                                            :href="route('hris.pegawai.edit', item.id)"
                                            class="p-1.5 text-indigo-600 hover:text-indigo-900 rounded hover:bg-indigo-50 dark:text-indigo-400 dark:hover:text-indigo-300 dark:hover:bg-slate-700"
                                            title="Edit"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1-2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-2.4 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 1-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </Link>
                                        <button
                                            @click="deletePegawai(item.id, item.orang.nama_gelar)"
                                            class="p-1.5 text-red-600 hover:text-red-900 rounded hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-slate-700"
                                            title="Nonaktifkan"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 1-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="pegawai.data.length === 0">
                                <td colspan="7" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400">Tidak ada data pegawai ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 flex justify-between items-center" v-if="pegawai.links.length > 3">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Menampilkan {{ pegawai.from }} - {{ pegawai.to }} dari {{ pegawai.total }} data
                    </div>
                    <div class="flex gap-1">
                        <template v-for="link in pegawai.links" :key="link.label">
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
