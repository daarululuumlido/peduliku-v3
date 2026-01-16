<script setup>
import { ref, watch, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    jabatan: Object,
    unitId: String,
});

const emit = defineEmits(['close']);

const form = useForm({
    nama_jabatan: '',
    is_pimpinan: false,
    kuota_sdm: 0,
    keterangan: '',
});

const activeTab = ref('details'); // details, pegawai
const search = ref('');
const searchResults = ref([]);
const isSearching = ref(false);

const assignForm = useForm({
    peran_pegawai_id: '',
    tgl_mulai: new Date().toISOString().split('T')[0],
    no_sk: '',
    spesialisasi: '',
});

watch(() => props.jabatan, (val) => {
    if (val) {
        form.nama_jabatan = val.nama_jabatan;
        form.is_pimpinan = !!val.is_pimpinan;
        form.kuota_sdm = val.kuota_sdm;
        form.keterangan = val.keterangan || '';
    }
}, { immediate: true });

const submitDetails = () => {
    form.put(route('hris.unit-organisasi.master-jabatan.update', [props.unitId, props.jabatan.id]), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
    });
};

const searchPegawai = async () => {
    if (search.value.length < 3) {
        searchResults.value = [];
        return;
    }
    
    isSearching.value = true;
    try {
        const response = await axios.get(route('hris.pegawai.search-active', { q: search.value }));
        searchResults.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        isSearching.value = false;
    }
};

const selectPegawai = (pegawai) => {
    assignForm.peran_pegawai_id = pegawai.id;
    search.value = `${pegawai.nama} (${pegawai.nip})`;
    searchResults.value = [];
};

const submitAssign = () => {
    assignForm.post(route('hris.unit-organisasi.master-jabatan.assign', [props.unitId, props.jabatan.id]), {
        preserveScroll: true,
        onSuccess: () => {
            assignForm.reset();
            search.value = '';
            // Don't close modal, maybe user wants to add more or see list
        },
    });
};

const removePegawai = (historiId) => {
    if (!confirm('Apakah Anda yakin ingin memberhentikan pegawai ini dari jabatan?')) return;
    
    router.post(route('hris.unit-organisasi.master-jabatan.unassign', [props.unitId, props.jabatan.id, historiId]), {}, {
        preserveScroll: true,
    });
};

const activePegawai = computed(() => {
    return props.jabatan?.histori_jabatan || [];
});

const closeModal = () => {
    emit('close');
    form.reset();
    assignForm.reset();
    activeTab.value = 'details';
    search.value = '';
};
</script>

<template>
    <Modal :show="show" @close="closeModal" maxWidth="2xl">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Edit Jabatan {{ jabatan?.nama_jabatan }}
            </h3>
        </div>

        <div class="px-6 py-4">
            <!-- Tabs -->
            <div class="flex space-x-1 border-b border-gray-200 dark:border-slate-700 mb-6">
                <button 
                    @click="activeTab = 'details'"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                    :class="activeTab === 'details' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                >
                    Detail Jabatan
                </button>
                <button 
                    @click="activeTab = 'pegawai'"
                    class="px-4 py-2 text-sm font-medium border-b-2 transition-colors"
                    :class="activeTab === 'pegawai' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                >
                    Pegawai ({{ activePegawai.length }})
                </button>
            </div>

            <!-- Details Tab -->
            <div v-show="activeTab === 'details'">
                <form @submit.prevent="submitDetails" class="space-y-4">
                    <div>
                        <InputLabel for="nama_jabatan" value="Nama Jabatan" />
                        <TextInput
                            id="nama_jabatan"
                            v-model="form.nama_jabatan"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.nama_jabatan" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <InputLabel for="kuota_sdm" value="Kuota SDM" />
                            <TextInput
                                id="kuota_sdm"
                                v-model="form.kuota_sdm"
                                type="number"
                                min="0"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.kuota_sdm" class="mt-2" />
                        </div>
                        <div class="flex items-center pt-8">
                            <label class="flex items-center">
                                <Checkbox v-model:checked="form.is_pimpinan" name="is_pimpinan" />
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Posisi Pimpinan</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <InputLabel for="keterangan" value="Keterangan" />
                        <textarea
                            id="keterangan"
                            v-model="form.keterangan"
                            class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            rows="3"
                        ></textarea>
                        <InputError :message="form.errors.keterangan" class="mt-2" />
                    </div>

                    <div class="flex justify-end pt-4">
                        <SecondaryButton @click="closeModal" class="mr-3">Batal</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Simpan Perubahan</PrimaryButton>
                    </div>
                </form>
            </div>

            <!-- Pegawai Tab -->
            <div v-show="activeTab === 'pegawai'">
                <!-- Assign Form -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Tugaskan Pegawai Baru</h4>
                    <form @submit.prevent="submitAssign" class="space-y-3">
                        <div class="relative">
                            <InputLabel for="search_pegawai" value="Cari Pegawai" />
                            <TextInput
                                id="search_pegawai"
                                v-model="search"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Ketik Nama atau NIP..."
                                @input="searchPegawai"
                                autocomplete="off"
                            />
                            <div v-if="searchResults.length > 0" class="absolute z-10 w-full mt-1 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-md shadow-lg max-h-48 overflow-y-auto">
                                <div
                                    v-for="p in searchResults"
                                    :key="p.id"
                                    @click="selectPegawai(p)"
                                    class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-600 cursor-pointer text-sm"
                                >
                                    <div class="font-medium text-gray-900 dark:text-white">{{ p.nama }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">NIP: {{ p.nip }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <InputLabel for="tgl_mulai" value="Tanggal Mulai" />
                                <TextInput
                                    id="tgl_mulai"
                                    v-model="assignForm.tgl_mulai"
                                    type="date"
                                    class="mt-1 block w-full"
                                    required
                                />
                            </div>
                            <div>
                                <InputLabel for="no_sk" value="No. SK" />
                                <TextInput
                                    id="no_sk"
                                    v-model="assignForm.no_sk"
                                    type="text"
                                    class="mt-1 block w-full"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <PrimaryButton :disabled="assignForm.processing || !assignForm.peran_pegawai_id">
                                Tugaskan
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Active List -->
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Pegawai Menjabat Saat Ini</h4>
                <div v-if="activePegawai.length > 0" class="space-y-3">
                    <div 
                        v-for="history in activePegawai" 
                        :key="history.id"
                        class="flex items-center justify-between p-3 border border-gray-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800"
                    >
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-xs">
                                {{ history.peran_pegawai?.orang?.nama?.charAt(0) }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ history.peran_pegawai?.orang?.nama }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Mulai: {{ new Date(history.tgl_mulai).toLocaleDateString('id-ID') }}
                                </div>
                            </div>
                        </div>
                        <button 
                            @click="removePegawai(history.id)"
                            class="text-red-600 hover:text-red-900 text-xs font-medium"
                        >
                            Berhentikan
                        </button>
                    </div>
                </div>
                <div v-else class="text-center py-6 text-gray-500 text-sm">
                    Belum ada pegawai yang menjabat posisi ini.
                </div>
            </div>
        </div>
    </Modal>
</template>
