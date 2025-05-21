<template>
    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-semibold">Role Management</h1>
                            <button
                                v-if="hasPermission('create_roles')"
                                @click="showCreateModal = true"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
                            >
                                Create Role
                            </button>
                        </div>

                        <!-- Search -->
                        <div v-if="hasPermission('view_roles')" class="mb-6">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search roles..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @input="fetchRoles"
                            />
                        </div>

                        <!-- Roles Table -->
                        <div v-if="hasPermission('view_roles')" class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
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
                                    <tr v-if="roles.length === 0">
                                        <td colspan="4" class="text-center py-8 text-gray-400">No roles found.</td>
                                    </tr>
                                    <tr v-for="role in roles" :key="role.uuid" class="hover:bg-gray-50 even:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ role.name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ role.description }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                <span
                                                    v-for="permission in role.permissions"
                                                    :key="permission.uuid"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                                                >
                                                    {{ permission.name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-3">
                                                <button
                                                    v-if="hasPermission('edit_roles')"
                                                    @click="editRole(role)"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                    title="Edit"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    v-if="hasPermission('delete_roles')"
                                                    @click="deleteRole(role)"
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
        <div v-if="showCreateModal && (hasPermission('create_roles') || hasPermission('edit_roles'))" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-lg">
                <h2 class="text-lg font-medium mb-4">
                    {{ editingRole ? 'Edit Role' : 'Create Role' }}
                </h2>
                <RoleForm
                  v-model="form"
                  :permissions="permissions"
                  :isEdit="!!editingRole"
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
import RoleForm from '../components/roles/RoleForm.vue';

const toast = useToast();
const { hasPermission } = usePermissions();

const roles = ref([]);
const permissions = ref([]);
const showCreateModal = ref(false);
const editingRole = ref(null);
const search = ref('');
const currentPage = ref(1);
const sortField = ref('created_at');
const sortDirection = ref('desc');
const router = useRouter();

const columns = [
    { field: 'name', label: 'Name' },
    { field: 'description', label: 'Description' },
    { field: 'permissions', label: 'Permissions' },
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
    permissions: [],
});
const formLoading = ref(false);
const formErrors = ref({});

const fetchRoles = async () => {
    try {
        const params = {
            page: currentPage.value,
            search: search.value,
            sort_field: sortField.value,
            sort_direction: sortDirection.value,
        };

        const response = await axios.get('/api/roles', { params });
        roles.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            from: response.data.from,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            to: response.data.to,
            total: response.data.total,
        };
    } catch (error) {
        console.error('Error fetching roles:', error);
        toast.error('Failed to fetch roles');
    }
};

const fetchPermissions = async () => {
    try {
        const response = await axios.get('/api/permissions');
        permissions.value = response.data;
    } catch (error) {
        console.error('Error fetching permissions:', error);
        toast.error('Failed to fetch permissions');
    }
};

const openCreateModal = () => {
    editingRole.value = null;
    form.value = { name: '', description: '', permissions: [] };
    formErrors.value = {};
    showCreateModal.value = true;
};

const editRole = (role) => {
    editingRole.value = role;
    form.value = {
        name: role.name,
        description: role.description,
        permissions: role.permissions.map(permission => permission.uuid),
    };
    formErrors.value = {};
    showCreateModal.value = true;
};

const deleteRole = async (role) => {
    if (!confirm('Are you sure you want to delete this role?')) return;
    try {
        await axios.delete(`/api/roles/${role.uuid}`);
        await fetchRoles();
        toast.success('Role deleted successfully!');
    } catch (error) {
        console.error('Error deleting role:', error);
        toast.error('Failed to delete role.');
    }
};

const handleFormSubmit = async (roleData) => {
    formLoading.value = true;
    formErrors.value = {};
    try {
        if (editingRole.value) {
            await axios.put(`/api/roles/${editingRole.value.uuid}`, roleData);
            toast.success('Role updated successfully!');
        } else {
            await axios.post('/api/roles', roleData);
            toast.success('Role created successfully!');
        }
        showCreateModal.value = false;
        editingRole.value = null;
        form.value = { name: '', description: '', permissions: [] };
        await fetchRoles();
    } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
            formErrors.value = error.response.data.errors;
        } else {
            toast.error('Failed to save role. Please check your input and try again.');
        }
        console.error('Error saving role:', error);
    } finally {
        formLoading.value = false;
    }
};

const handleFormCancel = () => {
    showCreateModal.value = false;
    editingRole.value = null;
    form.value = { name: '', description: '', permissions: [] };
    formErrors.value = {};
};

const sort = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    fetchRoles();
};

watch([currentPage, search], () => {
    fetchRoles();
});

onMounted(() => {
    fetchRoles();
    fetchPermissions();
});
</script> 