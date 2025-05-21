<template>
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Audit Log</h3>
        
        <div class="space-y-4">
            <div v-for="audit in audits" :key="audit.id" class="border-b pb-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="{
                                'bg-green-100 text-green-800': audit.event === 'created',
                                'bg-blue-100 text-blue-800': audit.event === 'updated',
                                'bg-red-100 text-red-800': audit.event === 'deleted'
                            }">
                            {{ audit.event }}
                        </span>
                        <div class="mt-1">
                            <span class="text-sm font-medium text-gray-900">
                                {{ audit.user?.name || 'System' }}
                            </span>
                            <span v-if="audit.user?.email" class="text-sm text-gray-500 ml-2">
                                ({{ audit.user.email }})
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-gray-500">
                            {{ formatDate(audit.created_at) }}
                        </span>
                        <div v-if="audit.ip_address" class="text-xs text-gray-400 mt-1">
                            IP: {{ audit.ip_address }}
                        </div>
                    </div>
                </div>

                <div v-if="audit.event === 'updated'" class="mt-2">
                    <div v-for="(value, key) in audit.new_values" :key="key" class="mb-2">
                        <div class="text-sm font-medium text-gray-700">{{ formatKey(key) }}</div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">Old:</span>
                                {{ formatValue(audit.old_values[key]) }}
                            </div>
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">New:</span>
                                {{ formatValue(value) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else-if="audit.event === 'created'" class="mt-2">
                    <div v-for="(value, key) in audit.new_values" :key="key" class="mb-2">
                        <div class="text-sm font-medium text-gray-700">{{ formatKey(key) }}</div>
                        <div class="text-sm text-gray-500">{{ formatValue(value) }}</div>
                    </div>
                </div>

                <div v-else-if="audit.event === 'deleted'" class="mt-2">
                    <div v-for="(value, key) in audit.old_values" :key="key" class="mb-2">
                        <div class="text-sm font-medium text-gray-700">{{ formatKey(key) }}</div>
                        <div class="text-sm text-gray-500">{{ formatValue(value) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="audits.length === 0" class="text-center text-gray-500 py-4">
            No audit logs found
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    modelType: {
        type: String,
        required: true
    },
    modelId: {
        type: String,
        required: true
    }
});

const audits = ref([]);

const fetchAudits = async () => {
    try {
        const encodedModelType = encodeURIComponent(props.modelType);
        const response = await axios.get(`/api/audits/${encodedModelType}/${props.modelId}`);
        audits.value = response.data;
    } catch (error) {
        console.error('Error fetching audits:', error);
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleString();
};

const formatKey = (key) => {
    return key.split('_').map(word => 
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
};

const formatValue = (value) => {
    if (value === null) return 'N/A';
    if (typeof value === 'boolean') return value ? 'Yes' : 'No';
    if (Array.isArray(value)) return value.join(', ');
    return value;
};

onMounted(() => {
    fetchAudits();
});
</script> 