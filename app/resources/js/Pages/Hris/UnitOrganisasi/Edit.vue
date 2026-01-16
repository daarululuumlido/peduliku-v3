<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    unit: Object,
    units: Array,
});

const form = useForm({
    parent_id: props.unit.parent_id || '',
    nama_unit: props.unit.nama_unit,
    kode_unit: props.unit.kode_unit || '',
    urutan: props.unit.urutan || 0,
});

const submit = () => {
    form.put(route('hris.unit-organisasi.update', props.unit.id));
};
</script>

<template>
    <Head title="Edit Unit Organisasi" />

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
                    Edit Unit Organisasi
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-slate-800">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Parent Unit -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Parent Unit (Opsional)
                            </label>
                            <select
                                v-model="form.parent_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.parent_id }"
                            >
                                <option value="">Tidak ada parent (Root level)</option>
                                <option 
                                    v-for="u in units" 
                                    :key="u.id" 
                                    :value="u.id"
                                    :disabled="u.id === unit.id"
                                >
                                    {{ u.nama_unit }}
                                </option>
                            </select>
                            <p v-if="form.errors.parent_id" class="mt-1 text-sm text-red-600">
                                {{ form.errors.parent_id }}
                            </p>
                        </div>

                        <!-- Nama Unit -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nama Unit <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="form.nama_unit"
                                placeholder="Contoh: Bagian Pengembangan SDM"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.nama_unit }"
                            />
                            <p v-if="form.errors.nama_unit" class="mt-1 text-sm text-red-600">
                                {{ form.errors.nama_unit }}
                            </p>
                        </div>

                        <!-- Kode Unit -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Kode Unit (Opsional)
                            </label>
                            <input
                                type="text"
                                v-model="form.kode_unit"
                                placeholder="Contoh: TMI, SMA-IT"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.kode_unit }"
                            />
                            <p v-if="form.errors.kode_unit" class="mt-1 text-sm text-red-600">
                                {{ form.errors.kode_unit }}
                            </p>
                        </div>

                        <!-- Urutan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Urutan
                            </label>
                            <input
                                type="number"
                                v-model="form.urutan"
                                min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                :class="{ 'border-red-500': form.errors.urutan }"
                            />
                            <p v-if="form.errors.urutan" class="mt-1 text-sm text-red-600">
                                {{ form.errors.urutan }}
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
