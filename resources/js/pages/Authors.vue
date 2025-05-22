<template>
  <MainLayout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-semibold">Author Management</h1>
                        <div class="flex space-x-4">
                            <button
                                v-if="hasPermission('view_authors')"
                                @click="showTrash = !showTrash"
                                class="text-gray-600 hover:text-gray-900"
                            >
                                {{ showTrash ? 'Hide Trash' : 'Show Trash' }}
                            </button>
                            <button
                                v-if="hasPermission('create_authors')"
                                @click="showCreateModal = true"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                            >
                                Create Author
                            </button>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div v-if="hasPermission('view_authors')" class="mb-6 flex gap-4">
                        <div class="flex-1">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search authors..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @input="fetchAuthors"
                            />
                        </div>
                        <div>
                            <select
                                v-model="isActive"
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @change="fetchAuthors"
                            >
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Authors Table -->
                    <div v-if="hasPermission('view_authors')" class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th
                                        v-for="column in columns"
                                        :key="column.field"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                        @click="sort(column.field)"
                                    >
                                        {{ column.label }}
                                        <span v-if="sortField === column.field">
                                            {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                        </span>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="authors.length === 0">
                                    <td colspan="5" class="text-center py-8 text-gray-400">No authors found.</td>
                                </tr>
                                <tr v-for="author in authors" :key="author.uuid" class="hover:bg-gray-50 even:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ author.name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ author.biography }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                author.is_active
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800',
                                            ]"
                                        >
                                            {{ author.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex space-x-3">
                                            <button
                                                v-if="hasPermission('view_authors')"
                                                @click="viewAuthor(author)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                                title="View"
                                            >
                                                View
                                            </button>
                                            <button
                                                v-if="!showTrash && hasPermission('edit_authors')"
                                                @click="editAuthor(author)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                                title="Edit"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                v-if="!showTrash && hasPermission('delete_authors')"
                                                @click="deleteAuthor(author)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete"
                                            >
                                                Delete
                                            </button>
                                            <button
                                                v-if="showTrash && hasPermission('edit_authors')"
                                                @click="restoreAuthor(author)"
                                                class="text-green-600 hover:text-green-900"
                                                title="Restore"
                                            >
                                                Restore
                                            </button>
                                            <button
                                                v-if="showTrash && hasPermission('delete_authors')"
                                                @click="forceDeleteAuthor(author)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete Permanently"
                                            >
                                                Delete Permanently
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 flex justify-between items-center">
                        <div class="text-sm text-gray-700">
                            Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} entries
                        </div>
                        <div class="flex gap-2">
                            <button
                                v-for="page in pagination.last_page"
                                :key="page"
                                @click="currentPage = page"
                                :class="[
                                    'px-3 py-1 rounded',
                                    currentPage === page
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300',
                                ]"
                            >
                                {{ page }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showCreateModal && (hasPermission('create_authors') || hasPermission('edit_authors'))" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-lg">
                <h2 class="text-lg font-medium mb-4">
                    {{ editingAuthor ? 'Edit Author' : 'Create Author' }}
                </h2>
                <form @submit.prevent="handleSubmit">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Biography</label>
                        <textarea
                            v-model="form.biography"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        ></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <div class="mt-2">
                            <label class="inline-flex items-center">
                                <input
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                                <span class="ml-2">Active</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700"
                        >
                            {{ editingAuthor ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Audit Log -->
        <div class="mt-8">
            <AuditLog
                v-if="editingAuthor"
                :model-type="'App\\\\Models\\\\Author'"
                :model-id="editingAuthor.uuid"
            />
        </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import AuditLog from '../components/AuditLog.vue';
import { useRouter, useRoute } from 'vue-router';
import { usePermissions } from '../composables/usePermissions';
import ExcelOperations from '../components/ExcelOperations.vue';
import MainLayout from '../layouts/MainLayout.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const authors = ref([]);
const showCreateModal = ref(false);
const editingAuthor = ref(null);
const search = ref('');
const isActive = ref('');
const currentPage = ref(1);
const sortField = ref('created_at');
const sortDirection = ref('desc');
const router = useRouter();
const showTrash = ref(false);
const { hasPermission } = usePermissions();
const route = useRoute();

const columns = [
    { field: 'name', label: 'Name' },
    { field: 'biography', label: 'Biography' },
    { field: 'is_active', label: 'Status' },
];

const pagination = ref({
    current_page: 1,
    from: 0,
    last_page: 1,
    per_page: 10,
    to: 0,
    total: 0,
});

const form = ref({
    name: '',
    biography: '',
    is_active: true,
});

const fetchAuthors = async () => {
    try {
        const params = {
            page: currentPage.value,
            search: search.value,
            is_active: isActive.value,
            sort_field: sortField.value,
            sort_direction: sortDirection.value,
            trashed: showTrash.value,
        };

        const response = await axios.get('/api/authors', { params });
        authors.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            from: response.data.from,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            to: response.data.to,
            total: response.data.total,
        };
    } catch (error) {
        console.error('Error fetching authors:', error);
    }
};

const editAuthor = (author) => {
    editingAuthor.value = author;
    form.value = {
        name: author.name,
        biography: author.biography,
        is_active: author.is_active,
    };
    showCreateModal.value = true;
};

const deleteAuthor = async (author) => {
    if (!confirm('Are you sure you want to delete this author?')) return;
    try {
        await axios.delete(`/api/authors/${author.uuid}`);
        await fetchAuthors();
        toast.success('Author deleted successfully!');
    } catch (error) {
        console.error('Error deleting author:', error);
        toast.error('Failed to delete author.');
    }
};

const handleSubmit = async () => {
    try {
        if (editingAuthor.value) {
            await axios.put(`/api/authors/${editingAuthor.value.uuid}`, form.value);
            toast.success('Author updated successfully!');
        } else {
            await axios.post('/api/authors', form.value);
            toast.success('Author created successfully!');
        }
        showCreateModal.value = false;
        editingAuthor.value = null;
        form.value = {
            name: '',
            biography: '',
            is_active: true,
        };
        await fetchAuthors();
    } catch (error) {
        console.error('Error saving author:', error);
        toast.error('Failed to save author. Please check your input and try again.');
    }
};

const sort = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    fetchAuthors();
};

const viewAuthor = (author) => {
    router.push(`/authors/${author.uuid}`);
};

const restoreAuthor = async (author) => {
    if (!confirm('Are you sure you want to restore this author?')) return;
    try {
        await axios.post(`/api/authors/${author.uuid}/restore`);
        await fetchAuthors();
        toast.success('Author restored successfully!');
    } catch (error) {
        console.error('Error restoring author:', error);
        toast.error('Failed to restore author.');
    }
};

const forceDeleteAuthor = async (author) => {
    if (!confirm('Are you sure you want to permanently delete this author? This action cannot be undone.')) return;
    try {
        await axios.delete(`/api/authors/${author.uuid}/force`);
        await fetchAuthors();
        toast.success('Author permanently deleted!');
    } catch (error) {
        console.error('Error permanently deleting author:', error);
        toast.error('Failed to permanently delete author.');
    }
};

// Watch for route state changes
watch(() => route.state, (newState) => {
    if (newState?.editAuthor) {
        editingAuthor.value = newState.editAuthor;
        form.value = {
            name: newState.editAuthor.name,
            biography: newState.editAuthor.biography,
            is_active: newState.editAuthor.is_active,
        };
        showCreateModal.value = true;
    }
}, { immediate: true });

watch([currentPage, search, isActive, showTrash], () => {
    fetchAuthors();
});

onMounted(() => {
    fetchAuthors();
});
</script> 