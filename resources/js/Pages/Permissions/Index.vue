<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const permission = ref({ id: null, name: '' });
const isModalOpen = ref(false);
defineProps({ permissions: Array });

const openModal = (perm = null) => {
    permission.value = perm ? { ...perm } : { id: null, name: '' };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const savePermission = () => {
    if (permission.value.id) {
        // Update existing permission
        router.put(route('admin.permissions.update', permission.value.id), { name: permission.value.name }, {
            onSuccess: () => closeModal(),
        });
    } else {
        // Create new permission
        router.post(route('admin.permissions.store'), { name: permission.value.name }, {
            onSuccess: () => closeModal(),
        });
    }
};

const deletePermission = (id) => {
    if (confirm("Are you sure you want to delete this permission?")) {
        router.delete(route('admin.permissions.destroy', id));
    }
};

// ✅ Get flash messages
const page = usePage();
  const flashMessage = computed(() => {
  return   page.props.flash || ''
});
</script>

<template>
    <AppLayout title="Permissions Management">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white mb-6">Manage Permissions</h1>

            <!-- ✅ Flash Message -->
            <div v-if="flashMessage" class="bg-green-100 text-green-700 p-3 rounded-md shadow-md mb-4">
                {{ flashMessage }}
            </div>

            <!-- 🌟 Add Permission Button -->
            <button
                @click="openModal()"
                class="bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-blue-700 transition"
            >
                Add New Permission
            </button>

            <!-- 🌟 Permissions List -->
            <div class="mt-6 bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Existing Permissions</h2>
                <ul class="divide-y divide-gray-300 dark:divide-gray-700">
                    <li
                        v-for="perm in permissions"
                        :key="perm.id"
                        class="py-3 px-4 flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-900 transition rounded-lg"
                    >
                        <span class="text-gray-900 dark:text-white font-medium text-lg">{{ perm.name }}</span>
                        <div class="space-x-2">
                            <button
                                @click="openModal(perm)"
                                class="px-4 py-2 text-blue-600 hover:text-blue-800 font-medium transition"
                            >
                                Edit
                            </button>
                            <button
                                @click="deletePermission(perm.id)"
                                class="px-4 py-2 text-red-600 hover:text-red-800 font-medium transition"
                            >
                                Delete
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- ✅ Modal Component -->
        <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ permission.id ? 'Edit Permission' : 'Add New Permission' }}
                </h2>
                <input
                    v-model="permission.name"
                    class="w-full border p-3 rounded-lg bg-gray-100 dark:bg-gray-700 dark:text-white focus:ring focus:ring-blue-300"
                    placeholder="Permission Name"
                    required
                />
                <div class="flex justify-end mt-4 space-x-2">
                    <button
                        @click="savePermission"
                        class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition"
                    >
                        {{ permission.id ? 'Update' : 'Save' }}
                    </button>
                    <button
                        @click="closeModal"
                        class="px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
