<script setup>
import { ref, computed } from 'vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Sidebar from '@/Components/Sidebar.vue'; // ✅ Import Sidebar
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';

defineProps({ title: String });

const page = usePage();
const isAdmin = computed(() => page.props.auth.user.roles.includes('admin'));

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />
        <Banner />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">
            <!-- Sidebar (Only for Admin) -->
            <Sidebar v-if="isAdmin" />

            <!-- Main Content -->
            <div class="flex-1">
                <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex">
                                <div class="shrink-0 flex items-center">
                                    <Link :href="route('dashboard')">
                                        <ApplicationMark class="block h-9 w-auto" />
                                    </Link>
                                </div>
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                            </div>
                            <div class="hidden sm:flex sm:items-center">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="flex text-sm border-2 border-transparent rounded-full">
                                            <img class="size-8 rounded-full object-cover"
                                                 :src="page.props.auth.user.profile_photo_url"
                                                 :alt="page.props.auth.user.name">
                                        </button>
                                    </template>
                                    <template #content>
                                        <DropdownLink :href="route('profile.show')">Profile</DropdownLink>
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">Log Out</DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Render Page Content Here -->
                <main class="p-6">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
