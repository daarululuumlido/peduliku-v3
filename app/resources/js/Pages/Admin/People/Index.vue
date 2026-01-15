<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    orang: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const gender = ref(props.filters.gender || '');

const applyFilters = () => {
    router.get(route('admin.orang.index'), {
        search: search.value,
        gender: gender.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const deleteOrang = (id, nama) => {
    if (confirm(`Apakah Anda yakin ingin menghapus data "${nama}"?`)) {
        router.delete(route('admin.orang.destroy', id));
    }
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
    <Head title="Manajemen Orang" />

    <ModuleLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Manajemen Orang
            </h2>
        </template>

        <!-- Page Header with Actions -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Data Orang</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola semua data penduduk dalam sistem.</p>
            </div>
            <div class="flex gap-2">
                <Link
                    :href="route('admin.orang.trashed')"
                    class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition dark:bg-slate-700 dark:border-slate-600 dark:text-gray-200 dark:hover:bg-slate-600"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 1-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    Sampah
                </Link>
                <Link
                    :href="route('admin.orang.create')"
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm transition btn-primary"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Orang
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
                            placeholder="Cari nama atau NIK..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                        />
                    </div>
                    <div>
                        <select
                            v-model="gender"
                            @change="applyFilters"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                        >
                            <option value="">Semua Gender</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Compact Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-900">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 sticky left-0 bg-gray-50 dark:bg-slate-900 z-20">
                                    NIK
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 sticky left-20 bg-gray-50 dark:bg-slate-900 z-10">
                                    Nama
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Gender
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Tgl Lahir
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Kota/Kab
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 hidden sm:table-cell">
                                    Alamat
                                </th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-slate-800 dark:divide-slate-700">
                            <tr v-for="item in orang.data" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                <td class="px-3 py-2 whitespace-nowrap text-xs font-mono text-gray-900 dark:text-white sticky left-0 bg-white dark:bg-slate-800 z-20">
                                    {{ item.nik }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ item.nama }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400" v-if="item.no_whatsapp">
                                        📱 {{ item.no_whatsapp }}
                                    </div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-1.5 py-0.5 text-xs font-medium rounded-full"
                                        :class="getGenderColor(item.gender)"
                                    >
                                        {{ item.gender === 'L' ? 'L' : 'P' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                    {{ new Date(item.tanggal_lahir).toLocaleDateString('id-ID') }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                    <span
                                        v-if="item.alamat_ktp?.desa"
                                        class="text-gray-900 dark:text-white"
                                        :title="item.alamat_ktp.full_address"
                                    >
                                        {{ item.alamat_ktp.desa.district.city.name || '-' }}
                                    </span>
                                    <span v-else class="text-gray-400 dark:text-gray-500">-</span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                                    <span
                                        v-if="item.alamat_ktp"
                                        class="text-gray-900 dark:text-white truncate block"
                                        :title="item.alamat_ktp.full_address"
                                    >
                                        {{ item.alamat_ktp.desa?.name || '-' }}
                                    </span>
                                    <span v-else class="text-gray-400 dark:text-gray-500">-</span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link
                                            :href="route('admin.orang.show', item.id)"
                                            class="p-1.5 text-blue-600 hover:text-blue-900 rounded hover:bg-blue-50 dark:text-blue-400 dark:hover:text-blue-300 dark:hover:bg-slate-700"
                                            title="Lihat Detail"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1-.855.588l-6.11 3.376a1.012 1.012 0 0 1-.433-.816L.3 8.76a1.012 1.012 0 0 0 .433-.816L10.845 6.246a1.012 1.012 0 0 0 .855.588l5.994 3.31a1.012 1.012 0 0 1 .433.816l-6.11 3.376a1.012 1.012 0 0 1-.433-.816L1.6 11.74a1.012 1.012 0 0 1 .433.816l5.994 3.31a1.012 1.012 0 0 1-.855.588l-6.11 3.376a1.012 1.012 0 0 1-.433-.816L2.036 12.322Z" />
                                            </svg>
                                        </Link>
                                        <Link
                                            :href="route('admin.orang.edit', item.id)"
                                            class="p-1.5 text-indigo-600 hover:text-indigo-900 rounded hover:bg-indigo-50 dark:text-indigo-400 dark:hover:text-indigo-300 dark:hover:bg-slate-700"
                                            title="Edit"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1-2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-2.4 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 1-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </Link>
                                        <button
                                            @click="deleteOrang(item.id, item.nama)"
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
                            <tr v-if="orang.data.length === 0">
                                <td colspan="7" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400">Tidak ada data orang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 flex justify-between items-center" v-if="orang.links.length > 3">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Menampilkan {{ orang.from }} - {{ orang.to }} dari {{ orang.total }} data
                    </div>
                    <div class="flex gap-1">
                        <template v-for="link in orang.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="px-3 py-1.5 border border-gray-300 text-sm rounded-lg transition-colors"
                                :class="{
                                    'bg-indigo-600 text-white border-indigo-600': link.active,
                                    'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-slate-700 dark:text-gray-200 dark:border-slate-600 dark:hover:bg-slate-600': !link.active
                                }"
                                v-html="link.label"
                            />
                            <span
                                v-else
                                v-html="link.label"
                                class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm opacity-50 cursor-not-allowed dark:border-slate-600"
                                :class="{
                                    'bg-indigo-600 text-white border-indigo-600': link.active
                                }"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>

        <!-- Page Header with Actions -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Data Orang</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola semua data penduduk dalam sistem.</p>
            </div>
            <div class="flex gap-2">
                <Link
                    :href="route('admin.orang.trashed')"
                    class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition dark:bg-slate-700 dark:border-slate-600 dark:text-gray-200 dark:hover:bg-slate-600"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    Sampah
                </Link>
                <Link
                    :href="route('admin.orang.create')"
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm transition btn-primary"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Orang
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
                            placeholder="Cari nama atau NIK..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                        />
                    </div>
                    <div>
                        <select
                            v-model="gender"
                            @change="applyFilters"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                        >
                            <option value="">Semua Gender</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    NIK
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Nama
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Gender
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Tempat, Tgl Lahir
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Alamat
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-slate-800 dark:divide-slate-700">
                            <tr v-for="item in orang.data" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                    {{ item.nik }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ item.nama }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400" v-if="item.no_whatsapp">
                                        📱 {{ item.no_whatsapp }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ item.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ item.tempat_lahir }}, {{ new Date(item.tanggal_lahir).toLocaleDateString('id-ID') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        v-if="item.alamat_ktp"
                                        class="text-sm text-gray-900 dark:text-white truncate block max-w-xs"
                                        :title="item.alamat_ktp.full_address"
                                    >
                                        {{ item.alamat_ktp.desa?.name || '-' }}
                                    </span>
                                    <span v-else class="text-gray-400 dark:text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link
                                        :href="route('admin.orang.show', item.id)"
                                        class="text-blue-600 hover:text-blue-900 mr-3 dark:text-blue-400 dark:hover:text-blue-300"
                                    >
                                        Lihat
                                    </Link>
                                    <Link
                                        :href="route('admin.orang.edit', item.id)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deleteOrang(item.id, item.nama)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="orang.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada data orang ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex justify-between items-center" v-if="orang.links.length > 3">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Menampilkan {{ orang.from }} - {{ orang.to }} dari {{ orang.total }} data
                    </div>
                    <div class="flex gap-1">
                        <template v-for="link in orang.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="px-3 py-1 border rounded-lg text-sm transition-colors"
                                :class="{
                                    'bg-indigo-600 text-white border-indigo-600': link.active,
                                    'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-slate-700 dark:text-gray-200 dark:border-slate-600 dark:hover:bg-slate-600': !link.active
                                }"
                                v-html="link.label"
                            />
                            <span
                                v-else
                                class="px-3 py-1 border border-gray-200 rounded-lg text-sm text-gray-400 dark:border-slate-600"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
