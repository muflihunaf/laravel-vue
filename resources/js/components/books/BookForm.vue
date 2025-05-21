<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <div>
      <h3 class="text-lg font-semibold mb-1">
        {{ isEdit ? 'Edit Book' : 'Create Book' }}
      </h3>
      <p class="text-gray-500 text-sm mb-4">
        {{ isEdit ? 'Update the details for this book.' : 'Fill in the details to create a new book.' }}
      </p>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
      <input
        v-model="form.title"
        type="text"
        required
        :disabled="loading"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
        placeholder="Book title"
      />
      <div v-if="errors.title" class="text-red-500 text-xs mt-1">{{ errors.title }}</div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
      <select
        v-model="form.author_uuid"
        required
        :disabled="loading"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
      >
        <option value="" disabled>Select author</option>
        <option v-for="author in authors" :key="author.uuid" :value="author.uuid">
          {{ author.name }}
        </option>
      </select>
      <div v-if="errors.author_uuid" class="text-red-500 text-xs mt-1">{{ errors.author_uuid }}</div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
      <select
        v-model="form.category_uuid"
        required
        :disabled="loading"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
      >
        <option value="" disabled>Select category</option>
        <option v-for="category in categories" :key="category.uuid" :value="category.uuid">
          {{ category.name }}
        </option>
      </select>
      <div v-if="errors.category_uuid" class="text-red-500 text-xs mt-1">{{ errors.category_uuid }}</div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
      <textarea
        v-model="form.description"
        rows="2"
        :disabled="loading"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
        placeholder="Book description"
      ></textarea>
      <div v-if="errors.description" class="text-red-500 text-xs mt-1">{{ errors.description }}</div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">File</label>
      <input
        type="file"
        @change="handleFileChange"
        :disabled="loading"
        class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
      />
      <div v-if="form.file_name" class="text-xs text-gray-500 mt-1">Selected: {{ form.file_name }}</div>
      <div v-if="errors.file" class="text-red-500 text-xs mt-1">{{ errors.file }}</div>
    </div>
    <div class="flex justify-end space-x-3 pt-2">
      <button
        type="button"
        @click="$emit('cancel')"
        :disabled="loading"
        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
      >
        Cancel
      </button>
      <button
        type="submit"
        :disabled="loading"
        class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 flex items-center gap-2 transition"
      >
        <svg v-if="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        <span>{{ isEdit ? 'Update' : 'Create' }}</span>
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  modelValue: Object,
  authors: Array,
  categories: Array,
  isEdit: Boolean,
  loading: Boolean,
  errors: Object
});
const emit = defineEmits(['update:modelValue', 'submit', 'cancel']);

const form = ref({ ...props.modelValue });

watch(
  () => props.modelValue,
  (val) => {
    form.value = { ...val };
  }
);

watch(
  form,
  (val) => {
    emit('update:modelValue', val);
  },
  { deep: true }
);

function handleFileChange(e) {
  const file = e.target.files[0];
  if (file) {
    form.value.file = file;
    form.value.file_name = file.name;
  } else {
    form.value.file = null;
    form.value.file_name = '';
  }
}

function handleSubmit() {
  emit('submit', form.value);
}
</script> 