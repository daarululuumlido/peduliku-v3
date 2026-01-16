<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    unit: Object,
});

const form = useForm({
    nama_jabatan: '',
    is_pimpinan: false,
    kuota_sdm: 1,
    keterangan: '',
});

const submit = () => {
    form.post(route('hris.unit-organisasi.master-jabatan.store', props.unit.id));
};
</script>

<template>
    <Head title="Tambah Jabatan" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('hris.unit-organisasi.show', unit.id)"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    ← Kembali
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                    Tambah Jabatan - {{ unit.nama_unit }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-slate-800">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Nama Jabatan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nama Jabatan <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="form.nama_jabatan"
                                placeholder="Contoh: Kepala Bagian"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.nama_jabatan }"
                            />
                            <p v-if="form.errors.nama_jabatan" class="mt-1 text-sm text-red-600">
                                {{ form.errors.nama_jabatan }}
                            </p>
                        </div>

                        <!-- Is Pimpinan -->
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="is_pimpinan"
                                v-model="form.is_pimpinan"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-slate-700 dark:border-slate-600 overflow-hidden"
                            />
                            <label for="is_pimpinan" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                                Jabatan Pimpinan?
                            </label>
                            <p v-if="form.errors.is_pimpinan" class="ml-3 text-sm text-red-600">
                                {{ form.errors.is_pimpinan }}
                            </p>
                        </div>

                        <!-- Kuota SDM -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Kuota SDM
                            </label>
                            <input
                                type="number"
                                v-model="form.kuota_sdm"
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.kuota_sdm }"
                            />
                            <p class="text-xs text-gray-500 mt-1">Jumlah maksimal pegawai yang dapat menjabat jabatan ini.</p>
                            <p v-if="form.errors.kuota_sdm" class="mt-1 text-sm text-red-600">
                                {{ form.errors.kuota_sdm }}
                            </p>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Keterangan (Opsional)
                            </label>
                            <textarea
                                v-model="form.keterangan"
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.keterangan }"
                            ></textarea>
                            <p v-if="form.errors.keterangan" class="mt-1 text-sm text-red-600">
                                {{ form.errors.keterangan }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                            <Link
                                :href="route('hris.unit-organisasi.show', unit.id)"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-600 dark:text-gray-200 dark:hover:bg-slate-700"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
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
