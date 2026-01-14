<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import SearchableSelect from './SearchableSelect.vue';

const props = defineProps({
    desaId: {
        type: String,
        default: '',
    },
    alamatLengkap: {
        type: String,
        default: '',
    },
    initialData: {
        type: Object,
        default: null,
    },
    error: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:desaId', 'update:alamatLengkap']);

const provinces = ref([]);
const cities = ref([]);
const districts = ref([]);
const villages = ref([]);

const selectedProvince = ref('');
const selectedCity = ref('');
const selectedDistrict = ref('');
const selectedVillage = ref('');
const alamat = ref(props.alamatLengkap);

const loading = ref({
    provinces: false,
    cities: false,
    districts: false,
    villages: false,
});

// Load provinces on mount
onMounted(async () => {
    await loadProvinces();

    // If we have initial data, populate the dropdowns
    if (props.initialData?.desa) {
        const desa = props.initialData.desa;
        selectedProvince.value = desa.district.city.province.code;
        await loadCities(selectedProvince.value);
        selectedCity.value = desa.district.city.code;
        await loadDistricts(selectedCity.value);
        selectedDistrict.value = desa.district.code;
        await loadVillages(selectedDistrict.value);
        selectedVillage.value = desa.code;
    }
});

const loadProvinces = async () => {
    loading.value.provinces = true;
    try {
        const response = await axios.get('/api/wilayah/provinsi');
        provinces.value = response.data;
    } catch (error) {
        console.error('Failed to load provinces:', error);
    } finally {
        loading.value.provinces = false;
    }
};

const loadCities = async (provinceCode) => {
    if (!provinceCode) {
        cities.value = [];
        return;
    }
    loading.value.cities = true;
    try {
        const response = await axios.get(`/api/wilayah/kota/${provinceCode}`);
        cities.value = response.data;
    } catch (error) {
        console.error('Failed to load cities:', error);
    } finally {
        loading.value.cities = false;
    }
};

const loadDistricts = async (cityCode) => {
    if (!cityCode) {
        districts.value = [];
        return;
    }
    loading.value.districts = true;
    try {
        const response = await axios.get(`/api/wilayah/kecamatan/${cityCode}`);
        districts.value = response.data;
    } catch (error) {
        console.error('Failed to load districts:', error);
    } finally {
        loading.value.districts = false;
    }
};

const loadVillages = async (districtCode) => {
    if (!districtCode) {
        villages.value = [];
        return;
    }
    loading.value.villages = true;
    try {
        const response = await axios.get(`/api/wilayah/desa/${districtCode}`);
        villages.value = response.data;
    } catch (error) {
        console.error('Failed to load villages:', error);
    } finally {
        loading.value.villages = false;
    }
};

// Watch for province changes
watch(selectedProvince, async (newVal, oldVal) => {
    if (newVal !== oldVal) {
        selectedCity.value = '';
        selectedDistrict.value = '';
        selectedVillage.value = '';
        cities.value = [];
        districts.value = [];
        villages.value = [];
        if (newVal) {
            await loadCities(newVal);
        }
    }
});

// Watch for city changes
watch(selectedCity, async (newVal, oldVal) => {
    if (newVal !== oldVal) {
        selectedDistrict.value = '';
        selectedVillage.value = '';
        districts.value = [];
        villages.value = [];
        if (newVal) {
            await loadDistricts(newVal);
        }
    }
});

// Watch for district changes
watch(selectedDistrict, async (newVal, oldVal) => {
    if (newVal !== oldVal) {
        selectedVillage.value = '';
        villages.value = [];
        if (newVal) {
            await loadVillages(newVal);
        }
    }
});

// Emit village selection
watch(selectedVillage, (newVal) => {
    emit('update:desaId', newVal);
});

// Emit alamat changes
watch(alamat, (newVal) => {
    emit('update:alamatLengkap', newVal);
});
</script>

<template>
    <div class="space-y-4">
        <h3 class="text-lg font-medium text-gray-900">Alamat KTP</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Province -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Provinsi
                </label>
                <SearchableSelect
                    v-model="selectedProvince"
                    :options="provinces"
                    :loading="loading.provinces"
                    placeholder="Ketik untuk mencari provinsi..."
                />
            </div>

            <!-- City -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kota/Kabupaten
                </label>
                <SearchableSelect
                    v-model="selectedCity"
                    :options="cities"
                    :loading="loading.cities"
                    :disabled="!selectedProvince"
                    placeholder="Ketik untuk mencari kota..."
                />
            </div>

            <!-- District -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kecamatan
                </label>
                <SearchableSelect
                    v-model="selectedDistrict"
                    :options="districts"
                    :loading="loading.districts"
                    :disabled="!selectedCity"
                    placeholder="Ketik untuk mencari kecamatan..."
                />
            </div>

            <!-- Village -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Desa/Kelurahan
                </label>
                <SearchableSelect
                    v-model="selectedVillage"
                    :options="villages"
                    :loading="loading.villages"
                    :disabled="!selectedDistrict"
                    :error="error"
                    placeholder="Ketik untuk mencari desa..."
                />
            </div>
        </div>

        <!-- Detail Alamat -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Detail Alamat (RT/RW, Jalan, No. Rumah)
            </label>
            <textarea
                v-model="alamat"
                rows="2"
                placeholder="Contoh: RT 01 RW 02, Jl. Merdeka No. 123"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            ></textarea>
        </div>
    </div>
</template>
