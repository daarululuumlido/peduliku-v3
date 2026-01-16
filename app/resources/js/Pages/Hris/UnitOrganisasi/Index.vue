<script setup>
import { ref } from 'vue';
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    units: Array,
    struktur: Array,
    filters: Object,
});

const selectedStruktur = ref(props.filters?.struktur_id || '');

const filterByStruktur = () => {
    router.get(route('hris.unit-organisasi.index'), {
        struktur_id: selectedStruktur.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const expandedUnits = ref(new Set());

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
</script>

<template>
    <Head title="Unit Organisasi" />

    <ModuleLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Unit Organisasi
            </h2>
        </template>

        <!-- Filters -->
        <div class="mb-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Filter by Periode
                            </label>
                            <select
                                v-model="selectedStruktur"
                                @change="filterByStruktur"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            >
                                <option value="">Semua Periode</option>
                                <option v-for="str in struktur" :key="str.id" :value="str.id">
                                    {{ str.nama_periode }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Units Tree -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Hierarki Unit
                    </h3>
                </div>

                <div v-if="units && units.length > 0" class="space-y-2">
                    <template v-for="unit in units" :key="unit.id">
                        <div class="mb-2">
                            <div
                                class="flex items-center gap-2 p-3 bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 hover:shadow-sm transition-shadow cursor-pointer"
                                @click="unit.children && unit.children.length > 0 && toggleExpand(unit.id)"
                            >
                                <div v-if="unit.children && unit.children.length > 0" class="flex-shrink-0">
                                    <svg
                                        class="w-5 h-5 text-gray-500 transition-transform dark:text-gray-400"
                                        :class="{ 'rotate-90': isExpanded(unit.id) }"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </div>
                                <div v-else class="w-5"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ unit.nama_unit }}</h4>
                                        <span v-if="unit.kode_unit" class="text-xs text-gray-500 dark:text-gray-400">({{ unit.kode_unit }})</span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ unit.master_jabatan_count || 0 }} jabatan
                                        <span v-if="unit.children && unit.children.length > 0"> · {{ unit.children.length }} sub-unit</span>
                                    </div>
                                </div>
                                <Link
                                    :href="route('hris.unit-organisasi.show', unit.id)"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm"
                                    @click.stop
                                >
                                    Detail
                                </Link>
                            </div>

                            <!-- Children -->
                            <div v-if="unit.children && unit.children.length > 0 && isExpanded(unit.id)" class="mt-2 ml-6">
                                <template v-for="child in unit.children" :key="child.id">
                                    <div class="flex items-center gap-2 p-3 bg-white dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 mb-2 hover:shadow-sm transition-shadow">
                                        <div class="w-5"></div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ child.nama_unit }}</h4>
                                                <span v-if="child.kode_unit" class="text-xs text-gray-500 dark:text-gray-400">({{ child.kode_unit }})</span>
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ child.master_jabatan_count || 0 }} jabatan
                                            </div>
                                        </div>
                                        <Link
                                            :href="route('hris.unit-organisasi.show', child.id)"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm"
                                        >
                                            Detail
                                        </Link>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div v-else class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Belum ada unit organisasi.</p>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
