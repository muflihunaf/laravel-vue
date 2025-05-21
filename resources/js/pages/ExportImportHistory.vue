<template>
  <MainLayout>
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-2xl font-bold mb-6">Export/Import History</h1>
      <div class="mb-4 flex flex-wrap gap-4">
        <select v-model="filters.type" class="rounded border-gray-300 px-2 py-1" @change="fetchJobs">
          <option value="">All Types</option>
          <option value="export">Export</option>
          <option value="import">Import</option>
        </select>
        <select v-model="filters.model" class="rounded border-gray-300 px-2 py-1" @change="fetchJobs">
          <option value="">All Models</option>
          <option value="books">Books</option>
          <option value="authors">Authors</option>
          <option value="categories">Categories</option>
        </select>
        <select v-model="filters.status" class="rounded border-gray-300 px-2 py-1" @change="fetchJobs">
          <option value="">All Statuses</option>
          <option value="pending">Pending</option>
          <option value="processing">Processing</option>
          <option value="completed">Completed</option>
          <option value="failed">Failed</option>
        </select>
      </div>
      <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm bg-white">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50 sticky top-0 z-10">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fields</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Created At</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="jobs.data && jobs.data.length === 0">
              <td colspan="6" class="text-center py-8 text-gray-400">No history found.</td>
            </tr>
            <tr v-for="job in jobs.data" :key="job.uuid" class="hover:bg-gray-50 even:bg-gray-50">
              <td class="px-4 py-2 capitalize">{{ job.type }}</td>
              <td class="px-4 py-2 capitalize">{{ job.model }}</td>
              <td class="px-4 py-2">
                <span v-for="field in job.fields" :key="field" class="inline-block bg-gray-100 rounded px-2 py-0.5 text-xs mr-1 mb-1">{{ field }}</span>
              </td>
              <td class="px-4 py-2">
                <span :class="statusClass(job.status)">
                  <span v-if="job.status === 'completed'" class="inline-flex items-center"><svg class="h-4 w-4 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Completed</span>
                  <span v-else-if="job.status === 'failed'" class="inline-flex items-center"><svg class="h-4 w-4 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>Failed</span>
                  <span v-else-if="job.status === 'processing'" class="inline-flex items-center"><svg class="h-4 w-4 mr-1 text-yellow-500 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" /></svg>Processing</span>
                  <span v-else class="inline-flex items-center"><svg class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" /></svg>{{ job.status }}</span>
                </span>
              </td>
              <td class="px-4 py-2">{{ formatDate(job.created_at) }}</td>
              <td class="px-4 py-2">
                <template v-if="job.type === 'export' && job.status === 'completed'">
                  <button @click="downloadJob(job.uuid, job.filename)" class="text-indigo-600 hover:underline flex items-center" title="Download">
                    <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg>
                    Download
                  </button>
                </template>
                <template v-else-if="job.status === 'failed'">
                  <span class="text-red-600 flex items-center" :title="job.message"><svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>Error</span>
                </template>
                <template v-else>
                  <span class="text-gray-400">-</span>
                </template>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="jobs.meta && jobs.meta.last_page > 1" class="flex justify-center mt-4">
          <button
            v-for="page in jobs.meta.last_page"
            :key="page"
            @click="fetchJobs(page)"
            :class="['px-3 py-1 rounded mx-1', jobs.meta.current_page === page ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300']"
          >
            {{ page }}
          </button>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import MainLayout from '../layouts/MainLayout.vue';

const apiBaseUrl = 'http://localhost:8000';
const jobs = ref({ data: [], meta: {} });
const filters = ref({ type: '', model: '', status: '' });
const token = localStorage.getItem('token');

const fetchJobs = async (page = 1) => {
  try {
    const params = { ...filters.value, page };
    const { data } = await axios.get('/api/history', { params });
    jobs.value = data;
  } catch (e) {
    jobs.value = { data: [], meta: {} };
  }
};

const downloadJob = async (uuid, filename) => {
  try {
    const response = await axios.get(
      `${apiBaseUrl}/api/history/${uuid}/download?token=${token}`,
      {
        responseType: 'blob',
        headers: {
          Accept: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        },
      }
    );
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', filename || 'export.xlsx');
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    alert('Download failed. Please check your authentication and try again.');
  }
};

const statusClass = (status) => {
  switch (status) {
    case 'completed': return 'text-green-600 font-semibold';
    case 'failed': return 'text-red-600 font-semibold';
    case 'processing': return 'text-yellow-600 font-semibold';
    default: return 'text-gray-600';
  }
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleString();
};

onMounted(() => {
  fetchJobs();
});
</script> 