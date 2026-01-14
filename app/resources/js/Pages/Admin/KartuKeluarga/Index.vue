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
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Kartu Keluarga
                </h2>
                <Link
                    :href="route('admin.kartu-keluarga.create')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                >
                    + Tambah KK
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Flash Message -->
                <div
                    v-if="$page.props.flash?.message"
                    class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded"
                >
                    {{ $page.props.flash.message }}
                </div>
                <div
                    v-if="$page.props.flash?.error"
                    class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded"
                >
                    {{ $page.props.flash.error }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Search -->
                        <div class="mb-6">
                            <input
                                type="text"
                                v-model="search"
                                placeholder="Cari No. KK..."
                                class="w-full md:w-96 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 font-mono"
                            />
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No. KK
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Alamat
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jumlah Anggota
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="kk in kartuKeluarga.data" :key="kk.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                            {{ kk.no_kk }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                            <template v-if="kk.alamat">
                                                {{ kk.alamat.alamat_lengkap }}
                                                <span v-if="kk.alamat?.desa">
                                                    , {{ kk.alamat.desa.name }}
                                                </span>
                                            </template>
                                            <span v-else class="text-gray-400">-</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ kk.anggota?.length || 0 }} orang
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                :href="route('admin.kartu-keluarga.show', kk.id)"
                                                class="text-blue-600 hover:text-blue-900 mr-3"
                                            >
                                                Lihat
                                            </Link>
                                            <Link
                                                :href="route('admin.kartu-keluarga.edit', kk.id)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteKK(kk.id, kk.no_kk)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="kartuKeluarga.data.length === 0">
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            Tidak ada data kartu keluarga.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 flex justify-between items-center" v-if="kartuKeluarga.links.length > 3">
                            <div class="text-sm text-gray-700">
                                Menampilkan {{ kartuKeluarga.from }} - {{ kartuKeluarga.to }} dari {{ kartuKeluarga.total }} data
                            </div>
                            <div class="flex gap-1">
                                <template v-for="link in kartuKeluarga.links" :key="link.label">
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        class="px-3 py-1 border rounded text-sm"
                                        :class="{
                                            'bg-blue-600 text-white border-blue-600': link.active,
                                            'bg-white text-gray-700 border-gray-300 hover:bg-gray-50': !link.active
                                        }"
                                        v-html="link.label"
                                    />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
