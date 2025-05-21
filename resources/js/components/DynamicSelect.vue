<template>
    <div class="relative">
        <VueSelect
            v-model="selectedValue"
            :options="formattedOptions"
            :multiple="multiple"
            :searchable="searchable"
            :placeholder="placeholder"
            :ajax="ajax"
            :ajax-url="ajaxUrl"
            @update:modelValue="handleChange"
        />
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Array],
        default: ''
    },
    options: {
        type: Array,
        required: false,
        default: () => []
    },
    placeholder: {
        type: String,
        default: 'Select an option'
    },
    multiple: {
        type: Boolean,
        default: false
    },
    searchable: {
        type: Boolean,
        default: true
    },
    ajax: {
        type: Boolean,
        default: false
    },
    ajaxUrl: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update:modelValue']);
const selectedValue = ref(props.modelValue);

const formattedOptions = computed(() => {
    return props.options.map(option => ({
        label: option.name,
        value: option.uuid
    }));
});

const handleChange = (value) => {
    emit('update:modelValue', value);
};

watch(
    () => props.modelValue,
    (newValue) => {
        selectedValue.value = newValue;
    }
);
</script>

<style>
/* Add any custom styles for VueSelect here if needed */
</style> 