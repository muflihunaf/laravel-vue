<template>
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-indigo-600">Library System</span>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <router-link
                            to="/dashboard"
                            class="inline-flex items-center px-1 pt-1 border-b-2"
                            :class="[
                                $route.path === '/dashboard'
                                    ? 'border-indigo-500 text-gray-900'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                            ]"
                            
                        >
                            Dashboard
                        </router-link>

                        <!-- Users -->
                        <router-link
                            to="/users"
                            class="inline-flex items-center px-1 pt-1 border-b-2"
                            :class="[
                                $route.path === '/users'
                                    ? 'border-indigo-500 text-gray-900'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                            ]"
                            v-if="hasRole('Administrator')"
                        >
                            Users
                        </router-link>

                        <!-- Roles -->
                        <router-link
                            to="/roles"
                            class="inline-flex items-center px-1 pt-1 border-b-2"
                            :class="[
                                $route.path === '/roles'
                                    ? 'border-indigo-500 text-gray-900'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                            ]"
                            v-if="hasRole('Administrator') || hasRole('Librarian') || hasRole('Reader')"
                        >
                            Roles
                        </router-link>

                        <!-- Books -->
                        <router-link
                            to="/books"
                            class="inline-flex items-center px-1 pt-1 border-b-2"
                            :class="[
                                $route.path === '/books'
                                    ? 'border-indigo-500 text-gray-900'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                            ]"
                        >
                            Books
                        </router-link>

                        <router-link
                            to="/authors"
                            class="inline-flex items-center px-1 pt-1 border-b-2"
                            :class="[
                                $route.path === '/authors'
                                    ? 'border-indigo-500 text-gray-900'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                            ]"
                        >
                            Authors
                        </router-link>

                        <router-link
                            to="/categories"
                            class="inline-flex items-center px-1 pt-1 border-b-2"
                            :class="[
                                $route.path === '/categories'
                                    ? 'border-indigo-500 text-gray-900'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                            ]"
                        >
                            Categories
                        </router-link>

                        <router-link
                            to="/history"
                            class="inline-flex items-center px-1 pt-1 border-b-2"
                            :class="[
                                $route.path === '/history'
                                    ? 'border-indigo-500 text-gray-900'
                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                            ]"
                        >
                            Export/Import History
                        </router-link>

                        
                    </div>
                </div>

                <!-- Right side -->
                <div class="flex items-center">
                    <!-- Profile dropdown -->
                    <div class="ml-3 relative" v-if="hasRole('Administrator')">
                        <div>
                            <button
                                @click="showProfileMenu = !showProfileMenu"
                                class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center text-white">
                                    {{ userInitials }}
                                </div>
                            </button>
                        </div>

                        <!-- Profile dropdown menu -->
                        <div
                            v-if="showProfileMenu"
                            class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                            role="menu"
                            aria-orientation="vertical"
                            aria-labelledby="user-menu-button"
                        >
                            <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                {{ user.name }}
                            </div>
                            <a
                                href="#"
                                @click.prevent="logout"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                role="menuitem"
                            >
                                Sign out
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <router-link
                    to="/dashboard"
                    class="block pl-3 pr-4 py-2 border-l-4"
                    :class="[
                        $route.path === '/dashboard'
                            ? 'border-indigo-500 text-indigo-700 bg-indigo-50'
                            : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700',
                    ]"
                >
                    Dashboard
                </router-link>

                <router-link
                    to="/books"
                    class="block pl-3 pr-4 py-2 border-l-4"
                    :class="[
                        $route.path === '/books'
                            ? 'border-indigo-500 text-indigo-700 bg-indigo-50'
                            : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700',
                    ]"
                >
                    Books
                </router-link>

                <router-link
                    to="/authors"
                    class="block pl-3 pr-4 py-2 border-l-4"
                    :class="[
                        $route.path === '/authors'
                            ? 'border-indigo-500 text-indigo-700 bg-indigo-50'
                            : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700',
                    ]"
                >
                    Authors
                </router-link>

                <router-link
                    to="/categories"
                    class="block pl-3 pr-4 py-2 border-l-4"
                    :class="[
                        $route.path === '/categories'
                            ? 'border-indigo-500 text-indigo-700 bg-indigo-50'
                            : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700',
                    ]"
                >
                    Categories
                </router-link>

                <router-link
                    to="/history"
                    class="block pl-3 pr-4 py-2 border-l-4"
                    :class="[
                        $route.path === '/history'
                            ? 'border-indigo-500 text-indigo-700 bg-indigo-50'
                            : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700',
                    ]"
                >
                    Export/Import History
                </router-link>

                <router-link
                    v-if="hasRole('Administrator')"
                    to="/users"
                    class="block pl-3 pr-4 py-2 border-l-4"
                    :class="[
                        $route.path === '/users'
                            ? 'border-indigo-500 text-indigo-700 bg-indigo-50'
                            : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700',
                    ]"
                >
                    Users
                </router-link>

                <router-link
                    v-if="hasRole('Administrator')"
                    to="/roles"
                    class="block pl-3 pr-4 py-2 border-l-4"
                    :class="[
                        $route.path === '/roles'
                            ? 'border-indigo-500 text-indigo-700 bg-indigo-50'
                            : 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700',
                    ]"
                >
                    Roles
                </router-link>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const showProfileMenu = ref(false);
const user = ref({
    name: 'Loading...',
    roles: [],
});

const userInitials = computed(() => {
    if (!user.value.name || user.value.name === 'Loading...') return '?';
    return user.value.name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase();
});

const hasRole = (role) => {
    return user.value.roles && user.value.roles.some(r => r.name === role);
};

const fetchUser = async () => {
    try {
        const response = await axios.get('/api/user');
        user.value = response.data;
        console.log('Fetched user data:', user.value);
    } catch (error) {
        console.error('Error fetching user:', error);
    }
};

const logout = async () => {
    try {
        await axios.post('/api/logout');
        router.push('/login');
    } catch (error) {
        console.error('Error logging out:', error);
    }
};

fetchUser();
</script> 