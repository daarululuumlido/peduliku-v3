<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ModuleSwitcher from '@/Components/ModuleSwitcher.vue';
import SidebarMenu from '@/Components/SidebarMenu.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ThemeSettings from '@/Components/ThemeSettings.vue';
import { useThemeStore } from '@/stores/useThemeStore';

const page = usePage();
const themeStore = useThemeStore();
const sidebarOpen = ref(false);
const themeSettingsRef = ref(null);

function openThemeSettings() {
    themeSettingsRef.value?.openPanel();
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900">
        <!-- Mobile sidebar overlay -->
        <div 
            v-if="sidebarOpen" 
            class="fixed inset-0 z-40 bg-gray-900/75 backdrop-blur-sm lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <!-- Sidebar -->
        <aside
            id="aside"
            class="aside fixed inset-y-0 left-0 z-50 flex w-64 flex-col transform transition-all duration-300 ease-in-out lg:translate-x-0 lg:py-2 lg:pl-2"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="aside flex flex-1 flex-col overflow-hidden lg:rounded-2xl">
                <!-- Sidebar Header with Module Switcher -->
                <div class="aside-brand flex h-14 items-center justify-between px-4">
                    <ModuleSwitcher />
                    <!-- Close button for mobile -->
                    <button 
                        @click="sidebarOpen = false"
                        class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-200 lg:hidden dark:text-slate-400 dark:hover:bg-slate-700"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Sidebar Menu -->
                <SidebarMenu />

                <!-- Sidebar Footer -->
                <div class="aside-brand flex items-center px-4 py-3">
                    <div class="flex min-w-0 flex-1 items-center">
                        <div class="flex-shrink-0">
                            <div 
                                class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-medium text-white"
                                :style="{ background: `linear-gradient(135deg, ${themeStore.primaryColor}, ${themeStore.secondaryColor})` }"
                            >
                                {{ $page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                            </div>
                        </div>
                        <div class="ml-3 min-w-0">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                {{ $page.props.auth.user?.name }}
                            </p>
                            <p class="truncate text-xs text-gray-500 dark:text-slate-400">
                                {{ $page.props.auth.user?.email }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:pl-64 lg:pt-2">
            <!-- Top Navbar -->
            <header class="sticky top-0 z-30 flex h-14 items-center bg-white/80 px-4 shadow-sm backdrop-blur-md transition-all sm:px-6 lg:mx-2 lg:rounded-t-2xl lg:px-8 dark:bg-slate-800/80">
                <!-- Mobile menu button -->
                <button
                    @click="sidebarOpen = true"
                    class="-ml-2 rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-inset lg:hidden dark:text-gray-400 dark:hover:bg-slate-700"
                    :style="{ '--tw-ring-color': themeStore.primaryColor }"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Page Title -->
                <div class="ml-4 flex-1 lg:ml-0">
                    <slot name="header" />
                </div>

                <!-- Right side actions -->
                <div class="flex items-center space-x-2">
                    <!-- Theme toggle button -->
                    <button
                        @click="themeStore.toggleDarkMode()"
                        class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-700"
                        :title="themeStore.darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                    >
                        <!-- Sun icon (show in dark mode) -->
                        <svg v-if="themeStore.darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                        <!-- Moon icon (show in light mode) -->
                        <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                    </button>

                    <!-- Theme settings button -->
                    <button
                        @click="openThemeSettings"
                        class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-700"
                        title="Theme Settings"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42" />
                        </svg>
                    </button>

                    <!-- User dropdown -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center text-sm font-medium text-gray-500 transition hover:text-gray-700 focus:outline-none dark:text-gray-400 dark:hover:text-gray-200">
                                <span class="mr-2 hidden sm:inline">{{ $page.props.auth.user?.name }}</span>
                                <div 
                                    class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-medium text-white"
                                    :style="{ background: `linear-gradient(135deg, ${themeStore.primaryColor}, ${themeStore.secondaryColor})` }"
                                >
                                    {{ $page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                                </div>
                                <svg class="-mr-0.5 ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('profile.edit')">
                                Profile
                            </DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Flash Messages -->
            <div v-if="$page.props.flash?.message" class="mx-4 mt-4 sm:mx-6 lg:mx-8">
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/30">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                        <p class="ml-3 text-sm font-medium text-green-800 dark:text-green-200">{{ $page.props.flash.message }}</p>
                    </div>
                </div>
            </div>

            <div v-if="$page.props.flash?.error" class="mx-4 mt-4 sm:mx-6 lg:mx-8">
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/30">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                        <p class="ml-3 text-sm font-medium text-red-800 dark:text-red-200">{{ $page.props.flash.error }}</p>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>

        <!-- Theme Settings Panel -->
        <ThemeSettings ref="themeSettingsRef" />
    </div>
</template>
