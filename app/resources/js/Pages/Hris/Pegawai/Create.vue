<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    orang_id: '',
    nip: '',
    tgl_bergabung: '',
    tmt: '',
    status_kepegawaian: 'Guru Tetap',
    status_mukim: 'TIDAK',
    is_pengajar: true,
    email_internal: '',
    no_rekening: '',
    nuptk: '',
});

const submit = () => {
    form.post(route('hris.pegawai.store'));
};
</script>

<template>
    <Head title="Tambah Pegawai" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('hris.pegawai.index')"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    ← Kembali
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                    Tambah Pegawai Baru
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-slate-800">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Orang ID & NIP -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Orang (Orang ID) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    v-model="form.orang_id"
                                    placeholder="UUID orang"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white font-mono text-sm"
                                    :class="{ 'border-red-500': form.errors.orang_id }"
                                />
                                <p v-if="form.errors.orang_id" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.orang_id }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Gunakan halaman pencarian untuk mencari orang
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    NIP <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    v-model="form.nip"
                                    placeholder="Nomor Induk Pegawai"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white font-mono"
                                    :class="{ 'border-red-500': form.errors.nip }"
                                />
                                <p v-if="form.errors.nip" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.nip }}
                                </p>
                            </div>
                        </div>

                        <!-- Tanggal Bergabung & TMT -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tanggal Bergabung <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    v-model="form.tgl_bergabung"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.tgl_bergabung }"
                                />
                                <p v-if="form.errors.tgl_bergabung" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.tgl_bergabung }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    TMT (Terhitung Mulai Tugas)
                                </label>
                                <input
                                    type="date"
                                    v-model="form.tmt"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.tmt }"
                                />
                                <p v-if="form.errors.tmt" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.tmt }}
                                </p>
                            </div>
                        </div>

                        <!-- Status Kepegawaian & Status Mukim -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Status Kepegawaian <span class="text-red-500">*</span>
                                </label>
                                <select
                                    v-model="form.status_kepegawaian"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.status_kepegawaian }"
                                >
                                    <option value="Guru Tetap">Guru Tetap</option>
                                    <option value="Guru Kontrak">Guru Kontrak</option>
                                    <option value="Karyawan Tetap">Karyawan Tetap</option>
                                    <option value="Karyawan Kontrak">Karyawan Kontrak</option>
                                    <option value="Honorer">Honorer</option>
                                </select>
                                <p v-if="form.errors.status_kepegawaian" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.status_kepegawaian }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Status Mukim
                                </label>
                                <select
                                    v-model="form.status_mukim"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                >
                                    <option value="TIDAK">Tidak Mukim</option>
                                    <option value="MUKIM">Mukim</option>
                                </select>
                                <p v-if="form.errors.status_mukim" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.status_mukim }}
                                </p>
                            </div>
                        </div>

                        <!-- Pengajar -->
                        <div>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.is_pengajar"
                                    class="form-checkbox h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Pengajar</span>
                            </label>
                        </div>

                        <!-- Email Internal & No Rekening -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Email Internal
                                </label>
                                <input
                                    type="email"
                                    v-model="form.email_internal"
                                    placeholder="email@pesantren.id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.email_internal }"
                                />
                                <p v-if="form.errors.email_internal" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.email_internal }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    No Rekening
                                </label>
                                <input
                                    type="text"
                                    v-model="form.no_rekening"
                                    placeholder="Nomor rekening"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white font-mono"
                                    :class="{ 'border-red-500': form.errors.no_rekening }"
                                />
                                <p v-if="form.errors.no_rekening" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.no_rekening }}
                                </p>
                            </div>
                        </div>

                        <!-- NUPTK -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                NUPTK
                            </label>
                            <input
                                type="text"
                                v-model="form.nuptk"
                                placeholder="Nomor Unik Pendidik dan Tenaga Kependidikan"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white font-mono"
                                :class="{ 'border-red-500': form.errors.nuptk }"
                            />
                            <p v-if="form.errors.nuptk" class="mt-1 text-sm text-red-600">
                                {{ form.errors.nuptk }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-slate-700">
                            <Link
                                :href="route('hris.pegawai.index')"
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-slate-600 dark:text-gray-200 dark:hover:bg-slate-700"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
