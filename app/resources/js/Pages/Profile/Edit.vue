<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <ModuleLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                Profile
            </h2>
        </template>

        <div class="space-y-6">
            <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-slate-800">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-slate-800">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm dark:bg-slate-800">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </ModuleLayout>
</template>
