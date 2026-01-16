<script setup>
import { ref, computed } from 'vue';
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import { Head, Link, useForm as useInertiaForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    pegawai: Object,
});

const tabs = [
    { id: 'profil', name: 'Profil', icon: 'user' },
    { id: 'jabatan', name: 'Riwayat Jabatan', icon: 'briefcase' },
    { id: 'pendidikan', name: 'Pendidikan', icon: 'academic-cap' },
    { id: 'keluarga', name: 'Keluarga', icon: 'users' },
];

const activeTab = ref('profil');

// Pendidikan Logic
const showPendidikanModal = ref(false);
const editingPendidikan = ref(null);
const jenjangOptions = ['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3', 'Lainnya'];

const jenjangOrder = {
    'SD': 1,
    'SMP': 2,
    'SMA': 3,
    'D3': 4,
    'S1': 5,
    'S2': 6,
    'S3': 7,
    'Lainnya': 8
};

const sortedPendidikan = computed(() => {
    if (!props.pegawai.orang.riwayat_pendidikan) return [];
    
    // Create a shallow copy to avoid mutating the prop
    return [...props.pegawai.orang.riwayat_pendidikan].sort((a, b) => {
        const orderA = jenjangOrder[a.jenjang] || 99;
        const orderB = jenjangOrder[b.jenjang] || 99;
        return orderA - orderB;
    });
});

const pendidikanForm = useInertiaForm({
    jenjang: '',
    institusi: '',
    jurusan: '',
    tahun_masuk: '',
    tahun_lulus: '',
    nilai_akhir: '',
    no_ijazah: '',
});

const openPendidikanModal = (pendidikan = null) => {
    editingPendidikan.value = pendidikan;
    if (pendidikan) {
        pendidikanForm.jenjang = pendidikan.jenjang;
        pendidikanForm.institusi = pendidikan.institusi;
        pendidikanForm.jurusan = pendidikan.jurusan;
        pendidikanForm.tahun_masuk = pendidikan.tahun_masuk;
        pendidikanForm.tahun_lulus = pendidikan.tahun_lulus;
        pendidikanForm.nilai_akhir = pendidikan.nilai_akhir;
        pendidikanForm.no_ijazah = pendidikan.no_ijazah;
    } else {
        pendidikanForm.reset();
    }
    showPendidikanModal.value = true;
};

const closePendidikanModal = () => {
    showPendidikanModal.value = false;
    pendidikanForm.reset();
    editingPendidikan.value = null;
};

const submitPendidikan = () => {
    if (editingPendidikan.value) {
        pendidikanForm.put(route('hris.pendidikan.update', [props.pegawai.id, editingPendidikan.value.id]), {
            onSuccess: () => closePendidikanModal(),
        });
    } else {
        pendidikanForm.post(route('hris.pendidikan.store', props.pegawai.id), {
            onSuccess: () => closePendidikanModal(),
        });
    }
};

const deletePendidikan = (pendidikan) => {
    if (confirm('Apakah Anda yakin ingin menghapus data pendidikan ini?')) {
        useInertiaForm({}).delete(route('hris.pendidikan.destroy', [props.pegawai.id, pendidikan.id]));
    }
};

const getStatusColor = (status) => {
    const colors = {
        'Guru Tetap': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'Guru Kontrak': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'Karyawan Tetap': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'Karyawan Kontrak': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
        'Honorer': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getGenderColor = (gender) => {
    return gender === 'L'
        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
        : 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300';
};

// Format status_hubungan for display (e.g., "kepala_keluarga" -> "Kepala Keluarga")
const formatStatusHubungan = (status) => {
    if (!status) return '';
    return status
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
        .join(' ');
};

const familyRoleOrder = {
    'kepala_keluarga': 1,
    'suami': 2,
    'istri': 3,
    'anak': 4,
    'menantu': 5,
    'cucu': 6,
    'orang_tua': 7,
    'mertua': 8,
    'famili_lain': 9,
};

const sortFamilyMembers = (members) => {
    if (!members) return [];
    return [...members].sort((a, b) => {
        const orderA = familyRoleOrder[a.status_hubungan] || 99;
        const orderB = familyRoleOrder[b.status_hubungan] || 99;
        
        if (orderA !== orderB) {
            return orderA - orderB;
        }
        
        // Secondary sort by usage? or maybe birthdate?
        // Let's keep it simple for now, maybe name or birthdate if needed
        return 0; 
    });
};

</script>

<template>
    <Head title="Detail Pegawai" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                    Profil Pegawai
                </h2>
                <Link
                    :href="route('hris.pegawai.index')"
                    class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    ← Kembali
                </Link>
            </div>
        </template>

        <!-- Page Header -->
        <div class="mb-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 h-20 w-20 bg-indigo-100 rounded-full flex items-center justify-center dark:bg-indigo-900">
                                <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ pegawai.orang.nama.charAt(0) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    {{ pegawai.orang.nama_gelar }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    NIP: {{ pegawai.nip }}
                                </p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                                        :class="getStatusColor(pegawai.status_kepegawaian)"
                                    >
                                        {{ pegawai.status_kepegawaian }}
                                    </span>
                                    <span
                                        class="inline-flex px-1.5 py-0.5 text-xs font-medium rounded-full"
                                        :class="getGenderColor(pegawai.orang.gender)"
                                    >
                                        {{ pegawai.orang.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <Link
                                :href="route('hris.pegawai.edit', pegawai.id)"
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
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <div class="border-b border-gray-200 dark:border-slate-700">
                <nav class="-mb-px flex space-x-8">
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                        :class="{
                            'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === tab.id,
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== tab.id
                        }"
                    >
                        {{ tab.name }}
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="space-y-6">
            <!-- Profil Tab -->
            <div v-show="activeTab === 'profil'" class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
                <div class="p-6">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Pribadi</h4>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">NIK</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ pegawai.orang.nik }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tempat Lahir</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ pegawai.orang.tempat_lahir || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Lahir</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ pegawai.orang.tanggal_lahir ? new Date(pegawai.orang.tanggal_lahir).toLocaleDateString('id-ID') : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">No WhatsApp</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ pegawai.orang.no_whatsapp || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ pegawai.email_internal || pegawai.orang.email || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Alamat Domisili</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ pegawai.alamatDomisili ? pegawai.alamatDomisili.full_address : '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="border-t border-gray-200 dark:border-slate-700 p-6">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Kepegawaian</h4>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Bergabung</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ new Date(pegawai.tgl_bergabung).toLocaleDateString('id-ID') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">TMT</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ new Date(pegawai.tmt).toLocaleDateString('id-ID') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Kepegawaian</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ pegawai.status_kepegawaian }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Mukim</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ pegawai.status_mukim }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Pengajar</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ pegawai.is_pengajar ? 'Ya' : 'Tidak' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">NUPTK</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ pegawai.nuptk || '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Riwayat Jabatan Tab -->
            <div v-show="activeTab === 'jabatan'" class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">Riwayat Jabatan</h4>
                        <Link
                            :href="route('hris.pegawai.assign-jabatan', pegawai.id)"
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm transition btn-primary"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tugas Jabatan
                        </Link>
                    </div>

                    <div v-if="pegawai.histori_jabatan && pegawai.histori_jabatan.length > 0" class="space-y-4">
                        <div
                            v-for="(histori, index) in pegawai.histori_jabatan"
                            :key="histori.id"
                            class="p-4 border border-gray-200 rounded-lg dark:border-slate-700"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-indigo-100 text-indigo-600 text-xs font-medium dark:bg-indigo-900 dark:text-indigo-400">
                                            {{ index + 1 }}
                                        </span>
                                        <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ histori.master_jabatan.nama_jabatan }}
                                        </h5>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ histori.master_jabatan.unit_organisasi.nama_unit }}
                                    </p>
                                    <div class="mt-2 flex flex-wrap gap-4 text-xs text-gray-500 dark:text-gray-400">
                                        <span>
                                            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                            </svg>
                                            {{ new Date(histori.tgl_mulai).toLocaleDateString('id-ID') }}
                                        </span>
                                        <span v-if="histori.tgl_selesai">
                                            s/d {{ new Date(histori.tgl_selesai).toLocaleDateString('id-ID') }}
                                        </span>
                                        <span v-else class="text-green-600 dark:text-green-400">
                                            ● Aktif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">Belum ada riwayat jabatan.</p>
                    </div>
                </div>
            </div>

            <!-- Pendidikan Tab -->
            <div v-show="activeTab === 'pendidikan'" class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">Riwayat Pendidikan</h4>
                        <button
                            @click="openPendidikanModal()"
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm transition btn-primary"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Pendidikan
                        </button>
                    </div>

                    <div v-if="sortedPendidikan && sortedPendidikan.length > 0" class="space-y-4">
                        <div
                            v-for="(edu, index) in sortedPendidikan"
                            :key="edu.id"
                            class="p-4 border border-gray-200 rounded-lg dark:border-slate-700 relative group"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center h-6 w-min px-2 rounded-full bg-blue-100 text-blue-600 text-xs font-bold dark:bg-blue-900 dark:text-blue-300">
                                            {{ edu.jenjang }}
                                        </span>
                                        <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ edu.institusi }}
                                        </h5>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ edu.jurusan || '-' }}
                                    </p>
                                    <div class="mt-2 flex flex-wrap gap-4 text-xs text-gray-500 dark:text-gray-400">
                                        <span>
                                            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                            </svg>
                                            {{ edu.tahun_masuk }} - {{ edu.tahun_lulus || 'Sekarang' }}
                                        </span>
                                        <span v-if="edu.nilai_akhir">
                                            Nilai Akhir: {{ edu.nilai_akhir }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button
                                        @click="editPendidikan(edu)"
                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                        title="Edit"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1-2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-2.4 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 1-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="deletePendidikan(edu)"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        title="Hapus"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 0 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.26 10.147a60.436 60.436 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">Belum ada data pendidikan.</p>
                        <button
                            @click="openPendidikanModal()"
                            class="mt-4 text-indigo-600 hover:text-indigo-900 font-medium text-sm"
                        >
                            + Tambah Pendidikan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Pendidikan -->
            <Modal :show="showPendidikanModal" @close="closePendidikanModal">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ editingPendidikan ? 'Edit Pendidikan' : 'Tambah Pendidikan' }}
                    </h2>
                    
                    <form @submit.prevent="submitPendidikan">
                        <div class="space-y-4">
                            <div>
                                <InputLabel for="jenjang" value="Jenjang" />
                                <SelectInput
                                    id="jenjang"
                                    v-model="pendidikanForm.jenjang"
                                    class="mt-1 block w-full"
                                    required
                                >
                                    <option value="" disabled>Pilih Jenjang</option>
                                    <option v-for="opt in jenjangOptions" :key="opt" :value="opt">{{ opt }}</option>
                                </SelectInput>
                                <InputError :message="pendidikanForm.errors.jenjang" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="institusi" value="Nama Institusi" />
                                <TextInput
                                    id="institusi"
                                    v-model="pendidikanForm.institusi"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="pendidikanForm.errors.institusi" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="jurusan" value="Jurusan" />
                                <TextInput
                                    id="jurusan"
                                    v-model="pendidikanForm.jurusan"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Contoh: Teknik Informatika"
                                />
                                <InputError :message="pendidikanForm.errors.jurusan" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="tahun_masuk" value="Tahun Masuk" />
                                    <TextInput
                                        id="tahun_masuk"
                                        v-model="pendidikanForm.tahun_masuk"
                                        type="number"
                                        class="mt-1 block w-full"
                                        required
                                        placeholder="Tahun"
                                    />
                                    <InputError :message="pendidikanForm.errors.tahun_masuk" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="tahun_lulus" value="Tahun Lulus" />
                                    <TextInput
                                        id="tahun_lulus"
                                        v-model="pendidikanForm.tahun_lulus"
                                        type="number"
                                        class="mt-1 block w-full"
                                        placeholder="Tahun"
                                    />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Kosongkan jika masih menempuh pendidikan
                                    </p>
                                    <InputError :message="pendidikanForm.errors.tahun_lulus" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="nilai_akhir" value="Nilai Akhir" />
                                    <TextInput
                                        id="nilai_akhir"
                                        v-model="pendidikanForm.nilai_akhir"
                                        type="number"
                                        step="0.01"
                                        class="mt-1 block w-full"
                                        placeholder="IPK / Nilai"
                                    />
                                    <InputError :message="pendidikanForm.errors.nilai_akhir" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="no_ijazah" value="No Ijazah" />
                                    <TextInput
                                        id="no_ijazah"
                                        v-model="pendidikanForm.no_ijazah"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="Nomor Ijazah"
                                    />
                                    <InputError :message="pendidikanForm.errors.no_ijazah" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <SecondaryButton @click="closePendidikanModal">
                                Batal
                            </SecondaryButton>
                            <PrimaryButton :disabled="pendidikanForm.processing">
                                {{ editingPendidikan ? 'Simpan Perubahan' : 'Simpan' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>

            <!-- Keluarga Tab -->
            <div v-show="activeTab === 'keluarga'" class="bg-white overflow-hidden shadow-sm rounded-xl dark:bg-slate-800">
                <div class="p-6">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Data Keluarga</h4>
                    
                    <div v-if="pegawai.orang.kartu_keluarga_anggota && pegawai.orang.kartu_keluarga_anggota.length > 0">
                        <div v-for="kkMembership in pegawai.orang.kartu_keluarga_anggota" :key="kkMembership.id" class="mb-8 last:mb-0">
                            <div class="bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nomor Kartu Keluarga</p>
                                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ kkMembership.kartu_keluarga.no_kk }}</p>
                                    </div>
                                    <!-- Add address if available in KK relation -->
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                                    <thead class="bg-gray-50 dark:bg-slate-700">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Lengkap</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Hubungan</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">L/P</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Umur</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-slate-800 dark:divide-slate-700">
                                        <tr 
                                            v-for="anggota in sortFamilyMembers(kkMembership.kartu_keluarga.anggota)" 
                                            :key="anggota.id"
                                            :class="{'bg-indigo-50 dark:bg-indigo-900/20': anggota.orang_id === pegawai.orang.id}"
                                        >
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                        {{ anggota.orang.nama.charAt(0) }}
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ anggota.orang.nama_gelar || anggota.orang.nama }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            NIK: {{ anggota.orang.nik }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ formatStatusHubungan(anggota.status_hubungan) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ anggota.orang.gender }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <div v-if="anggota.orang.tanggal_lahir">
                                                    {{ new Date().getFullYear() - new Date(anggota.orang.tanggal_lahir).getFullYear() }} Tahun
                                                </div>
                                                <div v-else>-</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">Belum ada data keluarga.</p>
                    </div>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
