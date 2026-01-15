<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    kartuKeluarga: Object,
    statusHubunganOptions: Object,
});

// Add member form
const showAddMemberForm = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const selectedOrang = ref(null);
const selectedStatusHubungan = ref('');
const isSearching = ref(false);

const deleteKK = () => {
    if (confirm(`Apakah Anda yakin ingin menghapus KK "${props.kartuKeluarga.no_kk}"?`)) {
        router.delete(route('admin.kartu-keluarga.destroy', props.kartuKeluarga.id));
    }
};

const removeMember = (orangId, nama) => {
    if (confirm(`Hapus "${nama}" dari KK ini?`)) {
        router.post(route('admin.kartu-keluarga.remove-member', props.kartuKeluarga.id), {
            orang_id: orangId,
        });
    }
};

// Search orang with debounce
let searchTimeout = null;
watch(searchQuery, (value) => {
    if (value.length < 2) {
        searchResults.value = [];
        return;
    }
    
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(async () => {
        isSearching.value = true;
        try {
            const response = await axios.get(route('admin.orang.search'), {
                params: { query: value }
            });
            searchResults.value = response.data;
        } catch (error) {
            console.error('Search error:', error);
        } finally {
            isSearching.value = false;
        }
    }, 300);
});

const selectOrang = (orang) => {
    selectedOrang.value = orang;
    searchQuery.value = orang.nama;
    searchResults.value = [];
};

const clearSelection = () => {
    selectedOrang.value = null;
    searchQuery.value = '';
    searchResults.value = [];
    selectedStatusHubungan.value = '';
};

// Format status_hubungan for display (e.g., "kepala_keluarga" -> "Kepala Keluarga")
const formatStatusHubungan = (status) => {
    if (!status) return '';
    return status
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
};

const addMember = () => {
    if (!selectedOrang.value || !selectedStatusHubungan.value) {
        alert('Pilih orang dan status hubungan terlebih dahulu.');
        return;
    }
    
    router.post(route('admin.kartu-keluarga.add-member', props.kartuKeluarga.id), {
        orang_id: selectedOrang.value.id,
        status_hubungan: selectedStatusHubungan.value,
    }, {
        onSuccess: () => {
            clearSelection();
            showAddMemberForm.value = false;
        }
    });
};
</script>

<template>
    <Head :title="`KK - ${kartuKeluarga.no_kk}`" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.kartu-keluarga.index')"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    ← Kembali
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                    Detail Kartu Keluarga
                </h2>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Flash Message -->
            <div
                v-if="$page.props.flash?.message"
                class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900/20 dark:border-green-700 dark:text-green-400"
            >
                {{ $page.props.flash.message }}
            </div>

            <!-- KK Info Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
                <div class="p-6">
                    <!-- Header with No. KK and Action Buttons -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b pb-6 mb-6 dark:border-slate-700">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 font-mono dark:text-white">
                                {{ kartuKeluarga.no_kk }}
                            </h3>
                            <p class="text-gray-500 mt-1 dark:text-gray-400">Nomor Kartu Keluarga</p>
                        </div>
                        <!-- Action Buttons (Moved Here) -->
                        <div class="flex gap-2">
                            <Link
                                :href="route('admin.kartu-keluarga.edit', kartuKeluarga.id)"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                                Edit
                            </Link>
                            <button
                                @click="deleteKK"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>

                    <!-- Alamat Section -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-900 border-b pb-2 dark:text-white dark:border-slate-700">Alamat</h4>
                        <template v-if="kartuKeluarga.alamat">
                            <div v-if="kartuKeluarga.alamat.alamat_lengkap">
                                <label class="text-sm text-gray-500 dark:text-gray-400">Detail Alamat</label>
                                <p class="text-gray-900 dark:text-white">{{ kartuKeluarga.alamat.alamat_lengkap }}</p>
                            </div>
                            <div v-if="kartuKeluarga.alamat.desa" class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-500 dark:text-gray-400">Desa/Kelurahan</label>
                                    <p class="text-gray-900 dark:text-white">{{ kartuKeluarga.alamat.desa.name }}</p>
                                </div>
                                <div v-if="kartuKeluarga.alamat.desa.district">
                                    <label class="text-sm text-gray-500 dark:text-gray-400">Kecamatan</label>
                                    <p class="text-gray-900 dark:text-white">{{ kartuKeluarga.alamat.desa.district.name }}</p>
                                </div>
                                <div v-if="kartuKeluarga.alamat.desa.district?.city">
                                    <label class="text-sm text-gray-500 dark:text-gray-400">Kota/Kabupaten</label>
                                    <p class="text-gray-900 dark:text-white">{{ kartuKeluarga.alamat.desa.district.city.name }}</p>
                                </div>
                                <div v-if="kartuKeluarga.alamat.desa.district?.city?.province">
                                    <label class="text-sm text-gray-500 dark:text-gray-400">Provinsi</label>
                                    <p class="text-gray-900 dark:text-white">{{ kartuKeluarga.alamat.desa.district.city.province.name }}</p>
                                </div>
                            </div>
                        </template>
                        <p v-else class="text-gray-400 italic dark:text-gray-500">Alamat belum diisi</p>
                    </div>
                </div>
            </div>

            <!-- Anggota KK Card -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
                <div class="p-6">
                    <div class="flex items-center justify-between border-b pb-4 mb-4 dark:border-slate-700">
                        <h4 class="font-semibold text-gray-900 dark:text-white">
                            Anggota Keluarga ({{ kartuKeluarga.anggota?.length || 0 }} orang)
                        </h4>
                        <button
                            @click="showAddMemberForm = !showAddMemberForm"
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                            </svg>
                            Tambah Anggota
                        </button>
                    </div>

                    <!-- Add Member Form -->
                    <div v-if="showAddMemberForm" class="mb-6 p-4 bg-gray-50 rounded-lg border dark:bg-slate-900 dark:border-slate-700">
                        <h5 class="font-medium text-gray-900 mb-4 dark:text-white">Tambah Anggota Keluarga</h5>
                        <div class="grid gap-4 md:grid-cols-3">
                            <!-- Person Search -->
                            <div class="relative md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">Cari Orang</label>
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    :disabled="selectedOrang !== null"
                                    placeholder="Ketik nama atau NIK..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white disabled:bg-gray-100 dark:disabled:bg-slate-800"
                                />
                                <!-- Selected Person Display -->
                                <div v-if="selectedOrang" class="mt-2 p-2 bg-indigo-50 rounded-lg flex items-center justify-between dark:bg-indigo-900/20">
                                    <div>
                                        <span class="font-medium text-indigo-700 dark:text-indigo-400">{{ selectedOrang.nama }}</span>
                                        <span class="text-sm text-gray-500 ml-2 font-mono dark:text-gray-400">{{ selectedOrang.nik }}</span>
                                    </div>
                                    <button @click="clearSelection" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <!-- Search Results Dropdown -->
                                <div 
                                    v-if="searchResults.length > 0 && !selectedOrang" 
                                    class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto dark:bg-slate-700 dark:border-slate-600"
                                >
                                    <button
                                        v-for="orang in searchResults"
                                        :key="orang.id"
                                        @click="selectOrang(orang)"
                                        class="w-full px-4 py-3 text-left hover:bg-gray-50 border-b last:border-b-0 dark:hover:bg-slate-600 dark:border-slate-600"
                                    >
                                        <div class="font-medium text-gray-900 dark:text-white">{{ orang.nama }}</div>
                                        <div class="text-sm text-gray-500 font-mono dark:text-gray-400">{{ orang.nik }}</div>
                                    </button>
                                </div>
                                <div v-if="isSearching" class="absolute right-3 top-9">
                                    <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Status Hubungan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">Status Hubungan</label>
                                <select
                                    v-model="selectedStatusHubungan"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                >
                                    <option value="">Pilih Status...</option>
                                    <option v-for="(label, value) in statusHubunganOptions" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            <button
                                @click="showAddMemberForm = false; clearSelection()"
                                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 dark:text-gray-300 dark:border-slate-600 dark:hover:bg-slate-700"
                            >
                                Batal
                            </button>
                            <button
                                @click="addMember"
                                :disabled="!selectedOrang || !selectedStatusHubungan"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Tambahkan
                            </button>
                        </div>
                    </div>

                    <!-- Member List -->
                    <div v-if="kartuKeluarga.anggota && kartuKeluarga.anggota.length > 0" class="space-y-3">
                        <div 
                            v-for="membership in kartuKeluarga.anggota" 
                            :key="membership.id"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg dark:bg-slate-900"
                        >
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center dark:bg-slate-700">
                                    <span class="text-gray-600 font-medium dark:text-gray-300">
                                        {{ membership.orang.nama.charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ membership.orang.nama }}</p>
                                    <p class="text-sm text-gray-500 font-mono dark:text-gray-400">{{ membership.orang.nik }}</p>
                                    <p class="text-xs text-indigo-600 mt-1 dark:text-indigo-400">{{ formatStatusHubungan(membership.status_hubungan) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <Link
                                    :href="route('admin.orang.show', membership.orang.id)"
                                    class="text-blue-600 hover:text-blue-900 text-sm dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    Lihat
                                </Link>
                                <button
                                    @click="removeMember(membership.orang.id, membership.orang.nama)"
                                    class="text-red-600 hover:text-red-900 text-sm dark:text-red-400 dark:hover:text-red-300"
                                >
                                    Hapus dari KK
                                </button>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-gray-400 text-center py-4 dark:text-gray-500">
                        Belum ada anggota dalam KK ini.
                    </p>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
