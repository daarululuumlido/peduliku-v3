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
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Manajemen Permission
            </h2>
        </template>

        <!-- Page Header with Actions -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Permission</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola permission untuk mengontrol akses fitur.</p>
            </div>
            <Link
                :href="route('admin.settings.permissions.create')"
                class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm transition btn-primary"
            >
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Permission
            </Link>
        </div>

        <!-- Card Container -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
            <div class="p-6">
                <!-- Search -->
                <div class="mb-6">
                    <input
                        type="text"
                        v-model="search"
                        placeholder="Cari permission..."
                        class="w-full md:w-96 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                    />
                </div>

                <!-- Grouped View -->
                <div class="space-y-6">
                    <div 
                        v-for="(perms, module) in groupedPermissions" 
                        :key="module"
                        class="border border-gray-200 rounded-xl p-4 dark:border-slate-700"
                    >
                        <h3 class="font-semibold text-lg text-gray-900 dark:text-white capitalize mb-3">
                            {{ module }}
                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                ({{ perms.length }} permissions)
                            </span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            <div 
                                v-for="permission in perms" 
                                :key="permission.id"
                                class="flex items-center justify-between p-2 bg-gray-50 rounded-lg dark:bg-slate-700"
                            >
                                <span class="text-sm font-mono text-gray-700 dark:text-gray-300">
                                    {{ permission.name }}
                                </span>
                                <div class="flex gap-2">
                                    <Link
                                        :href="route('admin.settings.permissions.edit', permission.id)"
                                        class="text-indigo-600 hover:text-indigo-900 text-sm dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="deletePermission(permission.id, permission.name)"
                                        class="text-red-600 hover:text-red-900 text-sm dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="Object.keys(groupedPermissions).length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                    Tidak ada permission ditemukan.
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
