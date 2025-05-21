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
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fields to Export</label>
                    <div class="mt-2 space-y-2">
                        <div v-for="field in availableFields" :key="field" class="flex items-center">
                            <input
                                type="checkbox"
                                :id="'export-' + field"
                                v-model="selectedExportFields"
                                :value="field"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            >
                            <label :for="'export-' + field" class="ml-2 block text-sm text-gray-900">
                                {{ field }}
                            </label>
                        </div>
                    </div>
                </div>
                <button
                    @click="handleExport"
                    :disabled="isExporting || selectedExportFields.length === 0"
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
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fields to Import</label>
                    <div class="mt-2 space-y-2">
                        <div v-for="field in availableFields" :key="field" class="flex items-center">
                            <input
                                type="checkbox"
                                :id="'import-' + field"
                                v-model="selectedImportFields"
                                :value="field"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            >
                            <label :for="'import-' + field" class="ml-2 block text-sm text-gray-900">
                                {{ field }}
                            </label>
                        </div>
                    </div>
                </div>
                <div>
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
                    @click="handleImport"
                    :disabled="isImporting || !selectedFile || selectedImportFields.length === 0"
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

const exportType = ref('books');
const importType = ref('books');
const availableFields = ref([]);
const selectedExportFields = ref([]);
const selectedImportFields = ref([]);
const selectedFile = ref(null);
const isExporting = ref(false);
const isImporting = ref(false);

const fetchAvailableFields = async (type) => {
    try {
        const response = await axios.get(`/api/excel/fields?type=${type}`);
        availableFields.value = response.data.fields;
    } catch (error) {
        console.error('Error fetching available fields:', error);
    }
};

watch([exportType, importType], async ([newExportType, newImportType]) => {
    await fetchAvailableFields(newExportType);
    selectedExportFields.value = [];
    selectedImportFields.value = [];
});

const handleExport = async () => {
    if (selectedExportFields.value.length === 0) return;

    isExporting.value = true;
    try {
        const response = await axios.post('/api/excel/export', {
            type: exportType.value,
            fields: selectedExportFields.value
        });
        // Handle success (e.g., show notification)
        console.log('Export started:', response.data);
    } catch (error) {
        console.error('Error exporting:', error);
        // Handle error (e.g., show error notification)
    } finally {
        isExporting.value = false;
    }
};

const handleFileSelect = (event) => {
    selectedFile.value = event.target.files[0];
};

const handleImport = async () => {
    if (!selectedFile.value || selectedImportFields.value.length === 0) return;

    isImporting.value = true;
    const formData = new FormData();
    formData.append('file', selectedFile.value);
    formData.append('type', importType.value);
    formData.append('fields', JSON.stringify(selectedImportFields.value));

    try {
        const response = await axios.post('/api/excel/import', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        // Handle success (e.g., show notification)
        console.log('Import started:', response.data);
    } catch (error) {
        console.error('Error importing:', error);
        // Handle error (e.g., show error notification)
    } finally {
        isImporting.value = false;
        selectedFile.value = null;
    }
};

// Initial fetch of available fields
fetchAvailableFields(exportType.value);
</script> 