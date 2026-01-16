<script setup>
import { ref, computed } from 'vue';
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import JabatanModal from './Partials/JabatanModal.vue';

const props = defineProps({
    unit: Object,
});

const isJabatanModalOpen = ref(false);
const selectedJabatanId = ref(null);

const selectedJabatan = computed(() => {
    if (!selectedJabatanId.value || !props.unit.master_jabatan) return null;
    return props.unit.master_jabatan.find(j => j.id === selectedJabatanId.value);
});

const openEditJabatan = (jabatan) => {
    selectedJabatanId.value = jabatan.id;
    isJabatanModalOpen.value = true;
};

const closeJabatanModal = () => {
    isJabatanModalOpen.value = false;
    selectedJabatanId.value = null;
};
</script>

<template>
    <Head title="Detail Unit Organisasi" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                    Unit Organisasi
                </h2>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('hris.struktur-organisasi.show', unit.struktur_id)"
                        class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                    >
                        ← Kembali ke Struktur
                    </Link>
                </div>
            </div>
        </template>

        <!-- Page Header -->
        <div class="mb-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ unit.nama_unit }}
                            </h3>
                            <p v-if="unit.kode_unit" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Kode: {{ unit.kode_unit }}
                            </p>
                            <div class="mt-3 flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                <span>Level: {{ unit.level_hierarki }}</span>
                                <span v-if="unit.parent">Parent: {{ unit.parent.nama_unit }}</span>
                                <span v-if="unit.struktur">Periode: {{ unit.struktur.nama_periode }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <Link
                                :href="route('hris.unit-organisasi.edit', unit.id)"
                                class="px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold rounded-md transition-colors"
                            >
                                Edit
                            </Link>
                            <Link
                                :href="route('hris.unit-organisasi.destroy', unit.id)"
                                method="delete"
                                as="button"
                                class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-md transition-colors"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus unit ini?')"
                            >
                                Hapus
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-sm dark:bg-slate-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3 dark:bg-green-900">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Jabatan</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ unit.master_jabatan?.length || 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm dark:bg-slate-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3 dark:bg-indigo-900">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75ZM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25ZM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25Z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Sub-Unit</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ unit.children?.length || 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jabatan List -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800 mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Jabatan</h4>
                    <Link
                        :href="route('hris.unit-organisasi.master-jabatan.create', unit.id)"
                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors"
                    >
                        + Tambah Jabatan
                    </Link>
                </div>
                
                <div v-if="unit.master_jabatan && unit.master_jabatan.length > 0" class="space-y-3">
                    <div
                        v-for="jabatan in unit.master_jabatan"
                        :key="jabatan.id"
                        class="p-4 border border-gray-200 rounded-lg dark:border-slate-700"
                    >
                        <div class="flex items-start justify-between">
                            <div>
                                <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ jabatan.nama_jabatan }}
                                </h5>
                                <div class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                    <span v-if="jabatan.is_pimpinan" class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                        Pimpinan
                                    </span>
                                    <span v-if="jabatan.kuota_sdm">Kuota: {{ jabatan.kuota_sdm }}</span>
                                    <span v-if="jabatan.histori_jabatan?.length > 0" class="text-indigo-600 dark:text-indigo-400">
                                        Terisi: {{ jabatan.histori_jabatan.length }}
                                    </span>
                                    <span v-if="jabatan.keterangan">{{ jabatan.keterangan }}</span>
                                </div>
                                <div class="mt-2" v-if="jabatan.histori_jabatan && jabatan.histori_jabatan.length > 0">
                                    <div class="flex -space-x-2 overflow-hidden">
                                        <div 
                                            v-for="history in jabatan.histori_jabatan.slice(0, 5)" 
                                            :key="history.id"
                                            class="inline-block h-6 w-6 rounded-full bg-gray-200 ring-2 ring-white dark:ring-slate-800 flex items-center justify-center text-xs font-bold text-gray-600"
                                            :title="history.peran_pegawai?.orang?.nama"
                                        >
                                            {{ history.peran_pegawai?.orang?.nama?.charAt(0) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button
                                    @click="openEditJabatan(jabatan)"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-xs"
                                >
                                    Detail & Edit
                                </button>
                                <!-- <Link
                                    :href="route('hris.unit-organisasi.master-jabatan.destroy', [unit.id, jabatan.id])"
                                    method="delete"
                                    as="button"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-xs"
                                >
                                    Hapus
                                </Link> -->
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Belum ada jabatan.</p>
                </div>
            </div>
        </div>

        <!-- Children Units -->
        <div v-if="unit.children && unit.children.length > 0" class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
            <div class="p-6">
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Sub-Unit</h4>
                
                <div class="space-y-2">
                    <div
                        v-for="child in unit.children"
                        :key="child.id"
                        class="p-4 border border-gray-200 rounded-lg dark:border-slate-700 hover:shadow-sm transition-shadow"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ child.nama_unit }}
                                </h5>
                                <p v-if="child.kode_unit" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ child.kode_unit }}
                                </p>
                            </div>
                            <Link
                                :href="route('hris.unit-organisasi.show', child.id)"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm"
                            >
                                Detail
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <JabatanModal
            :show="isJabatanModalOpen"
            :jabatan="selectedJabatan"
            :unitId="unit.id"
            @close="closeJabatanModal"
        />
    </ModuleLayout>
</template>
