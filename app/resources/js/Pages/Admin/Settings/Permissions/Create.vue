<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
});

const submit = () => {
    form.post(route('admin.settings.permissions.store'));
};
</script>

<template>
    <Head title="Tambah Permission" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.settings.permissions.index')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    ← Kembali
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Tambah Permission Baru
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Info -->
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                            <p class="font-medium mb-1">Format nama permission:</p>
                            <code class="bg-blue-100 px-2 py-0.5 rounded">module.action</code>
                            <p class="mt-2">Contoh: <code class="bg-blue-100 px-1">users.create</code>, <code class="bg-blue-100 px-1">orang.delete</code></p>
                        </div>

                        <!-- Permission Name -->
                        <div>
                            <InputLabel for="name" value="Nama Permission" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full font-mono"
                                v-model="form.name"
                                required
                                autofocus
                                placeholder="module.action"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end gap-4 pt-6 border-t">
                            <Link
                                :href="route('admin.settings.permissions.index')"
                                class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
                            >
                                Batal
                            </Link>
                            <PrimaryButton
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Permission' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
