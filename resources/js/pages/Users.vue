<template>
    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-8">
                            <ExcelOperations />
                        </div>
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-semibold">User Management</h1>
                            <button
                                v-if="hasPermission('create_users')"
                                @click="showCreateModal = true"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                            >
                                Create User
                            </button>
                        </div>

                        <!-- Search and Filter -->
                        <div v-if="hasPermission('view_users')" class="mb-6 flex gap-4">
                            <div class="flex-1">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Search users..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @input="fetchUsers"
                                />
                            </div>
                            <div>
                                <select
                                    v-model="roleFilter"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @change="fetchUsers"
                                >
                                    <option value="">All Roles</option>
                                    <option v-for="role in roles" :key="role.uuid" :value="role.uuid">
                                        {{ role.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Users Table -->
                        <div v-if="hasPermission('view_users')" class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
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
                                    <tr v-if="users.length === 0">
                                        <td colspan="6" class="text-center py-8 text-gray-400">No users found.</td>
                                    </tr>
                                    <tr v-for="user in users" :key="user.uuid" class="hover:bg-gray-50 even:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ user.name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ user.email }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                <span
                                                    v-for="role in user.roles"
                                                    :key="role.uuid"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                                                >
                                                    {{ role.name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-3">
                                                <button
                                                    v-if="hasPermission('edit_users')"
                                                    @click="editUser(user)"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                    title="Edit"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    v-if="hasPermission('delete_users')"
                                                    @click="deleteUser(user)"
                                                    class="text-red-600 hover:text-red-900"
                                                    title="Delete"
                                                >
                                                    Delete
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
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showCreateModal && (hasPermission('create_users') || hasPermission('edit_users'))" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-lg">
                <h2 class="text-lg font-medium mb-4">
                    {{ editingUser ? 'Edit User' : 'Create User' }}
                </h2>
                <UserForm
                  v-model="form"
                  :roles="roles"
                  :isEdit="!!editingUser"
                  :loading="formLoading"
                  :errors="formErrors"
                  @submit="handleFormSubmit"
                  @cancel="handleFormCancel"
                />
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { usePermissions } from '../composables/usePermissions';
import MainLayout from '../layouts/MainLayout.vue';
import { useToast } from 'vue-toastification';
import UserForm from '../components/users/UserForm.vue';
import ExcelOperations from '../components/ExcelOperations.vue';

const toast = useToast();
const { hasPermission } = usePermissions();

const users = ref([]);
const roles = ref([]);
const showCreateModal = ref(false);
const editingUser = ref(null);
const search = ref('');
const roleFilter = ref('');
const currentPage = ref(1);
const sortField = ref('created_at');
const sortDirection = ref('desc');
const router = useRouter();

const columns = [
    { field: 'name', label: 'Name' },
    { field: 'email', label: 'Email' },
    { field: 'roles', label: 'Roles' },
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
    email: '',
    password: '',
    roles: [],
});
const formLoading = ref(false);
const formErrors = ref({});

const fetchUsers = async () => {
    try {
        const params = {
            page: currentPage.value,
            search: search.value,
            role: roleFilter.value,
            sort_field: sortField.value,
            sort_direction: sortDirection.value,
        };

        const response = await axios.get('/api/users', { params });
        users.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            from: response.data.from,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            to: response.data.to,
            total: response.data.total,
        };
    } catch (error) {
        console.error('Error fetching users:', error);
        toast.error('Failed to fetch users');
    }
};

const fetchRoles = async () => {
    try {
        const response = await axios.get('/api/roles');
        roles.value = response.data.data || response.data; // support both paginated and array
    } catch (error) {
        console.error('Error fetching roles:', error);
        toast.error('Failed to fetch roles');
    }
};

const openCreateModal = () => {
    editingUser.value = null;
    form.value = { name: '', email: '', password: '', roles: [] };
    formErrors.value = {};
    showCreateModal.value = true;
};

const editUser = (user) => {
    editingUser.value = user;
    form.value = {
        name: user.name,
        email: user.email,
        password: '',
        roles: user.roles.map(role => role.uuid),
    };
    formErrors.value = {};
    showCreateModal.value = true;
};

const deleteUser = async (user) => {
    if (!confirm('Are you sure you want to delete this user?')) return;
    try {
        await axios.delete(`/api/users/${user.uuid}`);
        await fetchUsers();
        toast.success('User deleted successfully!');
    } catch (error) {
        console.error('Error deleting user:', error);
        toast.error('Failed to delete user.');
    }
};

const handleFormSubmit = async (userData) => {
    formLoading.value = true;
    formErrors.value = {};
    try {
        if (editingUser.value) {
            await axios.put(`/api/users/${editingUser.value.uuid}`, userData);
            toast.success('User updated successfully!');
        } else {
            await axios.post('/api/users', userData);
            toast.success('User created successfully!');
        }
        showCreateModal.value = false;
        editingUser.value = null;
        form.value = { name: '', email: '', password: '', roles: [] };
        await fetchUsers();
    } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
            formErrors.value = error.response.data.errors;
        } else {
            toast.error('Failed to save user. Please check your input and try again.');
        }
        console.error('Error saving user:', error);
    } finally {
        formLoading.value = false;
    }
};

const handleFormCancel = () => {
    showCreateModal.value = false;
    editingUser.value = null;
    form.value = { name: '', email: '', password: '', roles: [] };
    formErrors.value = {};
};

const sort = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    fetchUsers();
};

watch([currentPage, search, roleFilter], () => {
    fetchUsers();
});

onMounted(() => {
    fetchUsers();
    fetchRoles();
});
</script> 