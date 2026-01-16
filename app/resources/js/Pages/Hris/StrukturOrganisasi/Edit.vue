<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    struktur: Object,
});

const form = useForm({
    nama_periode: props.struktur.nama_periode,
    tgl_mulai: props.struktur.tgl_mulai,
    tgl_selesai: props.struktur.tgl_selesai,
    is_active: props.struktur.is_active,
});

const submit = () => {
    form.put(route('hris.struktur-organisasi.update', props.struktur.id));
};
</script>

<template>
    <Head title="Edit Struktur Organisasi" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('hris.struktur-organisasi.index')"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    ← Kembali
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                    Edit Struktur Organisasi
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-slate-800">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Nama Periode -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nama Periode <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="form.nama_periode"
                                placeholder="Contoh: Tahun Pelajaran 2025 - 2026"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.nama_periode }"
                            />
                            <p v-if="form.errors.nama_periode" class="mt-1 text-sm text-red-600">
                                {{ form.errors.nama_periode }}
                            </p>
                        </div>

                        <!-- Tanggal Mulai & Selesai -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    v-model="form.tgl_mulai"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.tgl_mulai }"
                                />
                                <p v-if="form.errors.tgl_mulai" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.tgl_mulai }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tanggal Selesai
                                </label>
                                <input
                                    type="date"
                                    v-model="form.tgl_selesai"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.tgl_selesai }"
                                />
                                <p v-if="form.errors.tgl_selesai" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.tgl_selesai }}
                                </p>
                            </div>
                        </div>

                        <!-- Status Aktif -->
                        <div>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.is_active"
                                    class="form-checkbox h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Set sebagai periode aktif</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Menonaktifkan semua periode lain
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                            <Link
                                :href="route('hris.struktur-organisasi.show', struktur.id)"
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
