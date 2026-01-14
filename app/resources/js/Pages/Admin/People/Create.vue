<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import AddressSelector from '@/Components/AddressSelector.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    nik: '',
    nama: '',
    gender: '',
    tanggal_lahir: '',
    tempat_lahir: '',
    nama_ibu_kandung: '',
    no_whatsapp: '',
    alamat_lengkap: '',
    desa_id: '',
    dokumen: [],
});

const submit = () => {
    form.post(route('admin.orang.store'));
};
</script>

<template>
    <Head title="Tambah Orang" />

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
                    Tambah Orang Baru
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- NIK -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                NIK <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="form.nik"
                                maxlength="16"
                                placeholder="16 digit NIK"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 font-mono"
                                :class="{ 'border-red-500': form.errors.nik }"
                            />
                            <p v-if="form.errors.nik" class="mt-1 text-sm text-red-600">
                                {{ form.errors.nik }}
                            </p>
                        </div>

                        <!-- Nama -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="form.nama"
                                placeholder="Nama lengkap sesuai KTP"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                :class="{ 'border-red-500': form.errors.nama }"
                            />
                            <p v-if="form.errors.nama" class="mt-1 text-sm text-red-600">
                                {{ form.errors.nama }}
                            </p>
                        </div>

                        <!-- Gender -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Jenis Kelamin <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-6">
                                <label class="inline-flex items-center">
                                    <input
                                        type="radio"
                                        v-model="form.gender"
                                        value="L"
                                        class="form-radio text-blue-600"
                                    />
                                    <span class="ml-2">Laki-laki</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input
                                        type="radio"
                                        v-model="form.gender"
                                        value="P"
                                        class="form-radio text-blue-600"
                                    />
                                    <span class="ml-2">Perempuan</span>
                                </label>
                            </div>
                            <p v-if="form.errors.gender" class="mt-1 text-sm text-red-600">
                                {{ form.errors.gender }}
                            </p>
                        </div>

                        <!-- Tempat & Tanggal Lahir -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tempat Lahir <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    v-model="form.tempat_lahir"
                                    placeholder="Kota/Kabupaten kelahiran"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': form.errors.tempat_lahir }"
                                />
                                <p v-if="form.errors.tempat_lahir" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.tempat_lahir }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Lahir <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    v-model="form.tanggal_lahir"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-500': form.errors.tanggal_lahir }"
                                />
                                <p v-if="form.errors.tanggal_lahir" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.tanggal_lahir }}
                                </p>
                            </div>
                        </div>

                        <!-- Nama Ibu & WhatsApp -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Ibu Kandung
                                </label>
                                <input
                                    type="text"
                                    v-model="form.nama_ibu_kandung"
                                    placeholder="Nama ibu kandung"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    No. WhatsApp
                                </label>
                                <input
                                    type="text"
                                    v-model="form.no_whatsapp"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                        </div>

                        <!-- Address Selector -->
                        <AddressSelector
                            v-model:desa-id="form.desa_id"
                            v-model:alamat-lengkap="form.alamat_lengkap"
                            :error="form.errors.desa_id"
                        />

                        <!-- Dokumen Upload -->
                        <div class="pt-6 border-t">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Dokumen</h3>
                            <div class="space-y-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Pilih Dokumen (KTP, KK, Akta, dll)
                                </label>
                                <input
                                    type="file"
                                    multiple
                                    @input="form.dokumen = $event.target.files"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                                <p class="text-xs text-gray-500">Maksimal 10MB per file. Format: Jpg, Png, Pdf.</p>
                                <p v-if="form.errors.dokumen" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.dokumen }}
                                </p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end gap-4 pt-6 border-t">
                            <Link
                                :href="route('admin.orang.index')"
                                class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
