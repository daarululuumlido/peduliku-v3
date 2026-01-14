<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
    roles: Array,
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    whatsapp: props.user.whatsapp || '',
    roles: props.user.roles.map(r => r.id),
});

const toggleRole = (roleId) => {
    const index = form.roles.indexOf(roleId);
    if (index > -1) {
        form.roles.splice(index, 1);
    } else {
        form.roles.push(roleId);
    }
};

const submit = () => {
    form.put(route('admin.settings.users.update', props.user.id));
};
</script>

<template>
    <Head :title="`Edit User - ${user.name}`" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.settings.users.index')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    ← Kembali
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit User: {{ user.name }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Name -->
                        <div>
                            <InputLabel for="name" value="Nama" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Email -->
                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- WhatsApp -->
                        <div>
                            <InputLabel for="whatsapp" value="No. WhatsApp (opsional)" />
                            <TextInput
                                id="whatsapp"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.whatsapp"
                                placeholder="08xxxxxxxxxx"
                            />
                            <InputError class="mt-2" :message="form.errors.whatsapp" />
                        </div>

                        <!-- Password -->
                        <div>
                            <InputLabel value="Ubah Password (opsional)" />
                            <p class="text-sm text-gray-500 mb-2">
                                Kosongkan jika tidak ingin mengubah password.
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <TextInput
                                        type="password"
                                        class="block w-full"
                                        v-model="form.password"
                                        placeholder="Password baru"
                                    />
                                    <InputError class="mt-2" :message="form.errors.password" />
                                </div>
                                <div>
                                    <TextInput
                                        type="password"
                                        class="block w-full"
                                        v-model="form.password_confirmation"
                                        placeholder="Konfirmasi password"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Roles -->
                        <div>
                            <InputLabel value="Role" />
                            <p class="text-sm text-gray-500 mb-3">
                                Pilih minimal satu role untuk user ini.
                            </p>
                            <div class="space-y-2">
                                <label 
                                    v-for="role in roles" 
                                    :key="role.id"
                                    class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                    :class="{ 'border-blue-500 bg-blue-50': form.roles.includes(role.id) }"
                                >
                                    <input
                                        type="checkbox"
                                        :value="role.id"
                                        :checked="form.roles.includes(role.id)"
                                        @change="toggleRole(role.id)"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <div>
                                        <span 
                                            class="font-medium"
                                            :class="{
                                                'text-red-700': role.name === 'Super Admin',
                                                'text-gray-900': role.name !== 'Super Admin'
                                            }"
                                        >
                                            {{ role.name }}
                                        </span>
                                        <p v-if="role.name === 'Super Admin'" class="text-xs text-red-500">
                                            Akses penuh ke semua fitur
                                        </p>
                                    </div>
                                </label>
                            </div>
                            <InputError class="mt-2" :message="form.errors.roles" />
                        </div>

                        <!-- User Info -->
                        <div class="p-4 bg-gray-50 rounded-lg text-sm text-gray-600">
                            <p><strong>Dibuat:</strong> {{ new Date(user.created_at).toLocaleString('id-ID') }}</p>
                            <p v-if="user.email_verified_at">
                                <strong>Email Terverifikasi:</strong> {{ new Date(user.email_verified_at).toLocaleString('id-ID') }}
                            </p>
                            <p v-if="user.google_id">
                                <strong>Login via:</strong> Google OAuth
                            </p>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end gap-4 pt-6 border-t">
                            <Link
                                :href="route('admin.settings.users.index')"
                                class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
                            >
                                Batal
                            </Link>
                            <PrimaryButton
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
