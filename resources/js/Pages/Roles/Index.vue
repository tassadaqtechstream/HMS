<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';

const role = ref({ id: null, name: '' });
const isModalOpen = ref(false);
const permissions = ref([]); // All available permissions
const selectedPermissions = ref([]); // Assigned permission IDs
const isPermissionModalOpen = ref(false);
const roleIdForPermissions = ref(null);

defineProps({ roles: Array });

const fetchPermissions = async (roleId) => {
    roleIdForPermissions.value = roleId;

    try {
        const response = await fetch(route('admin.roles.permissions', roleId));
        const data = await response.json();

        permissions.value = data.permissions; // Load all available permissions
        selectedPermissions.value = data.assigned_permissions.map(p => p.id); // Store assigned permission IDs

        isPermissionModalOpen.value = true;
    } catch (error) {
        console.error('Error fetching permissions:', error);
    }
};

const updatePermissions = () => {
    router.put(route('admin.roles.assign-permissions', roleIdForPermissions.value),
        { permissions: selectedPermissions.value }, {
            onSuccess: () => {
                isPermissionModalOpen.value = false;
            },
            onError: (errors) => {
                console.error('Error updating permissions:', errors);
            }
        }
    );
};

const openModal = (existingRole = null) => {
    role.value = existingRole ? { ...existingRole } : { id: null, name: '' };
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const saveRole = () => {
    if (role.value.id) {
        router.put(route('admin.roles.update', role.value.id), { name: role.value.name }, {
            onSuccess: closeModal,
        });
    } else {
        router.post(route('admin.roles.store'), { name: role.value.name }, {
            onSuccess: closeModal,
        });
    }
};

const deleteRole = (roleId) => {
    if (confirm('Are you sure you want to delete this role?')) {
        router.delete(route('admin.roles.destroy', roleId));
    }
};

const page = usePage();

// ✅ Get flash messages
const flashMessage = computed(() => page.props.flash?.success || '');

</script>

<template>
    <AppLayout title="Roles Management">
        <div class="p-6">
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white mb-6">Manage Roles</h1>

            <!-- ✅ Flash Message -->
            <div v-if="flashMessage" class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ flashMessage }}
            </div>


            <!-- 🌟 Add Role Button -->
            <button
                @click="openModal()"
                class="mb-4 px-5 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
            >
                Add New Role
            </button>

            <!-- 🌟 Roles List -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Existing Roles</h2>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    <li
                        v-for="r in roles"
                        :key="r.id"
                        class="py-3 px-4 flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-lg mb-2"
                    >
                        <span class="text-gray-900 dark:text-white font-medium text-lg">{{ r.name }}</span>
                        <div class="space-x-2">
                            <button
                                @click="openModal(r)"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700"
                            >
                                Edit
                            </button>
                            <button
                                @click="deleteRole(r.id)"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-700"
                            >
                                Delete
                            </button>
                            <button
                                @click="fetchPermissions(r.id)"
                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-700"
                            >
                                Assign Permission
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- ✅ Create/Edit Role Modal -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-semibold">
                    {{ role.id ? 'Edit Role' : 'Add New Role' }}
                </h2>
                <form @submit.prevent="saveRole" class="mt-4 space-y-4">
                    <input
                        v-model="role.name"
                        class="w-full border p-3 rounded-lg"
                        placeholder="Enter role name"
                        required
                    />
                    <div class="flex justify-end space-x-2">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg"
                        >
                            {{ role.id ? 'Save Changes' : 'Add Role' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- ✅ Assign Permissions Modal -->
        <Modal :show="isPermissionModalOpen" @close="isPermissionModalOpen = false">
            <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                <!-- 🌟 Modal Title -->
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                    Assign Permissions
                </h2>

                <!-- 🌟 Horizontal Permissions Grid -->
                <div class="flex flex-wrap gap-3 max-h-60 overflow-y-auto">
                    <label
                        v-for="permission in permissions"
                        :key="permission.id"
                        class="flex items-center space-x-2 bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition"
                    >
                        <input
                            type="checkbox"
                            :value="permission.id"
                            v-model="selectedPermissions"
                            class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring focus:ring-green-400"
                        />
                        <span class="text-gray-900 dark:text-white text-sm">
                    {{ permission.name }}
                </span>
                    </label>
                </div>

                <!-- 🌟 Action Buttons -->
                <div class="flex justify-end space-x-3 mt-6">
                    <button
                        type="button"
                        @click="isPermissionModalOpen = false"
                        class="px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 transition"
                    >
                        Cancel
                    </button>
                    <button
                        @click="updatePermissions"
                        class="px-5 py-2 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 transition"
                    >
                        Save Permissions
                    </button>
                </div>
            </div>
        </Modal>


    </AppLayout>
</template>
