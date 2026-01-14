<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <Head title="Dashboard - Admin" />

    <ModuleLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Dashboard</h2>
        </template>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-8">
            <!-- Total Orang Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Total Orang</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.totalOrang ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
                    <Link :href="route('admin.orang.index')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Lihat semua →
                    </Link>
                </div>
            </div>

            <!-- Total KK Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Kartu Keluarga</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.totalKK ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
                    <Link :href="route('admin.kartu-keluarga.index')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Lihat semua →
                    </Link>
                </div>
            </div>

            <!-- Total Users Card (Super Admin only) -->
            <div v-if="stats.totalUsers !== undefined" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </div>
                        <div class="ml-5">
                            <p class="text-sm font-medium text-gray-500">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.totalUsers ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
                    <Link :href="route('admin.settings.users.index')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Kelola users →
                    </Link>
                </div>
            </div>
        </div>

        <!-- Recent Orang (if available) -->
        <div v-if="stats.recentOrang && stats.recentOrang.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Data Orang Terbaru</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <Link
                    v-for="orang in stats.recentOrang"
                    :key="orang.id"
                    :href="route('admin.orang.show', orang.id)"
                    class="flex items-center px-6 py-4 hover:bg-gray-50 transition-colors"
                >
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                            <span class="text-sm font-medium text-indigo-600">{{ orang.nama?.charAt(0)?.toUpperCase() }}</span>
                        </div>
                    </div>
                    <div class="ml-4 flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ orang.nama }}</p>
                        <p class="text-sm text-gray-500">{{ orang.nik }}</p>
                    </div>
                    <div class="text-sm text-gray-400">
                        {{ new Date(orang.created_at).toLocaleDateString('id-ID') }}
                    </div>
                </Link>
            </div>
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100">
                <Link :href="route('admin.orang.index')" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Lihat semua data →
                </Link>
            </div>
        </div>

        <!-- Welcome message for viewers -->
        <div v-if="!stats.recentOrang" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Selamat Datang di PeduliKu</h3>
            <p class="text-gray-500">Gunakan menu di samping untuk mengakses fitur yang tersedia.</p>
        </div>
    </ModuleLayout>
</template>
