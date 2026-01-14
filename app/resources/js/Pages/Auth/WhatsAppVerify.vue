<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    whatsapp: String,
    message: String,
});

const form = useForm({
    whatsapp: props.whatsapp,
    otp: '',
});

const resendCooldown = ref(0);
let cooldownInterval = null;

onMounted(() => {
    // Start 60 second cooldown
    resendCooldown.value = 60;
    cooldownInterval = setInterval(() => {
        if (resendCooldown.value > 0) {
            resendCooldown.value--;
        }
    }, 1000);
});

onUnmounted(() => {
    if (cooldownInterval) {
        clearInterval(cooldownInterval);
    }
});

const submit = () => {
    form.post(route('auth.whatsapp.verify'));
};

const resend = () => {
    if (resendCooldown.value > 0) return;
    
    router.post(route('auth.whatsapp.resend'), {
        whatsapp: props.whatsapp,
    }, {
        onSuccess: () => {
            resendCooldown.value = 60;
        }
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Verifikasi OTP" />

        <div v-if="message" class="mb-4 text-sm font-medium text-green-600">
            {{ message }}
        </div>

        <div class="mb-4 text-sm text-gray-600">
            Masukkan kode OTP 6 digit yang dikirim ke WhatsApp 
            <strong>{{ whatsapp }}</strong>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="otp" value="Kode OTP" />

                <TextInput
                    id="otp"
                    type="text"
                    class="mt-1 block w-full text-center text-2xl tracking-widest"
                    v-model="form.otp"
                    required
                    autofocus
                    maxlength="6"
                    placeholder="000000"
                />

                <InputError class="mt-2" :message="form.errors.otp" />
            </div>

            <div class="mt-6">
                <PrimaryButton
                    class="w-full justify-center"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Memverifikasi...' : 'Verifikasi' }}
                </PrimaryButton>
            </div>

            <div class="mt-4 text-center text-sm text-gray-600">
                <span v-if="resendCooldown > 0">
                    Kirim ulang dalam {{ resendCooldown }} detik
                </span>
                <button
                    v-else
                    type="button"
                    @click="resend"
                    class="text-indigo-600 underline hover:text-indigo-900"
                >
                    Kirim ulang kode OTP
                </button>
            </div>

            <div class="mt-4 text-center">
                <Link
                    :href="route('auth.whatsapp')"
                    class="text-sm text-gray-600 underline hover:text-gray-900"
                >
                    Ganti nomor WhatsApp
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
