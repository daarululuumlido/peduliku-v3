<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import AddressSelector from '@/Components/AddressSelector.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    kartuKeluarga: Object,
});

const form = useForm({
    no_kk: props.kartuKeluarga.no_kk,
    alamat_lengkap: props.kartuKeluarga.alamat?.alamat_lengkap || '',
    desa_id: props.kartuKeluarga.alamat?.desa_id || '',
});

const submit = () => {
    form.put(route('admin.kartu-keluarga.update', props.kartuKeluarga.id));
};
</script>

<template>
    <Head :title="`Edit KK - ${kartuKeluarga.no_kk}`" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.kartu-keluarga.index')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    ← Kembali
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Kartu Keluarga
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- No KK -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                No. Kartu Keluarga <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="form.no_kk"
                                maxlength="16"
                                placeholder="16 digit nomor KK"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 font-mono"
                                :class="{ 'border-red-500': form.errors.no_kk }"
                            />
                            <p v-if="form.errors.no_kk" class="mt-1 text-sm text-red-600">
                                {{ form.errors.no_kk }}
                            </p>
                        </div>

                        <!-- Address Selector -->
                        <AddressSelector
                            v-model:desa-id="form.desa_id"
                            v-model:alamat-lengkap="form.alamat_lengkap"
                            :initial-data="kartuKeluarga.alamat"
                            :error="form.errors.desa_id"
                        />

                        <!-- Submit Button -->
                        <div class="flex justify-end gap-4 pt-6 border-t">
                            <Link
                                :href="route('admin.kartu-keluarga.index')"
                                class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition disabled:opacity-50"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
