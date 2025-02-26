<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage(); // Get current page props

const menuItems = [
    { name: 'Dashboard', route: 'dashboard', icon: '🏠' },
    { name: 'Roles Management', route: 'admin.roles.index', icon: '🔑' },
    { name: 'Permissions', route: 'admin.permissions.index', icon: '🛡️' },
    { name: 'Users Management', route: 'admin.users.index', icon: '👤' },
];

const isActive = (routeName) => route().current(routeName);
</script>

<template>
    <aside class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 p-4 min-h-screen">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Admin Panel</h2>
        <nav>
            <ul>
                <li v-for="item in menuItems" :key="item.route" class="mb-2">
                    <Link
                        :href="route(item.route)"
                        class="flex items-center space-x-2 p-2 rounded transition duration-300"
                        :class="{
                            'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white font-semibold': isActive(item.route),
                            'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700': !isActive(item.route)
                        }"
                    >
                        <span>{{ item.icon }}</span>
                        <span>{{ item.name }}</span>
                    </Link>
                </li>
            </ul>
        </nav>
    </aside>
</template>
