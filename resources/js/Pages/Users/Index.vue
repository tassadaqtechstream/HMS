<script setup>
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ users: Array, roles: Array });

const showModal = ref(false);
const form = ref({
    id: null,
    name: '',
    email: '',
    password: '',
    role: '' // Store role ID instead of role name
});

// Open modal for add/edit user
const openModal = (user = null) => {
    if (user) {
        form.value = {
            id: user.id,
            name: user.name,
            email: user.email,
            password: '', // No password confirmation needed
            role: user.roles[0]?.id || '' // ✅ Store role ID instead of name
        };
    } else {
        form.value = { id: null, name: '', email: '', password: '', role: '' };
    }
    showModal.value = true;
};

// Close modal
const closeModal = () => {
    showModal.value = false;
};

// Save or update user
const saveUser = () => {
    if (form.value.id) {
        router.put(route('admin.users.update', form.value.id), form.value, { onSuccess: closeModal });
    } else {
        router.post(route('admin.users.store'), form.value, { onSuccess: closeModal });
    }
};

// Delete user
const deleteUser = (id) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('admin.users.destroy', id));
    }
};

// Flash Message Handling
const page = usePage();
const flashMessage = computed(() => page.props.flash.success || '');


// ✅ Get validation errors
const errors = computed(() => page.props.flash?.error || '');
</script>

<template>
    <AppLayout title="Users">
        <div class="p-6 bg-white shadow rounded-lg">
            <h2 class="text-lg font-semibold mb-4">User Management</h2>

            <!-- ✅ Flash Message -->
            <div v-if="flashMessage" class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ flashMessage }}
            </div>

            <!-- ✅ Flash Error Message -->
            <div v-if="errors" class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ errors }}
            </div>


            <!-- 🌟 Add User Button -->
            <button @click="openModal()" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">Add User</button>

            <!-- 🌟 User Table -->
            <table class="w-full border-collapse border border-gray-300 mt-4">
                <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 p-2">ID</th>
                    <th class="border border-gray-300 p-2">Name</th>
                    <th class="border border-gray-300 p-2">Email</th>
                    <th class="border border-gray-300 p-2">Role</th>
                    <th class="border border-gray-300 p-2">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="user in props.users" :key="user.id" class="text-center">
                    <td class="border border-gray-300 p-2">{{ user.id }}</td>
                    <td class="border border-gray-300 p-2">{{ user.name }}</td>
                    <td class="border border-gray-300 p-2">{{ user.email }}</td>
                    <td class="border border-gray-300 p-2">{{ user.roles.length ? user.roles[0].name : 'N/A' }}</td>
                    <td class="border border-gray-300 p-2">
                        <button @click="openModal(user)" class="bg-blue-500 text-white px-3 py-1 rounded mr-2">Edit</button>
                        <button @click="deleteUser(user.id)" class="bg-red-500 text-white px-3 py-1 rounded">Delete</button>
                    </td>
                </tr>
                </tbody>
            </table>

            <!-- ✅ Modal -->
            <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
                <div class="bg-white p-6 rounded shadow-lg w-96">
                    <h3 class="text-lg font-semibold mb-4">{{ form.id ? 'Edit User' : 'Add User' }}</h3>

                    <label class="block mb-2">Name</label>
                    <input v-model="form.name" type="text" class="w-full border p-2 rounded mb-3" required />

                    <label class="block mb-2">Email</label>
                    <input v-model="form.email" type="email" class="w-full border p-2 rounded mb-3" required />

                    <label class="block mb-2">Password</label>
                    <input v-model="form.password" type="password" class="w-full border p-2 rounded mb-3" placeholder="Leave blank to keep current password" />

                    <label class="block mb-2">Role</label>
                    <select v-model="form.role" class="w-full border p-2 rounded mb-3" required>
                        <option v-for="role in props.roles" :key="role.id" :value="role.id">
                            {{ role.name }}
                        </option>
                    </select>

                    <div class="flex justify-end">
                        <button @click="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                        <button @click="saveUser" class="bg-blue-500 text-white px-4 py-2 rounded">
                            {{ form.id ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
