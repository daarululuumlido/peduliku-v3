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
</script>

<template>
    <Head :title="`Detail - ${orang.nama}`" />

    <ModuleLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('admin.orang.index')"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        ← Kembali
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Detail Orang
                    </h2>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="route('admin.orang.edit', orang.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                    >
                        Edit
                    </Link>
                    <button
                        @click="deleteOrang"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header with Name -->
                        <div class="border-b pb-6 mb-6">
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ orang.nama }}
                            </h3>
                            <p class="text-gray-500 font-mono mt-1">NIK: {{ orang.nik }}</p>
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
                        <div class="mt-8" v-if="orang.kartu_keluarga">
                            <h4 class="font-semibold text-gray-900 border-b pb-2 mb-4">Kartu Keluarga</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="font-mono text-lg">No. KK: {{ orang.kartu_keluarga.no_kk }}</p>
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
