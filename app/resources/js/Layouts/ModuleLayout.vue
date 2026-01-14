<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ModuleSwitcher from '@/Components/ModuleSwitcher.vue';
import SidebarMenu from '@/Components/SidebarMenu.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();
const sidebarOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Mobile sidebar overlay -->
        <div 
            v-if="sidebarOpen" 
            class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Sidebar Header with Module Switcher -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 bg-gray-50">
                <ModuleSwitcher />
            </div>

            <!-- Sidebar Menu -->
            <SidebarMenu />

            <!-- Sidebar Footer -->
            <div class="flex items-center px-4 py-3 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center flex-1 min-w-0">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-medium text-sm">
                            {{ $page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                        </div>
                    </div>
                    <div class="ml-3 min-w-0">
                        <p class="text-sm font-medium text-gray-700 truncate">
                            {{ $page.props.auth.user?.name }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ $page.props.auth.user?.email }}
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:pl-64">
            <!-- Top Navbar -->
            <header class="sticky top-0 z-30 flex items-center h-16 px-4 bg-white border-b border-gray-200 shadow-sm sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button
                    @click="sidebarOpen = true"
                    class="p-2 -ml-2 text-gray-500 rounded-md lg:hidden hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Page Title -->
                <div class="flex-1 ml-4 lg:ml-0">
                    <slot name="header" />
                </div>

                <!-- Right side actions -->
                <div class="flex items-center space-x-4">
                    <!-- User dropdown -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition">
                                <span class="hidden sm:inline mr-2">{{ $page.props.auth.user?.name }}</span>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-medium text-sm">
                                    {{ $page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                                </div>
                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                        <p class="ml-3 text-sm font-medium text-green-800">{{ $page.props.flash.message }}</p>
                    </div>
                </div>
            </div>

            <div v-if="$page.props.flash?.error" class="mx-4 mt-4 sm:mx-6 lg:mx-8">
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                        <p class="ml-3 text-sm font-medium text-red-800">{{ $page.props.flash.error }}</p>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
