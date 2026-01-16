<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    struktur: Object,
    units: Object,
    jabatanByUnit: Object,
    pegawaiByJabatan: Object,
});

const expandedUnits = ref(new Set());
const expandedJabatan = ref(new Set());

const toggleExpand = (unitId) => {
    if (expandedUnits.value.has(unitId)) {
        expandedUnits.value.delete(unitId);
    } else {
        expandedUnits.value.add(unitId);
    }
};

const isExpanded = (unitId) => {
    return expandedUnits.value.has(unitId);
};

const toggleJabatan = (jabatanId) => {
    if (expandedJabatan.value.has(jabatanId)) {
        expandedJabatan.value.delete(jabatanId);
    } else {
        expandedJabatan.value.add(jabatanId);
    }
};

const isJabatanExpanded = (jabatanId) => {
    return expandedJabatan.value.has(jabatanId);
};

const getUnitJabatan = (unitId) => {
    return props.jabatanByUnit?.[unitId] || [];
};

const getJabatanPegawai = (unitId, jabatanId) => {
    return props.pegawaiByJabatan?.[`${unitId}_${jabatanId}`] || [];
};

const allExpanded = ref(false);

const toggleAll = () => {
    if (allExpanded.value) {
        // Collapse all
        expandedUnits.value.clear();
        expandedJabatan.value.clear();
        allExpanded.value = false;
    } else {
        // Expand all units
        props.units.forEach(unit => {
            expandedUnits.value.add(unit.id);
        });
        
        // Expand all jabatans
        if (props.jabatanByUnit) {
            Object.values(props.jabatanByUnit).forEach(jabatans => {
                jabatans.forEach(jabatan => {
                    expandedJabatan.value.add(jabatan.jabatan_id);
                });
            });
        }
        allExpanded.value = true;
    }
};

const renderUnitTree = (units, level = 0) => {
    return units.map(unit => {
        const hasChildren = unit.children && unit.children.length > 0;
        const paddingLeft = level * 24;

        return `
            <div class="mb-2">
                <div class="flex items-center gap-2 p-3 bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 hover:shadow-sm transition-shadow" style="padding-left: ${paddingLeft + 12}px">
                    ${hasChildren ? `
                        <button
                            @click="toggleExpand('${unit.id}')"
                            class="flex-shrink-0 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        >
                            <svg class="w-5 h-5 transition-transform ${isExpanded('${unit.id}') ? 'rotate-90' : ''}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    ` : '<div class="w-5"></div>'}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">${unit.nama_unit}</h4>
                            ${unit.kode_unit ? `<span class="text-xs text-gray-500 dark:text-gray-400">(${unit.kode_unit})</span>` : ''}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            ${unit.jabatan_count || 0} jabatan
                        </div>
                    </div>
                    <Link
                        :href="route('hris.unit-organisasi.show', '${unit.id}')"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm"
                    >
                        Detail
                    </Link>
                </div>
                ${hasChildren && isExpanded('${unit.id}') ? `
                    <div class="mt-2">
                        ${renderUnitTree(unit.children, level + 1)}
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');
};

const rootUnits = computed(() => {
    return props.units.filter(u => !u.parent_id);
});

const totalJabatan = computed(() => {
    return props.units.reduce((sum, unit) => sum + (unit.jabatan_count || 0), 0);
});

const totalPegawai = computed(() => {
    return props.struktur.pegawai_count || 0;
});
</script>

<template>
    <Head title="Detail Struktur Organisasi" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                    Struktur Organisasi
                </h2>
                <Link
                    :href="route('hris.struktur-organisasi.index')"
                    class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    ← Kembali
                </Link>
            </div>
        </template>

        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ struktur.nama_periode }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ new Date(struktur.tgl_mulai).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                        s/d
                        {{ new Date(struktur.tgl_selesai).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <button
                        @click="toggleAll"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-600 transition shadow-sm"
                    >
                        <svg v-if="!allExpanded" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 4.5L9 3.75M3.75 20.25v-4.5m0 4.5h4.5m-4.5-4.5L9 20.25M20.25 3.75v4.5m0-4.5h-4.5m4.5 4.5L15 3.75M20.25 20.25v-4.5m0 4.5h-4.5m4.5-4.5L15 20.25" />
                        </svg>
                        <svg v-else class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25" />
                        </svg>
                        {{ allExpanded ? 'Collapse All' : 'Expand All' }}
                    </button>

                    <Link
                        :href="route('hris.struktur-organisasi.edit', struktur.id)"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm transition btn-primary"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1-2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-2.4 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 1-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        Edit
                    </Link>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-sm dark:bg-slate-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3 dark:bg-indigo-900">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75ZM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25ZM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25Z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Unit Organisasi</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ units.length }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm dark:bg-slate-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3 dark:bg-green-900">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Jabatan</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ totalJabatan }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm dark:bg-slate-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3 dark:bg-blue-900">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pegawai Aktif</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ totalPegawai }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unit Tree -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
            <div class="p-6">
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Hierarki Unit Organisasi</h4>

                <div v-if="rootUnits.length === 0" class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Belum ada unit organisasi.</p>
                </div>

                <div v-else class="space-y-2">
                    <template v-for="unit in rootUnits" :key="unit.id">
                        <div class="mb-2">
                            <div
                                class="flex items-center gap-2 p-3 bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 hover:shadow-sm transition-shadow"
                            >
                                <button
                                    v-if="(unit.children && unit.children.length > 0) || getUnitJabatan(unit.id).length > 0"
                                    @click="toggleExpand(unit.id)"
                                    class="flex-shrink-0 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                >
                                    <svg
                                        class="w-5 h-5 transition-transform"
                                        :class="{ 'rotate-90': isExpanded(unit.id) }"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </button>
                                <div v-else class="w-5"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ unit.nama_unit }}</h4>
                                        <span v-if="unit.kode_unit" class="text-xs text-gray-500 dark:text-gray-400">({{ unit.kode_unit }})</span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ unit.jabatan_count || 0 }} jabatan
                                        <span v-if="unit.children && unit.children.length > 0">· {{ unit.children.length }} sub-unit</span>
                                        <span v-if="getUnitJabatan(unit.id).length > 0">· {{ getUnitJabatan(unit.id).length }} jabatan</span>
                                    </div>
                                </div>
                                <button
                                    v-if="(unit.children && unit.children.length > 0) || getUnitJabatan(unit.id).length > 0"
                                    @click="toggleExpand(unit.id)"
                                    class="px-3 py-1 text-sm rounded transition-colors"
                                    :class="isExpanded(unit.id) ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300'"
                                >
                                    {{ isExpanded(unit.id) ? 'Sembunyikan' : 'Lihat Detail' }}
                                </button>
                            </div>

                            <!-- Children -->
                            <div v-if="unit.children && unit.children.length > 0 && isExpanded(unit.id)" class="mt-2">
                                <template v-for="child in unit.children" :key="child.id">
                                    <div class="flex items-center gap-2 p-3 bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 ml-6 hover:shadow-sm transition-shadow">
                                        <div class="w-5"></div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ child.nama_unit }}</h4>
                                                <span v-if="child.kode_unit" class="text-xs text-gray-500 dark:text-gray-400">({{ child.kode_unit }})</span>
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ child.jabatan_count || 0 }} jabatan
                                                <span v-if="getUnitJabatan(child.id).length > 0"> · {{ getUnitJabatan(child.id).length }} jabatan</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Child's Jabatan List -->
                                    <div v-if="getUnitJabatan(child.id).length > 0" class="ml-4 md:ml-12 mt-2 space-y-2">
                                        <div v-for="jabatan in getUnitJabatan(child.id)" :key="jabatan.jabatan_id" class="space-y-2">
                                            <!-- Jabatan Item -->
                                            <div 
                                                @click="toggleJabatan(jabatan.jabatan_id)"
                                                class="flex items-center justify-between p-2 bg-gray-50 dark:bg-slate-700/50 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span v-if="isJabatanExpanded(jabatan.jabatan_id)" class="text-xs text-gray-500">▼</span>
                                                    <span v-else class="text-xs text-gray-500">▶</span>
                                                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        <span v-if="jabatan.is_pimpinan" class="mr-1" title="Pimpinan">👑</span>
                                                        {{ jabatan.nama_jabatan }}
                                                    </div>
                                                </div>
                                                <span class="text-xs bg-gray-200 dark:bg-slate-600 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-full">
                                                    {{ jabatan.pegawai_count }} org
                                                </span>
                                            </div>

                                            <!-- Employees in Jabatan -->
                                            <div v-if="isJabatanExpanded(jabatan.jabatan_id)" class="ml-2 md:ml-6 space-y-2 border-l-2 border-gray-100 dark:border-slate-700 pl-2 md:pl-3">
                                                <div v-for="emp in getJabatanPegawai(child.id, jabatan.jabatan_id)" :key="emp.pegawai_id" class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                                    <div class="flex-shrink-0 mt-1">
                                                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex flex-col md:flex-row md:items-center gap-0.5 md:gap-2">
                                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ emp.nama_lengkap }}</p>
                                                            <span v-if="emp.nip" class="text-xs text-gray-500 dark:text-gray-400">({{ emp.nip }})</span>
                                                        </div>
                                                        <p v-if="emp.tgl_mulai" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                            Sejak {{ new Date(emp.tgl_mulai).toLocaleDateString('id-ID', { year: 'numeric', month: 'short' }) }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div v-if="getJabatanPegawai(child.id, jabatan.jabatan_id).length === 0" class="text-xs text-gray-400 italic">
                                                    Belum ada pejabat definitif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Parent Unit's Jabatan List -->
                            <div v-if="isExpanded(unit.id) && getUnitJabatan(unit.id).length > 0" class="mt-2 ml-4 md:ml-6 space-y-2">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                                    <span class="px-2 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded">
                                        {{ getUnitJabatan(unit.id).length }} Jabatan
                                    </span>
                                </div>
                                
                                <div v-for="jabatan in getUnitJabatan(unit.id)" :key="jabatan.jabatan_id" class="space-y-2">
                                    <!-- Jabatan Item -->
                                    <div 
                                        @click="toggleJabatan(jabatan.jabatan_id)"
                                        class="flex items-center justify-between p-2 bg-gray-50 dark:bg-slate-700/50 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span v-if="isJabatanExpanded(jabatan.jabatan_id)" class="text-xs text-gray-500">▼</span>
                                            <span v-else class="text-xs text-gray-500">▶</span>
                                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                <span v-if="jabatan.is_pimpinan" class="mr-1" title="Pimpinan">👑</span>
                                                {{ jabatan.nama_jabatan }}
                                            </div>
                                        </div>
                                        <span class="text-xs bg-gray-200 dark:bg-slate-600 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-full">
                                            {{ jabatan.pegawai_count }} org
                                        </span>
                                    </div>

                                    <!-- Employees in Jabatan -->
                                    <div v-if="isJabatanExpanded(jabatan.jabatan_id)" class="ml-2 md:ml-6 space-y-2 border-l-2 border-gray-100 dark:border-slate-700 pl-2 md:pl-3">
                                        <div v-for="emp in getJabatanPegawai(unit.id, jabatan.jabatan_id)" :key="emp.pegawai_id" class="flex items-start gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700/30">
                                            <div class="flex-shrink-0 mt-1">
                                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex flex-col md:flex-row md:items-center gap-0.5 md:gap-2">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ emp.nama_lengkap }}</p>
                                                    <span v-if="emp.nip" class="text-xs text-gray-500 dark:text-gray-400">({{ emp.nip }})</span>
                                                </div>
                                                <p v-if="emp.tgl_mulai" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                    Sejak {{ new Date(emp.tgl_mulai).toLocaleDateString('id-ID', { year: 'numeric', month: 'short' }) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div v-if="getJabatanPegawai(unit.id, jabatan.jabatan_id).length === 0" class="text-xs text-gray-400 italic">
                                            Belum ada pejabat definitif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
