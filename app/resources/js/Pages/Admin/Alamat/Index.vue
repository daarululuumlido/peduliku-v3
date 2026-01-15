<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    alamat: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const sort = ref(props.filters.sort || 'created_at');
const direction = ref(props.filters.direction || 'desc');

const applyFilters = () => {
    router.get(route('admin.alamat.index'), {
        search: search.value,
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
        direction.value = 'asc';
    }
    applyFilters();
};

const getSortIcon = (field) => {
    if (sort.value !== field) return null;
    return direction.value === 'asc' ? '↑' : '↓';
};

const deleteAlamat = (id, nama) => {
    if (confirm(`Apakah Anda yakin ingin menghapus data alamat "${nama}"?`)) {
        router.delete(route('admin.alamat.destroy', id));
    }
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
    <Head title="Manajemen Alamat" />

    <ModuleLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Manajemen Alamat
            </h2>
        </template>

        <!-- Page Header with Actions -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Data Alamat</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola master data alamat dalam sistem.</p>
            </div>
            <div class="flex gap-2">
                <Link
                    :href="route('admin.alamat.create')"
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm transition btn-primary"
                >
                   <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Alamat
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
                            placeholder="Cari alamat atau desa..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                        />
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-900">
                            <tr>
                                <th 
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-800"
                                    @click="handleSort('alamat_lengkap')"
                                >
                                    <div class="flex items-center gap-1">
                                        Alamat Lengkap
                                        <span v-if="sort === 'alamat_lengkap'" class="text-indigo-600 dark:text-indigo-400">{{ getSortIcon('alamat_lengkap') }}</span>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Desa / Kelurahan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Kecamatan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Kota / Kabupaten
                                </th>
                                <th 
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 w-32 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-800"
                                    @click="handleSort('orang_count')"
                                >
                                    <div class="flex items-center justify-center gap-1">
                                        Digunakan Oleh
                                        <span v-if="sort === 'orang_count'" class="text-indigo-600 dark:text-indigo-400">{{ getSortIcon('orang_count') }}</span>
                                    </div>
                                </th>
                                <th 
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-800"
                                    @click="handleSort('created_at')"
                                >
                                    <div class="flex items-center justify-center gap-1">
                                        Dibuat
                                        <span v-if="sort === 'created_at'" class="text-indigo-600 dark:text-indigo-400">{{ getSortIcon('created_at') }}</span>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-slate-800 dark:divide-slate-700">
                            <tr v-for="item in alamat.data" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ item.alamat_lengkap || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ item.desa?.name || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ item.desa?.district?.name || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ item.desa?.district?.city?.name || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ item.orang_count }} Orang
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400">
                                    {{ new Date(item.created_at).toLocaleDateString('id-ID') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link
                                        :href="route('admin.alamat.edit', item.id)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deleteAlamat(item.id, item.alamat_lengkap || item.desa?.name)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="alamat.data.length === 0">
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada data alamat ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex justify-between items-center" v-if="alamat.links.length > 3">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Menampilkan {{ alamat.from }} - {{ alamat.to }} dari {{ alamat.total }} data
                    </div>
                    <div class="flex gap-1">
                        <template v-for="link in alamat.links" :key="link.label">
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
