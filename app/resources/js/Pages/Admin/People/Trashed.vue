<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    orang: Object,
});

const restoreOrang = (id, nama) => {
    if (confirm(`Pulihkan data "${nama}"?`)) {
        router.post(route('admin.orang.restore', id));
    }
};

const forceDeleteOrang = (id, nama) => {
    if (confirm(`HAPUS PERMANEN data "${nama}"? Data tidak dapat dikembalikan!`)) {
        router.delete(route('admin.orang.force-delete', id));
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID');
};
</script>

<template>
    <Head title="Data Terhapus" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.orang.index')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    ← Kembali
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    🗑️ Data Orang Terhapus
                </h2>
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
                                            Dihapus pada
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="item in orang.data" :key="item.id" class="bg-red-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                            {{ item.nik }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ item.nama }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(item.deleted_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button
                                                @click="restoreOrang(item.id, item.nama)"
                                                class="text-green-600 hover:text-green-900 mr-4"
                                            >
                                                ♻️ Pulihkan
                                            </button>
                                            <button
                                                @click="forceDeleteOrang(item.id, item.nama)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                ❌ Hapus Permanen
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="orang.data.length === 0">
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            Tidak ada data terhapus.
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
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
