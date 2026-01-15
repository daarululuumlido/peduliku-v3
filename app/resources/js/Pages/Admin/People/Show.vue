<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    orang: Object,
});

const deleteOrang = () => {
    if (confirm(`Apakah Anda yakin ingin menghapus data "${props.orang.nama}"?`)) {
        router.delete(route('admin.orang.destroy', props.orang.id));
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

// Format status_hubungan for display (e.g., "kepala_keluarga" -> "Kepala Keluarga")
const formatStatusHubungan = (status) => {
    if (!status) return '';
    return status
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
};
</script>

<template>
    <Head :title="`Detail - ${orang.nama}`" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.orang.index')"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    ← Kembali
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                    Detail Orang
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header with Name and Action Buttons -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b pb-6 mb-6 dark:border-slate-700">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ orang.nama }}
                                </h3>
                                <p class="text-gray-500 font-mono mt-1 dark:text-gray-400">NIK: {{ orang.nik }}</p>
                            </div>
                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <Link
                                    :href="route('admin.orang.edit', orang.id)"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    Edit
                                </Link>
                                <button
                                    @click="deleteOrang"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Info -->
                            <div class="space-y-4">
                                <h4 class="font-semibold text-gray-900 border-b pb-2">Data Pribadi</h4>
                                
                                <div>
                                    <label class="text-sm text-gray-500">Jenis Kelamin</label>
                                    <p class="text-gray-900">{{ orang.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                </div>

                                <div>
                                    <label class="text-sm text-gray-500">Tempat, Tanggal Lahir</label>
                                    <p class="text-gray-900">{{ orang.tempat_lahir }}, {{ formatDate(orang.tanggal_lahir) }}</p>
                                </div>

                                <div v-if="orang.nama_ibu_kandung">
                                    <label class="text-sm text-gray-500">Nama Ibu Kandung</label>
                                    <p class="text-gray-900">{{ orang.nama_ibu_kandung }}</p>
                                </div>

                                <div v-if="orang.no_whatsapp">
                                    <label class="text-sm text-gray-500">No. WhatsApp</label>
                                    <p class="text-gray-900">
                                        <a :href="`https://wa.me/${orang.no_whatsapp.replace(/^0/, '62')}`" target="_blank" class="text-green-600 hover:underline">
                                            📱 {{ orang.no_whatsapp }}
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <!-- Address Info -->
                            <div class="space-y-4">
                                <h4 class="font-semibold text-gray-900 border-b pb-2">Alamat KTP</h4>
                                
                                <template v-if="orang.alamat_ktp">
                                    <div v-if="orang.alamat_ktp.alamat_lengkap">
                                        <label class="text-sm text-gray-500">Detail Alamat</label>
                                        <p class="text-gray-900">{{ orang.alamat_ktp.alamat_lengkap }}</p>
                                    </div>

                                    <div v-if="orang.alamat_ktp.desa">
                                        <label class="text-sm text-gray-500">Desa/Kelurahan</label>
                                        <p class="text-gray-900">{{ orang.alamat_ktp.desa.name }}</p>
                                    </div>

                                    <div v-if="orang.alamat_ktp.desa?.district">
                                        <label class="text-sm text-gray-500">Kecamatan</label>
                                        <p class="text-gray-900">{{ orang.alamat_ktp.desa.district.name }}</p>
                                    </div>

                                    <div v-if="orang.alamat_ktp.desa?.district?.city">
                                        <label class="text-sm text-gray-500">Kota/Kabupaten</label>
                                        <p class="text-gray-900">{{ orang.alamat_ktp.desa.district.city.name }}</p>
                                    </div>

                                    <div v-if="orang.alamat_ktp.desa?.district?.city?.province">
                                        <label class="text-sm text-gray-500">Provinsi</label>
                                        <p class="text-gray-900">{{ orang.alamat_ktp.desa.district.city.province.name }}</p>
                                    </div>
                                </template>
                                <p v-else class="text-gray-400 italic">Alamat belum diisi</p>
                            </div>
                        </div>

                        <!-- Family Card Info -->
                        <div class="mt-8" v-if="orang.kartu_keluarga_anggota && orang.kartu_keluarga_anggota.length > 0">
                            <h4 class="font-semibold text-gray-900 border-b pb-2 mb-4 dark:text-white dark:border-slate-700">Kartu Keluarga</h4>
                            <div class="space-y-3">
                                <Link
                                    v-for="membership in orang.kartu_keluarga_anggota"
                                    :key="membership.id"
                                    :href="route('admin.kartu-keluarga.show', membership.kartu_keluarga.id)"
                                    class="block bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition group dark:bg-slate-900 dark:hover:bg-slate-800"
                                >
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-mono text-lg text-gray-900 group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400">
                                                No. KK: {{ membership.kartu_keluarga.no_kk }}
                                            </p>
                                            <p class="text-sm text-indigo-600 mt-1 dark:text-indigo-400">
                                                {{ formatStatusHubungan(membership.status_hubungan) }}
                                            </p>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </div>
                                </Link>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="mt-8" v-if="orang.dokumen && orang.dokumen.length > 0">
                            <h4 class="font-semibold text-gray-900 border-b pb-2 mb-4">Dokumen</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    v-for="doc in orang.dokumen"
                                    :key="doc.id"
                                    class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
                                >
                                    <span class="text-2xl">📄</span>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ doc.nama_file }}</p>
                                        <p class="text-sm text-gray-500">{{ doc.mime_type }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
