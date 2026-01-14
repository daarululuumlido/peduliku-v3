<script setup>
import { useThemeStore } from '@/stores/useThemeStore';

const themeStore = useThemeStore();
</script>

<template>
    <button
        class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold uppercase tracking-wide text-white shadow-md transition duration-150 ease-in-out hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-offset-2 active:brightness-95 disabled:opacity-50"
        :style="{ 
            background: `linear-gradient(135deg, ${themeStore.primaryColor}, ${themeStore.secondaryColor})`,
            '--tw-ring-color': themeStore.primaryColor
        }"
    >
        <slot />
    </button>
</template>
