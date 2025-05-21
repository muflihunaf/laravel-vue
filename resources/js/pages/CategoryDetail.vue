<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Back Button -->
                    <div class="mb-6">
                        <button
                            @click="$router.push('/categories')"
                            class="text-indigo-600 hover:text-indigo-900"
                        >
                            ← Back to Categories
                        </button>
                    </div>

                    <!-- Category Details -->
                    <div v-if="category" class="space-y-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-3xl font-bold">{{ category.name }}</h1>
                                <p class="text-gray-600 mt-2">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            category.is_active
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800',
                                        ]"
                                    >
                                        {{ category.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <button
                                    @click="editCategory"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                                >
                                    Edit
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h2 class="text-xl font-semibold mb-4">Details</h2>
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Description</h3>
                                        <p class="mt-1">{{ category.description }}</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h2 class="text-xl font-semibold mb-4">Books</h2>
                                <div class="space-y-4">
                                    <div v-if="category.books && category.books.length > 0">
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span
                                                v-for="book in category.books"
                                                :key="book.uuid"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                            >
                                                {{ book.title }}
                                            </span>
                                        </div>
                                    </div>
                                    <div v-else class="text-gray-500">
                                        No books in this category
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Audit Log -->
                        <div class="mt-8">
                            <h2 class="text-xl font-semibold mb-4">Audit History</h2>
                            <AuditLog
                                :model-type="'App\\Models\\Category'"
                                :model-id="category.uuid"
                            />
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div v-else class="flex justify-center items-center h-64">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import AuditLog from '../components/AuditLog.vue';

const route = useRoute();
const router = useRouter();
const category = ref(null);

const fetchCategory = async () => {
    try {
        const response = await axios.get(`/api/categories/${route.params.uuid}`);
        category.value = response.data;
    } catch (error) {
        console.error('Error fetching category:', error);
    }
};

const editCategory = () => {
    router.push(`/categories/${category.value.uuid}/edit`);
};

onMounted(() => {
    fetchCategory();
});
</script> 