<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <div>
      <h3 class="text-lg font-semibold mb-1">
        {{ isEdit ? 'Edit User' : 'Create User' }}
      </h3>
      <p class="text-gray-500 text-sm mb-4">
        {{ isEdit ? 'Update the details for this user.' : 'Fill in the details to create a new user.' }}
      </p>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
      <input
        v-model="form.name"
        type="text"
        required
        :disabled="loading"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
        placeholder="User name"
      />
      <div v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
      <input
        v-model="form.email"
        type="email"
        required
        :disabled="loading"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
        placeholder="user@example.com"
      />
      <div v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Password <span v-if="isEdit" class="text-xs text-gray-400">(leave blank to keep current)</span></label>
      <input
        v-model="form.password"
        type="password"
        :required="!isEdit"
        :disabled="loading"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
        placeholder="Password"
        autocomplete="new-password"
      />
      <div v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password }}</div>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Roles</label>
      <div class="flex flex-wrap gap-2 bg-gray-50 p-2 rounded">
        <label
          v-for="role in roles"
          :key="role.uuid"
          class="inline-flex items-center px-2 py-1 rounded hover:bg-indigo-50 transition"
        >
          <input
            v-model="form.roles"
            type="checkbox"
            :value="role.uuid"
            :disabled="loading"
            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          />
          <span class="ml-2 text-xs text-gray-700">{{ role.name }}</span>
        </label>
      </div>
      <div v-if="errors.roles" class="text-red-500 text-xs mt-1">{{ errors.roles }}</div>
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
  roles: Array,
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

function handleSubmit() {
  emit('submit', form.value);
}
</script> 