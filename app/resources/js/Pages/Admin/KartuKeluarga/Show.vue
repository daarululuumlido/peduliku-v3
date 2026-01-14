<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    kartuKeluarga: Object,
});

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
</script>

<template>
    <Head :title="`KK - ${kartuKeluarga.no_kk}`" />

    <ModuleLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('admin.kartu-keluarga.index')"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        ← Kembali
                    </Link>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Detail Kartu Keluarga
                    </h2>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="route('admin.kartu-keluarga.edit', kartuKeluarga.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                    >
                        Edit
                    </Link>
                    <button
                        @click="deleteKK"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Flash Message -->
                <div
                    v-if="$page.props.flash?.message"
                    class="p-4 bg-green-100 border border-green-400 text-green-700 rounded"
                >
                    {{ $page.props.flash.message }}
                </div>

                <!-- KK Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="border-b pb-6 mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 font-mono">
                                {{ kartuKeluarga.no_kk }}
                            </h3>
                            <p class="text-gray-500 mt-1">Nomor Kartu Keluarga</p>
                        </div>

                        <div class="space-y-4">
                            <h4 class="font-semibold text-gray-900 border-b pb-2">Alamat</h4>
                            <template v-if="kartuKeluarga.alamat">
                                <div v-if="kartuKeluarga.alamat.alamat_lengkap">
                                    <label class="text-sm text-gray-500">Detail Alamat</label>
                                    <p class="text-gray-900">{{ kartuKeluarga.alamat.alamat_lengkap }}</p>
                                </div>
                                <div v-if="kartuKeluarga.alamat.desa" class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm text-gray-500">Desa/Kelurahan</label>
                                        <p class="text-gray-900">{{ kartuKeluarga.alamat.desa.name }}</p>
                                    </div>
                                    <div v-if="kartuKeluarga.alamat.desa.district">
                                        <label class="text-sm text-gray-500">Kecamatan</label>
                                        <p class="text-gray-900">{{ kartuKeluarga.alamat.desa.district.name }}</p>
                                    </div>
                                    <div v-if="kartuKeluarga.alamat.desa.district?.city">
                                        <label class="text-sm text-gray-500">Kota/Kabupaten</label>
                                        <p class="text-gray-900">{{ kartuKeluarga.alamat.desa.district.city.name }}</p>
                                    </div>
                                    <div v-if="kartuKeluarga.alamat.desa.district?.city?.province">
                                        <label class="text-sm text-gray-500">Provinsi</label>
                                        <p class="text-gray-900">{{ kartuKeluarga.alamat.desa.district.city.province.name }}</p>
                                    </div>
                                </div>
                            </template>
                            <p v-else class="text-gray-400 italic">Alamat belum diisi</p>
                        </div>
                    </div>
                </div>

                <!-- Anggota KK -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="font-semibold text-gray-900 border-b pb-2 mb-4">
                            Anggota Keluarga ({{ kartuKeluarga.anggota?.length || 0 }} orang)
                        </h4>

                        <div v-if="kartuKeluarga.anggota && kartuKeluarga.anggota.length > 0" class="space-y-3">
                            <div 
                                v-for="anggota in kartuKeluarga.anggota" 
                                :key="anggota.id"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                            >
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-gray-600 font-medium">
                                            {{ anggota.nama.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ anggota.nama }}</p>
                                        <p class="text-sm text-gray-500 font-mono">{{ anggota.nik }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <Link
                                        :href="route('admin.orang.show', anggota.id)"
                                        class="text-blue-600 hover:text-blue-900 text-sm"
                                    >
                                        Lihat
                                    </Link>
                                    <button
                                        @click="removeMember(anggota.id, anggota.nama)"
                                        class="text-red-600 hover:text-red-900 text-sm"
                                    >
                                        Hapus dari KK
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-gray-400 text-center py-4">
                            Belum ada anggota dalam KK ini.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
