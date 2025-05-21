<template>
    <div class="space-y-4">
        <!-- Export Section -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Export to Excel</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <select v-model="exportType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="books">Books</option>
                        <option value="authors">Authors</option>
                        <option value="categories">Categories</option>
                        <option value="users">Users</option>
                    </select>
                </div>
                <button
                    @click="handleExport"
                    :disabled="isExporting"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                >
                    {{ isExporting ? 'Exporting...' : 'Export' }}
                </button>
            </div>
        </div>

        <!-- Import Section -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Import from Excel</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <select v-model="importType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="books">Books</option>
                        <option value="authors">Authors</option>
                        <option value="categories">Categories</option>
                        <option value="users">Users</option>
                    </select>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Excel File</label>
                        <input
                            type="file"
                            @change="handleFileSelect"
                            accept=".xlsx,.xls"
                            class="mt-1 block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100"
                        >
                    </div>
                    <button
                        @click="downloadTemplate"
                        class="ml-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Template
                    </button>
                </div>
                <button
                    @click="handleImport"
                    :disabled="isImporting || !selectedFile"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                >
                    {{ isImporting ? 'Importing...' : 'Import' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification';

const exportType = ref('books');
const importType = ref('books');
const selectedFile = ref(null);
const isExporting = ref(false);
const isImporting = ref(false);
const toast = useToast();

const handleExport = async () => {
    isExporting.value = true;
    try {
        const response = await axios.post('/api/excel/export', {
            type: exportType.value,
            fields: [] // Empty array means all fields
        });

        // Show success message with job info
        toast.success(response.data.message);
        console.log('Export started:', response.data);
    } catch (error) {
        console.error('Error exporting:', error);
        toast.error('Failed to start export. Please try again.');
    } finally {
        isExporting.value = false;
    }
};

const handleFileSelect = (event) => {
    selectedFile.value = event.target.files[0];
};

const handleImport = async () => {
    if (!selectedFile.value) return;

    isImporting.value = true;
    const formData = new FormData();
    formData.append('file', selectedFile.value);
    formData.append('type', importType.value);

    try {
        const response = await axios.post('/api/excel/import', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        toast.success(response.data.message);
        console.log('Import started:', response.data);
    } catch (error) {
        console.error('Error importing:', error);
        toast.error('Failed to start import. Please try again.');
    } finally {
        isImporting.value = false;
        selectedFile.value = null;
    }
};

const downloadTemplate = async () => {
    try {
        const response = await axios.post('/api/excel/export', {
            type: importType.value,
            fields: [], // Empty array means all fields
            template: true
        }, {
            responseType: 'blob'
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `${importType.value}_template.xlsx`);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error downloading template:', error);
        toast.error('Failed to download template. Please try again.');
    }
};
</script> 