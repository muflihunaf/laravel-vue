<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Back Button -->
                    <div class="mb-6">
                        <button
                            @click="$router.push('/books')"
                            class="text-indigo-600 hover:text-indigo-900"
                        >
                            ← Back to Books
                        </button>
                    </div>

                    <!-- Book Details -->
                    <div v-if="book" class="space-y-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-3xl font-bold">{{ book.title }}</h1>
                                <p class="text-gray-600 mt-2">ISBN: {{ book.isbn }}</p>
                            </div>
                            <div class="flex space-x-4">
                                <button
                                    v-if="book.file_path"
                                    @click="downloadBook"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
                                >
                                    Download
                                </button>
                                <!-- <button
                                    @click="editBook"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                                >
                                    Edit
                                </button> -->
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h2 class="text-xl font-semibold mb-4">Details</h2>
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Description</h3>
                                        <p class="mt-1">{{ book.description }}</p>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Publication Year</h3>
                                        <p class="mt-1">{{ book.publication_year }}</p>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Status</h3>
                                        <p class="mt-1">
                                            <span
                                                :class="[
                                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                    book.is_available
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800',
                                                ]"
                                            >
                                                {{ book.is_available ? 'Available' : 'Not Available' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h2 class="text-xl font-semibold mb-4">Relations</h2>
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Authors</h3>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span
                                                v-for="author in book.authors"
                                                :key="author.uuid"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                            >
                                                {{ author.name }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500">Categories</h3>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span
                                                v-for="category in book.categories"
                                                :key="category.uuid"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                            >
                                                {{ category.name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Audit Log -->
                        <div class="mt-8">
                            <h2 class="text-xl font-semibold mb-4">Audit History</h2>
                            <AuditLog
                                :model-type="'App\\Models\\Book'"
                                :model-id="book.uuid"
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
const book = ref(null);

const fetchBook = async () => {
    try {
        const response = await axios.get(`/api/books/${route.params.uuid}`);
        book.value = response.data;
    } catch (error) {
        console.error('Error fetching book:', error);
    }
};

const downloadBook = async () => {
    try {
        const response = await axios.get(`/api/books/${book.value.uuid}/download`, {
            responseType: 'blob',
        });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `${book.value.title}.${book.value.file_path.split('.').pop()}`);
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (error) {
        console.error('Error downloading book:', error);
    }
};

const editBook = () => {
    router.push({ 
        name: 'books',
        query: { edit: book.value.uuid }
    });
};

onMounted(() => {
    fetchBook();
});
</script> 