<template>
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <router-link to="/" class="text-xl font-bold text-indigo-600">
                            Library System
                        </router-link>
                    </div>
                </div>

                <!-- Right side navigation -->
                <div class="flex items-center">
                    <div class="ml-3 relative">
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-700">{{ user?.name }}</span>
                            <button
                                @click="logout"
                                class="text-gray-600 hover:text-gray-900"
                            >
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';

const router = useRouter();
const toast = useToast();
const user = ref(null);

const logout = async () => {
    try {
        await axios.post('/api/logout');
        router.push('/login');
        toast.success('Logged out successfully');
    } catch (error) {
        console.error('Logout error:', error);
        toast.error('Failed to logout');
    }
};

onMounted(async () => {
    try {
        const response = await axios.get('/api/user');
        user.value = response.data;
    } catch (error) {
        console.error('Error fetching user:', error);
    }
});
</script> 