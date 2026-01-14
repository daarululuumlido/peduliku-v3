<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    whatsapp: '',
});

const submit = () => {
    form.post(route('auth.whatsapp.send'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Login WhatsApp" />

        <div class="mb-4 text-sm text-gray-600">
            Masukkan nomor WhatsApp yang terdaftar untuk menerima kode OTP.
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="whatsapp" value="Nomor WhatsApp" />

                <TextInput
                    id="whatsapp"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.whatsapp"
                    required
                    autofocus
                    placeholder="08123456789"
                />

                <InputError class="mt-2" :message="form.errors.whatsapp" />
            </div>

            <div class="mt-6 flex items-center justify-between">
                <Link
                    :href="route('login')"
                    class="text-sm text-gray-600 underline hover:text-gray-900"
                >
                    Kembali ke Login
                </Link>

                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Mengirim...' : 'Kirim OTP' }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
