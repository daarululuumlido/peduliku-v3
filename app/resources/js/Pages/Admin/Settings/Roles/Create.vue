<script setup>
import ModuleLayout from '@/Layouts/ModuleLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    permissions: Array,
});

const form = useForm({
    name: '',
    permissions: [],
});

// Group permissions by module
const groupedPermissions = computed(() => {
    const groups = {};
    props.permissions.forEach(permission => {
        const [module] = permission.name.split('.');
        if (!groups[module]) {
            groups[module] = [];
        }
        groups[module].push(permission);
    });
    return groups;
});

const togglePermission = (permissionId) => {
    const index = form.permissions.indexOf(permissionId);
    if (index > -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(permissionId);
    }
};

const toggleModule = (module) => {
    const modulePermissions = groupedPermissions.value[module].map(p => p.id);
    const allSelected = modulePermissions.every(id => form.permissions.includes(id));
    
    if (allSelected) {
        form.permissions = form.permissions.filter(id => !modulePermissions.includes(id));
    } else {
        modulePermissions.forEach(id => {
            if (!form.permissions.includes(id)) {
                form.permissions.push(id);
            }
        });
    }
};

const isModuleSelected = (module) => {
    const modulePermissions = groupedPermissions.value[module].map(p => p.id);
    return modulePermissions.every(id => form.permissions.includes(id));
};

const submit = () => {
    form.post(route('admin.settings.roles.store'));
};
</script>

<template>
    <Head title="Tambah Role" />

    <ModuleLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.settings.roles.index')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    ← Kembali
                </Link>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Tambah Role Baru
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Role Name -->
                        <div>
                            <InputLabel for="name" value="Nama Role" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                placeholder="Contoh: Staff HR"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Permissions -->
                        <div>
                            <InputLabel value="Permissions" />
                            <p class="text-sm text-gray-500 mb-4">
                                Pilih permissions yang akan diberikan ke role ini.
                            </p>

                            <div class="space-y-4">
                                <div 
                                    v-for="(perms, module) in groupedPermissions" 
                                    :key="module"
                                    class="border rounded-lg p-4"
                                >
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-medium text-gray-900 capitalize">
                                            {{ module }}
                                        </h4>
                                        <button
                                            type="button"
                                            @click="toggleModule(module)"
                                            class="text-sm text-blue-600 hover:text-blue-800"
                                        >
                                            {{ isModuleSelected(module) ? 'Hapus Semua' : 'Pilih Semua' }}
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                        <label 
                                            v-for="permission in perms" 
                                            :key="permission.id"
                                            class="flex items-center gap-2 cursor-pointer"
                                        >
                                            <input
                                                type="checkbox"
                                                :value="permission.id"
                                                :checked="form.permissions.includes(permission.id)"
                                                @change="togglePermission(permission.id)"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            />
                                            <span class="text-sm text-gray-700">
                                                {{ permission.name.split('.')[1] }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <InputError class="mt-2" :message="form.errors.permissions" />
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end gap-4 pt-6 border-t">
                            <Link
                                :href="route('admin.settings.roles.index')"
                                class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition"
                            >
                                Batal
                            </Link>
                            <PrimaryButton
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Role' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </ModuleLayout>
</template>
