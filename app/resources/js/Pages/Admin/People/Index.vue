<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
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
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manajemen Orang
                </h2>
                <div class="flex gap-2">
                    <Link
                        :href="route('admin.orang.trashed')"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition"
                    >
                        Sampah
                    </Link>
                    <Link
                        :href="route('admin.orang.create')"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                    >
                        + Tambah Orang
                    </Link>
                </div>
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

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Filters -->
                        <div class="flex flex-wrap gap-4 mb-6">
                            <div class="flex-1 min-w-64">
                                <input
                                    type="text"
                                    v-model="search"
                                    placeholder="Cari nama atau NIK..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                            <div>
                                <select
                                    v-model="gender"
                                    @change="applyFilters"
                                    class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Semua Gender</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            NIK
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Gender
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tempat, Tgl Lahir
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Alamat
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="item in orang.data" :key="item.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                            {{ item.nik }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ item.nama }}
                                            </div>
                                            <div class="text-sm text-gray-500" v-if="item.no_whatsapp">
                                                📱 {{ item.no_whatsapp }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ item.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ item.tempat_lahir }}, {{ new Date(item.tanggal_lahir).toLocaleDateString('id-ID') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                            <template v-if="item.alamat_ktp">
                                                {{ item.alamat_ktp.alamat_lengkap }}
                                                <span v-if="item.alamat_ktp?.desa">
                                                    , {{ item.alamat_ktp.desa.name }}
                                                </span>
                                            </template>
                                            <span v-else class="text-gray-400">-</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                :href="route('admin.orang.show', item.id)"
                                                class="text-blue-600 hover:text-blue-900 mr-3"
                                            >
                                                Lihat
                                            </Link>
                                            <Link
                                                :href="route('admin.orang.edit', item.id)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteOrang(item.id, item.nama)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="orang.data.length === 0">
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            Tidak ada data orang ditemukan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 flex justify-between items-center" v-if="orang.links.length > 3">
                            <div class="text-sm text-gray-700">
                                Menampilkan {{ orang.from }} - {{ orang.to }} dari {{ orang.total }} data
                            </div>
                            <div class="flex gap-1">
                                <template v-for="link in orang.links" :key="link.label">
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
                                    <span
                                        v-else
                                        class="px-3 py-1 border border-gray-200 rounded text-sm text-gray-400"
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
