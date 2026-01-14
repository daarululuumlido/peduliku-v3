<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import AddressSearchSelect from '@/Components/AddressSearchSelect.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    kartuKeluarga: Object,
});

const useNewAddress = ref(false);

const form = useForm({
    no_kk: props.kartuKeluarga.no_kk,
    alamat_lengkap: '',
    desa_id: '',
    alamat_id: props.kartuKeluarga.alamat_id || '',
});

const toggleAddressMode = () => {
    useNewAddress.value = !useNewAddress.value;
    if (useNewAddress.value) {
        form.alamat_id = '';
        form.desa_id = '';
        form.alamat_lengkap = '';
    } else {
        form.alamat_lengkap = '';
        form.desa_id = '';
    }
};

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

                        <!-- Address Section -->
                        <div class="border-t pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Alamat Kartu Keluarga</h3>
                                <button
                                    type="button"
                                    @click="toggleAddressMode"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                                >
                                    {{ useNewAddress ? 'Cari Alamat yang Sudah Ada' : 'Buat Alamat Baru' }}
                                </button>
                            </div>

                            <div v-if="!useNewAddress" class="space-y-4">
                                <AddressSearchSelect
                                    v-model="form.alamat_id"
                                    :initial-label="props.kartuKeluarga.alamat?.full_address || ''"
                                    :error="form.errors.alamat_id"
                                />
                            </div>

                            <div v-else class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <AddressSelector
                                    v-model:desa-id="form.desa_id"
                                    v-model:alamat-lengkap="form.alamat_lengkap"
                                    :initial-data="kartuKeluarga.alamat"
                                    :error="form.errors.desa_id"
                                />
                            </div>
                        </div>

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
