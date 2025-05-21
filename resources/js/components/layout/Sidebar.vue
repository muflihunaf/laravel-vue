<template>
    <div class="hidden md:flex md:flex-shrink-0">
        <div class="flex flex-col w-64">
            <div class="flex flex-col h-0 flex-1 bg-white border-r border-gray-200">
                <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                    <nav class="mt-5 flex-1 px-2 space-y-1">
                        <router-link
                            v-for="item in filteredNavigation"
                            :key="item.route"
                            :to="item.route"
                            :class="[
                                isActiveRoute(item.route)
                                    ? 'bg-indigo-50 text-indigo-600'
                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                'group flex items-center px-2 py-2 text-sm font-medium rounded-md'
                            ]"
                        >
                            <svg
                                :class="[
                                    isActiveRoute(item.route)
                                        ? 'text-indigo-600'
                                        : 'text-gray-400 group-hover:text-gray-500',
                                    'mr-3 flex-shrink-0 h-6 w-6'
                                ]"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    :d="item.icon"
                                />
                            </svg>
                            {{ item.name }}
                        </router-link>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useNavigation } from '@/composables/useNavigation';

const { filteredNavigation, isActiveRoute } = useNavigation();
</script> 