<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    kartuKeluarga: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

const deleteKK = (id, noKK) => {
    if (confirm(`Apakah Anda yakin ingin menghapus KK "${noKK}"?`)) {
        router.delete(route('admin.kartu-keluarga.destroy', id));
    }
};

// Debounce search
let searchTimeout = null;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.kartu-keluarga.index'), { search: value }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});
</script>

<template>
    <Head title="Kartu Keluarga" />

    <ModuleLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Kartu Keluarga
            </h2>
        </template>

        <!-- Page Header with Actions -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Data Kartu Keluarga</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola semua data kartu keluarga dalam sistem.</p>
            </div>
            <Link
                :href="route('admin.kartu-keluarga.create')"
                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm transition btn-primary"
            >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah KK
            </Link>
        </div>

        <!-- Card Container -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
            <div class="p-6">
                <!-- Search -->
                <div class="mb-6">
                    <input
                        type="text"
                        v-model="search"
                        placeholder="Cari No. KK..."
                        class="w-full md:w-96 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                    />
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    No. KK
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Alamat
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Jumlah Anggota
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-slate-800 dark:divide-slate-700">
                            <tr v-for="kk in kartuKeluarga.data" :key="kk.id" class="hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                    {{ kk.no_kk }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate dark:text-gray-400">
                                    <template v-if="kk.alamat">
                                        {{ kk.alamat.alamat_lengkap }}
                                        <span v-if="kk.alamat?.desa">
                                            , {{ kk.alamat.desa.name }}
                                        </span>
                                    </template>
                                    <span v-else class="text-gray-400 dark:text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                   <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ kk.anggota?.length || 0 }} Orang
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link
                                        :href="route('admin.kartu-keluarga.show', kk.id)"
                                        class="text-blue-600 hover:text-blue-900 mr-3 dark:text-blue-400 dark:hover:text-blue-300"
                                    >
                                        Lihat
                                    </Link>
                                    <Link
                                        :href="route('admin.kartu-keluarga.edit', kk.id)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deleteKK(kk.id, kk.no_kk)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="kartuKeluarga.data.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada data kartu keluarga.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex justify-between items-center" v-if="kartuKeluarga.links.length > 3">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Menampilkan {{ kartuKeluarga.from }} - {{ kartuKeluarga.to }} dari {{ kartuKeluarga.total }} data
                    </div>
                    <div class="flex gap-1">
                        <template v-for="link in kartuKeluarga.links" :key="link.label">
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
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
