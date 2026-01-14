<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import AddressSelector from '@/Components/AddressSelector.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    alamat: Object,
});

const form = useForm({
    _method: 'PUT',
    alamat_lengkap: props.alamat.alamat_lengkap || '',
    desa_id: props.alamat.desa_id || '',
});

const submit = () => {
    form.post(route('admin.alamat.update', props.alamat.id), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Edit Alamat" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.alamat.index')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    ← Kembali
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit Alamat
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        
                        <!-- Address Selector -->
                        <AddressSelector
                            v-model:desa-id="form.desa_id"
                            v-model:alamat-lengkap="form.alamat_lengkap"
                            :initial-data="alamat"
                            :error="form.errors.desa_id"
                        />

                        <!-- Submit Button -->
                        <div class="flex justify-end gap-4 pt-6 border-t">
                            <Link
                                :href="route('admin.alamat.index')"
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
