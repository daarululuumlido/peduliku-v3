<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

const modules = computed(() => page.props.modules || {});
const currentModule = computed(() => page.props.currentModule);

const currentModuleData = computed(() => {
    if (!currentModule.value || !modules.value[currentModule.value]) {
        return null;
    }
    return modules.value[currentModule.value];
});

const otherModules = computed(() => {
    return Object.values(modules.value).filter(m => m.slug !== currentModule.value);
});

const moduleIcons = {
    'cog-6-tooth': `<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />`,
    'academic-cap': `<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />`,
    'currency-dollar': `<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />`,
    'users': `<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />`,
};

function getIcon(iconName) {
    return moduleIcons[iconName] || moduleIcons['cog-6-tooth'];
}
</script>

<template>
    <div class="relative" v-if="currentModuleData">
        <div class="group">
            <button
                class="flex items-center w-full px-3 py-2 text-left bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg"
            >
                <div class="flex items-center justify-center w-8 h-8 bg-white/20 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" v-html="getIcon(currentModuleData.icon)"></svg>
                </div>
                <span class="font-semibold text-white">{{ currentModuleData.name }}</span>
                <svg v-if="otherModules.length > 0" class="w-4 h-4 ml-auto text-white/70 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                </svg>
            </button>

            <!-- Dropdown -->
            <div v-if="otherModules.length > 0" class="absolute left-0 right-0 mt-2 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                <div class="p-2">
                    <div class="text-xs font-medium text-gray-400 uppercase tracking-wider px-2 py-1 mb-1">
                        Pindah Modul
                    </div>
                    <Link
                        v-for="mod in otherModules"
                        :key="mod.slug"
                        :href="route(mod.route)"
                        class="flex items-center px-2 py-2 rounded-lg hover:bg-gray-100 transition-colors"
                    >
                        <div class="flex items-center justify-center w-8 h-8 bg-indigo-100 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" v-html="getIcon(mod.icon)"></svg>
                        </div>
                        <span class="font-medium text-gray-700">{{ mod.name }}</span>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
