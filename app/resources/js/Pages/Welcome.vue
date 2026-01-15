<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome to PeduliKu" />
    
    <div class="min-h-screen bg-gray-50 text-black/80 dark:bg-zinc-900 dark:text-white/80 selection:bg-primary selection:text-white font-sans">
        <!-- Hero Section -->
        <main class="max-w-7xl mx-auto px-6 lg:px-12 py-16 lg:py-24 grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8 animate-fade-in-up">
                <div class="inline-block px-3 py-1 rounded-full bg-green-100 text-primary text-sm font-medium dark:bg-green-900/30">
                    Sistem Informasi Manajemen Terpadu
                </div>
                <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight text-zinc-900 dark:text-white">
                    <span class="text-primary">PeduliKu</span> <span class="text-secondary">v3</span>
                </h1>
                <p class="text-lg text-zinc-600 dark:text-zinc-400 max-w-lg leading-relaxed">
                    Platform digital komprehensif untuk mendukung manajemen dan operasional Pesantren Modern Daarul 'Uluum Lido. Mengelola data SDM, santri, dan administrasi dengan lebih efisien, transparan, dan terintegrasi.
                </p>
                
                <div class="flex flex-wrap gap-4 pt-4">
                     <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center px-8 py-3 text-base font-bold rounded-xl shadow-lg transition transform hover:-translate-y-1 bg-zinc-900 text-white hover:bg-zinc-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 dark:bg-white dark:text-black dark:hover:bg-zinc-200"
                    >
                        Akses Dashboard
                        <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </Link>
                    <template v-else>
                         <Link
                            :href="route('login')"
                            class="inline-flex items-center px-8 py-3 text-base font-bold rounded-xl shadow-lg transition transform hover:-translate-y-1 bg-zinc-900 text-white hover:bg-zinc-700 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-zinc-900 focus:ring-offset-2 dark:bg-white dark:text-black dark:hover:bg-zinc-200"
                        >
                            Masuk Sekarang
                            <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        </Link>
                    </template>
                </div>
            </div>

            <div class="relative lg:h-[600px] flex items-center justify-center p-8">
                <!-- Decorative Elements -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-primary/20 rounded-full blur-3xl dark:bg-primary/20"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/3 -translate-y-1/3 w-[400px] h-[400px] bg-secondary/20 rounded-full blur-3xl dark:bg-secondary/10"></div>
                
                <!-- Hero Image / Illustration Placeholder -->
                <div class="relative z-10 bg-white/80 dark:bg-zinc-800/80 backdrop-blur-xl border border-white/20 dark:border-zinc-700 p-8 rounded-3xl shadow-2xl transform rotate-3 hover:rotate-0 transition duration-500">
                     <div class="aspect-square w-full max-w-sm flex flex-col items-center justify-center text-center space-y-4">
                        <img src="/images/logo.png" alt="Logo Besar" class="w-48 h-auto drop-shadow-xl" />
                        <div>
                            <h3 class="font-bold text-2xl text-zinc-800 dark:text-gray-100">Kemandirian</h3>
                            <p class="text-zinc-500 dark:text-zinc-400">Salah satu pilar utama pesantren.</p>
                        </div>
                     </div>
                </div>
            </div>
        </main>

        <!-- Simple Feature Grid -->
        <section class="max-w-7xl mx-auto px-6 lg:px-12 py-12 border-t border-zinc-200 dark:border-zinc-800">
             <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-zinc-800 p-6 rounded-2xl shadow-sm border border-zinc-100 dark:border-zinc-700 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/50 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2 text-zinc-900 dark:text-white">Manajemen SDM</h3>
                    <p class="text-zinc-600 dark:text-zinc-400 text-sm">Pengelolaan data guru, karyawan, dan staf yang terpusat dan terstruktur.</p>
                </div>
                 <div class="bg-white dark:bg-zinc-800 p-6 rounded-2xl shadow-sm border border-zinc-100 dark:border-zinc-700 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2 text-zinc-900 dark:text-white">Pendidikan & Santri</h3>
                    <p class="text-zinc-600 dark:text-zinc-400 text-sm">Monitoring perkembangan akademik dan administrasi santri yang lebih mudah.</p>
                </div>
                 <div class="bg-white dark:bg-zinc-800 p-6 rounded-2xl shadow-sm border border-zinc-100 dark:border-zinc-700 hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-4">
                         <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2 text-zinc-900 dark:text-white">Pelaporan Data</h3>
                    <p class="text-zinc-600 dark:text-zinc-400 text-sm">Laporan real-time dan akurat untuk pengambilan keputusan yang lebih baik.</p>
                </div>
             </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 bg-white dark:bg-zinc-950 border-t border-zinc-100 dark:border-zinc-800">
             <div class="max-w-7xl mx-auto px-6 lg:px-12 text-center text-sm text-zinc-500">
                <p>&copy; 2024 Pesantren Modern Daarul 'Uluum Lido. All rights reserved.</p>
                <p class="mt-2 text-xs">Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})</p>
            </div>
        </footer>
    </div>
</template>

<style>
@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
}
</style>
