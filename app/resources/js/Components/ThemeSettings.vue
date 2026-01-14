<script setup>
import { ref } from 'vue';
import { useThemeStore, colorPresets, fontOptions } from '@/stores/useThemeStore';

const themeStore = useThemeStore();

const isOpen = ref(false);

function openPanel() {
    isOpen.value = true;
}

function closePanel() {
    isOpen.value = false;
}

defineExpose({ openPanel, closePanel });
</script>

<template>
    <!-- Theme Settings Button (exposed for parent to use) -->
    <Teleport to="body">
        <!-- Overlay -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div 
                v-if="isOpen" 
                class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
                @click="closePanel"
            ></div>
        </Transition>

        <!-- Slide-out Panel -->
        <Transition
            enter-active-class="transition-transform duration-300 ease-out"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition-transform duration-300 ease-in"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
        >
            <div 
                v-if="isOpen"
                class="fixed right-0 top-0 z-50 h-full w-80 bg-white shadow-2xl dark:bg-slate-800"
            >
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-slate-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Theme Settings</h2>
                    <button 
                        @click="closePanel"
                        class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-700"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="h-[calc(100%-140px)] overflow-y-auto px-6 py-6">
                    <!-- Dark Mode Toggle -->
                    <div class="mb-8">
                        <h3 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Mode</h3>
                        <div class="flex items-center justify-between rounded-lg bg-gray-100 p-3 dark:bg-slate-700">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Dark Mode</span>
                            <button
                                @click="themeStore.toggleDarkMode()"
                                :class="[
                                    'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                                    themeStore.darkMode ? 'bg-indigo-600' : 'bg-gray-200'
                                ]"
                            >
                                <span
                                    :class="[
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                        themeStore.darkMode ? 'translate-x-5' : 'translate-x-0'
                                    ]"
                                ></span>
                            </button>
                        </div>
                    </div>

                    <!-- Color Palette -->
                    <div class="mb-8">
                        <h3 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Color Palette</h3>
                        <div class="grid grid-cols-4 gap-3">
                            <button
                                v-for="(preset, key) in colorPresets"
                                :key="key"
                                @click="themeStore.setColorPreset(key)"
                                class="group relative flex h-12 flex-col items-center justify-center rounded-lg transition-all hover:scale-105"
                                :class="[
                                    themeStore.colorPresetKey === key 
                                        ? 'ring-2 ring-offset-2 ring-gray-900 dark:ring-white dark:ring-offset-slate-800' 
                                        : ''
                                ]"
                                :style="{ background: `linear-gradient(135deg, ${preset.primary}, ${preset.secondary})` }"
                            >
                                <span 
                                    class="absolute -bottom-5 text-xs text-gray-600 opacity-0 transition-opacity group-hover:opacity-100 dark:text-gray-400"
                                >
                                    {{ preset.name }}
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Font Family -->
                    <div class="mb-8">
                        <h3 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Font Family</h3>
                        <div class="space-y-2">
                            <button
                                v-for="font in fontOptions"
                                :key="font.name"
                                @click="themeStore.setFont(font.name, font.family)"
                                class="flex w-full items-center justify-between rounded-lg border px-4 py-3 transition-all"
                                :class="[
                                    themeStore.fontName === font.name
                                        ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/30'
                                        : 'border-gray-200 hover:border-gray-300 dark:border-slate-600 dark:hover:border-slate-500'
                                ]"
                                :style="{ fontFamily: font.family }"
                            >
                                <span class="text-sm text-gray-800 dark:text-gray-200">{{ font.name }}</span>
                                <svg 
                                    v-if="themeStore.fontName === font.name"
                                    class="h-5 w-5 text-indigo-600 dark:text-indigo-400" 
                                    fill="none" 
                                    viewBox="0 0 24 24" 
                                    stroke-width="2" 
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="mb-8">
                        <h3 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Preview</h3>
                        <div class="rounded-lg border border-gray-200 p-4 dark:border-slate-600">
                            <div 
                                class="mb-3 rounded-lg px-4 py-2 text-center text-white"
                                :style="{ background: `linear-gradient(135deg, ${themeStore.primaryColor}, ${themeStore.secondaryColor})` }"
                            >
                                Primary Button
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400" :style="{ fontFamily: themeStore.fontFamily }">
                                This is a preview of how your text will look with the selected font.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="absolute bottom-0 left-0 right-0 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-slate-700 dark:bg-slate-900">
                    <button
                        @click="themeStore.resetToDefaults()"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 dark:border-slate-600 dark:text-gray-300 dark:hover:bg-slate-700"
                    >
                        Reset to Defaults
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
