<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const roleFilter = ref(props.filters.role || '');
const statusFilter = ref(props.filters.status ?? '');

const applyFilters = () => {
    router.get(route('admin.settings.users.index'), {
        search: search.value,
        role: roleFilter.value,
        status: statusFilter.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const deleteUser = (id, name) => {
    if (confirm(`Apakah Anda yakin ingin menghapus user "${name}"?`)) {
        router.delete(route('admin.settings.users.destroy', id));
    }
};

const toggleStatus = (id, name, isActive) => {
    const action = isActive ? 'menonaktifkan' : 'mengaktifkan';
    if (confirm(`Apakah Anda yakin ingin ${action} user "${name}"?`)) {
        router.post(route('admin.settings.users.toggle-status', id));
    }
};

// Debounce search
let searchTimeout = null;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});
</script>

<template>
    <Head title="Manajemen User" />

    <ModuleLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manajemen User
                </h2>
                <Link
                    :href="route('admin.settings.users.create')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                >
                    + Tambah User
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
                        <!-- Filters -->
                        <div class="flex flex-wrap gap-4 mb-6">
                            <div class="flex-1 min-w-64">
                                <input
                                    type="text"
                                    v-model="search"
                                    placeholder="Cari nama atau email..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                            <div>
                                <select
                                    v-model="roleFilter"
                                    @change="applyFilters"
                                    class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Semua Role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.name">
                                        {{ role.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <select
                                    v-model="statusFilter"
                                    @change="applyFilters"
                                    class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Role
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users.data" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img 
                                                        v-if="user.avatar"
                                                        :src="user.avatar" 
                                                        :alt="user.name"
                                                        class="h-10 w-10 rounded-full"
                                                    />
                                                    <div 
                                                        v-else
                                                        class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center"
                                                    >
                                                        <span class="text-gray-600 font-medium">
                                                            {{ user.name.charAt(0).toUpperCase() }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ user.name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ user.email }}
                                                    </div>
                                                    <div v-if="user.whatsapp" class="text-xs text-gray-400">
                                                        📱 {{ user.whatsapp }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                <span 
                                                    v-for="role in user.roles" 
                                                    :key="role.id"
                                                    class="px-2 py-0.5 text-xs rounded-full"
                                                    :class="{
                                                        'bg-red-100 text-red-800': role.name === 'Super Admin',
                                                        'bg-blue-100 text-blue-800': role.name !== 'Super Admin'
                                                    }"
                                                >
                                                    {{ role.name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                class="px-2 py-1 text-xs rounded-full"
                                                :class="{
                                                    'bg-green-100 text-green-800': user.is_active,
                                                    'bg-red-100 text-red-800': !user.is_active
                                                }"
                                            >
                                                {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link
                                                :href="route('admin.settings.users.edit', user.id)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                v-if="user.id !== $page.props.auth.user.id"
                                                @click="toggleStatus(user.id, user.name, user.is_active)"
                                                class="mr-3"
                                                :class="{
                                                    'text-yellow-600 hover:text-yellow-900': user.is_active,
                                                    'text-green-600 hover:text-green-900': !user.is_active
                                                }"
                                            >
                                                {{ user.is_active ? 'Suspend' : 'Aktifkan' }}
                                            </button>
                                            <button
                                                v-if="user.id !== $page.props.auth.user.id && !user.roles.some(r => r.name === 'Super Admin')"
                                                @click="deleteUser(user.id, user.name)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="users.data.length === 0">
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            Tidak ada user ditemukan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6 flex justify-between items-center" v-if="users.links.length > 3">
                            <div class="text-sm text-gray-700">
                                Menampilkan {{ users.from }} - {{ users.to }} dari {{ users.total }} user
                            </div>
                            <div class="flex gap-1">
                                <template v-for="link in users.links" :key="link.label">
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
