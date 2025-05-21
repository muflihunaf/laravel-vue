<template>
  <MainLayout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-semibold">Category Management</h1>
                        <div class="flex space-x-4">
                            <button
                                v-if="hasPermission('view_categories')"
                                @click="showTrash = !showTrash"
                                class="text-gray-600 hover:text-gray-900"
                            >
                                {{ showTrash ? 'Hide Trash' : 'Show Trash' }}
                            </button>
                            <button
                                v-if="hasPermission('create_categories')"
                                @click="showCreateModal = true"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                            >
                                Create Category
                            </button>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div v-if="hasPermission('view_categories')" class="mb-6 flex gap-4">
                        <div class="flex-1">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search categories..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @input="fetchCategories"
                            />
                        </div>
                        <div>
                            <select
                                v-model="isActive"
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @change="fetchCategories"
                            >
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Categories Table -->
                    <div v-if="hasPermission('view_categories')" class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
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
                                <tr v-if="categories.length === 0">
                                    <td colspan="5" class="text-center py-8 text-gray-400">No categories found.</td>
                                </tr>
                                <tr v-for="category in categories" :key="category.uuid" class="hover:bg-gray-50 even:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ category.name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ category.description }}
                                    </td>
                                    <td class="px-6 py-4">
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
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex space-x-3">
                                            <button
                                                v-if="hasPermission('view_categories')"
                                                @click="viewCategory(category)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                                title="View"
                                            >
                                                View
                                            </button>
                                            <button
                                                v-if="!showTrash && hasPermission('edit_categories')"
                                                @click="editCategory(category)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                                title="Edit"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                v-if="!showTrash && hasPermission('delete_categories')"
                                                @click="deleteCategory(category)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete"
                                            >
                                                Delete
                                            </button>
                                            <button
                                                v-if="showTrash && hasPermission('edit_categories')"
                                                @click="restoreCategory(category)"
                                                class="text-green-600 hover:text-green-900"
                                                title="Restore"
                                            >
                                                Restore
                                            </button>
                                            <button
                                                v-if="showTrash && hasPermission('delete_categories')"
                                                @click="forceDeleteCategory(category)"
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
        <div v-if="showCreateModal && (hasPermission('create_categories') || hasPermission('edit_categories'))" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-lg">
                <h2 class="text-lg font-medium mb-4">
                    {{ editingCategory ? 'Edit Category' : 'Create Category' }}
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
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea
                            v-model="form.description"
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
                            {{ editingCategory ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Audit Log -->
        <div class="mt-8">
            <AuditLog
                v-if="editingCategory"
                :model-type="'App\\\Models\\\Category'"
                :model-id="editingCategory.uuid"
            />
        </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import AuditLog from '../components/AuditLog.vue';
import { useRouter } from 'vue-router';
import { usePermissions } from '../composables/usePermissions';
import ExcelOperations from '../components/ExcelOperations.vue';
import MainLayout from '../layouts/MainLayout.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const { hasPermission } = usePermissions();

const categories = ref([]);
const showCreateModal = ref(false);
const editingCategory = ref(null);
const search = ref('');
const isActive = ref('');
const currentPage = ref(1);
const sortField = ref('created_at');
const sortDirection = ref('desc');
const router = useRouter();
const showTrash = ref(false);

const columns = [
    { field: 'name', label: 'Name' },
    { field: 'description', label: 'Description' },
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
    description: '',
    is_active: true,
});

const fetchCategories = async () => {
    try {
        const params = {
            page: currentPage.value,
            search: search.value,
            is_active: isActive.value,
            sort_field: sortField.value,
            sort_direction: sortDirection.value,
            trashed: showTrash.value,
        };

        const response = await axios.get('/api/categories', { params });
        categories.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            from: response.data.from,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            to: response.data.to,
            total: response.data.total,
        };
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
};

const editCategory = (category) => {
    editingCategory.value = category;
    form.value = {
        name: category.name,
        description: category.description,
        is_active: category.is_active,
    };
    showCreateModal.value = true;
};

const deleteCategory = async (category) => {
    if (!confirm('Are you sure you want to delete this category?')) return;
    try {
        await axios.delete(`/api/categories/${category.uuid}`);
        await fetchCategories();
        toast.success('Category deleted successfully!');
    } catch (error) {
        console.error('Error deleting category:', error);
        toast.error('Failed to delete category.');
    }
};

const handleSubmit = async () => {
    try {
        if (editingCategory.value) {
            await axios.put(`/api/categories/${editingCategory.value.uuid}`, form.value);
            toast.success('Category updated successfully!');
        } else {
            await axios.post('/api/categories', form.value);
            toast.success('Category created successfully!');
        }
        showCreateModal.value = false;
        editingCategory.value = null;
        form.value = {
            name: '',
            description: '',
            is_active: true,
        };
        await fetchCategories();
    } catch (error) {
        console.error('Error saving category:', error);
        toast.error('Failed to save category. Please check your input and try again.');
    }
};

const sort = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    fetchCategories();
};

const viewCategory = (category) => {
    router.push(`/categories/${category.uuid}`);
};

const restoreCategory = async (category) => {
    if (!confirm('Are you sure you want to restore this category?')) return;
    try {
        await axios.post(`/api/categories/${category.uuid}/restore`);
        await fetchCategories();
        toast.success('Category restored successfully!');
    } catch (error) {
        console.error('Error restoring category:', error);
        toast.error('Failed to restore category.');
    }
};

const forceDeleteCategory = async (category) => {
    if (!confirm('Are you sure you want to permanently delete this category? This action cannot be undone.')) return;
    try {
        await axios.delete(`/api/categories/${category.uuid}/force`);
        await fetchCategories();
        toast.success('Category permanently deleted!');
    } catch (error) {
        console.error('Error permanently deleting category:', error);
        toast.error('Failed to permanently delete category.');
    }
};

watch([currentPage, search, isActive, showTrash], () => {
    fetchCategories();
});

onMounted(() => {
    fetchCategories();
});
</script> 