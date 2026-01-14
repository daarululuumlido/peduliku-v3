<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    permissions: Object,
    groupedPermissions: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

const deletePermission = (id, name) => {
    if (confirm(`Apakah Anda yakin ingin menghapus permission "${name}"?`)) {
        router.delete(route('admin.settings.permissions.destroy', id));
    }
};

// Debounce search
let searchTimeout = null;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.settings.permissions.index'), { search: value }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});
</script>

<template>
    <Head title="Manajemen Permission" />

    <ModuleLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Manajemen Permission
                </h2>
                <Link
                    :href="route('admin.settings.permissions.create')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                >
                    + Tambah Permission
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
                        <!-- Search -->
                        <div class="mb-6">
                            <input
                                type="text"
                                v-model="search"
                                placeholder="Cari permission..."
                                class="w-full md:w-96 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <!-- Grouped View -->
                        <div class="space-y-6">
                            <div 
                                v-for="(perms, module) in groupedPermissions" 
                                :key="module"
                                class="border rounded-lg p-4"
                            >
                                <h3 class="font-semibold text-lg text-gray-900 capitalize mb-3">
                                    {{ module }}
                                    <span class="text-sm font-normal text-gray-500">
                                        ({{ perms.length }} permissions)
                                    </span>
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                    <div 
                                        v-for="permission in perms" 
                                        :key="permission.id"
                                        class="flex items-center justify-between p-2 bg-gray-50 rounded"
                                    >
                                        <span class="text-sm font-mono text-gray-700">
                                            {{ permission.name }}
                                        </span>
                                        <div class="flex gap-2">
                                            <Link
                                                :href="route('admin.settings.permissions.edit', permission.id)"
                                                class="text-indigo-600 hover:text-indigo-900 text-sm"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deletePermission(permission.id, permission.name)"
                                                class="text-red-600 hover:text-red-900 text-sm"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-if="Object.keys(groupedPermissions).length === 0" class="text-center py-8 text-gray-500">
                            Tidak ada permission ditemukan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
