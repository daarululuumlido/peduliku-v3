<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    roles: Object,
});

const deleteRole = (id, name) => {
    if (confirm(`Apakah Anda yakin ingin menghapus role "${name}"?`)) {
        router.delete(route('admin.settings.roles.destroy', id));
    }
};
</script>

<template>
    <Head title="Manajemen Role" />

    <ModuleLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Manajemen Role
            </h2>
        </template>

        <!-- Page Header with Actions -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Role</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola role dan permissions untuk hak akses.</p>
            </div>
            <Link
                :href="route('admin.settings.roles.create')"
                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm transition btn-primary"
            >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Role
            </Link>
        </div>

        <!-- Card Container -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                        <thead class="bg-gray-50 dark:bg-slate-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Nama Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Permissions
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Users
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-slate-800 dark:divide-slate-700">
                            <tr v-for="role in roles.data" :key="role.id" class="hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span 
                                            class="px-3 py-1 rounded-full text-sm font-medium"
                                            :class="{
                                                'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300': role.name === 'Super Admin',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300': role.name !== 'Super Admin'
                                            }"
                                        >
                                            {{ role.name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        <template v-if="role.name === 'Super Admin'">
                                            <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded dark:bg-red-900/30 dark:text-red-300">
                                                Semua Permission
                                            </span>
                                        </template>
                                        <template v-else-if="role.permissions.length > 0">
                                            <span 
                                                v-for="permission in role.permissions.slice(0, 5)" 
                                                :key="permission.id"
                                                class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded dark:bg-slate-600 dark:text-gray-300"
                                            >
                                                {{ permission.name }}
                                            </span>
                                            <span 
                                                v-if="role.permissions.length > 5"
                                                class="px-2 py-0.5 bg-gray-200 text-gray-600 text-xs rounded dark:bg-slate-500 dark:text-gray-300"
                                            >
                                                +{{ role.permissions.length - 5 }} lainnya
                                            </span>
                                        </template>
                                        <span v-else class="text-gray-400 dark:text-gray-500 text-sm">
                                            Tidak ada permission
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ role.users_count }} user
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link
                                        :href="route('admin.settings.roles.edit', role.id)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        v-if="role.name !== 'Super Admin'"
                                        @click="deleteRole(role.id, role.name)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="roles.data.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada role.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex justify-between items-center" v-if="roles.links.length > 3">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Menampilkan {{ roles.from }} - {{ roles.to }} dari {{ roles.total }} role
                    </div>
                    <div class="flex gap-1">
                        <template v-for="link in roles.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="px-3 py-1 border rounded-lg text-sm transition-colors"
                                :class="{
                                    'bg-indigo-600 text-white border-indigo-600': link.active,
                                    'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-slate-700 dark:text-gray-200 dark:border-slate-600 dark:hover:bg-slate-600': !link.active
                                }"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
