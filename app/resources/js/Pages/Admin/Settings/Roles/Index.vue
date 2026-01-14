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
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manajemen Role
                </h2>
                <Link
                    :href="route('admin.settings.roles.create')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                >
                    + Tambah Role
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Flash Message -->
                <div
                    v-if="$page.props.flash?.message"
                    class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded"
                >
                    {{ $page.props.flash.message }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Role
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Permissions
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Users
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="role in roles.data" :key="role.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span 
                                                    class="px-3 py-1 rounded-full text-sm font-medium"
                                                    :class="{
                                                        'bg-red-100 text-red-800': role.name === 'Super Admin',
                                                        'bg-blue-100 text-blue-800': role.name !== 'Super Admin'
                                                    }"
                                                >
                                                    {{ role.name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                <template v-if="role.name === 'Super Admin'">
                                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded">
                                                        Semua Permission
                                                    </span>
                                                </template>
                                                <template v-else-if="role.permissions.length > 0">
                                                    <span 
                                                        v-for="permission in role.permissions.slice(0, 5)" 
                                                        :key="permission.id"
                                                        class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded"
                                                    >
                                                        {{ permission.name }}
                                                    </span>
                                                    <span 
                                                        v-if="role.permissions.length > 5"
                                                        class="px-2 py-0.5 bg-gray-200 text-gray-600 text-xs rounded"
                                                    >
                                                        +{{ role.permissions.length - 5 }} lainnya
                                                    </span>
                                                </template>
                                                <span v-else class="text-gray-400 text-sm">
                                                    Tidak ada permission
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ role.users_count }} user
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                :href="route('admin.settings.roles.edit', role.id)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                v-if="role.name !== 'Super Admin'"
                                                @click="deleteRole(role.id, role.name)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="roles.data.length === 0">
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            Tidak ada role.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 flex justify-between items-center" v-if="roles.links.length > 3">
                            <div class="text-sm text-gray-700">
                                Menampilkan {{ roles.from }} - {{ roles.to }} dari {{ roles.total }} role
                            </div>
                            <div class="flex gap-1">
                                <template v-for="link in roles.links" :key="link.label">
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        class="px-3 py-1 border rounded text-sm"
                                        :class="{
                                            'bg-blue-600 text-white border-blue-600': link.active,
                                            'bg-white text-gray-700 border-gray-300 hover:bg-gray-50': !link.active
                                        }"
                                        v-html="link.label"
                                    />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
