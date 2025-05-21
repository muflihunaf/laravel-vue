<template>
  <MainLayout>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold mb-4">Books</h1>
            <ExcelOperations />
        </div>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Header Section -->
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900">Book Management</h1>
                                <p class="mt-1 text-sm text-gray-500">Manage your library's book collection</p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <!-- <button
                                    v-if="hasPermission('view_books')"
                                    @click="showTrash = !showTrash"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    {{ showTrash ? 'Hide Trash' : 'Show Trash' }}
                                </button> -->
                                <button
                                    v-if="hasPermission('create_books')"
                                    @click="showCreateModal = true"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Create Book
                                </button>
                            </div>
                        </div>

                        <!-- Search and Filter Section -->
                        <div v-if="hasPermission('view_books')" class="mb-6">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="flex-1">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <input
                                            v-model="search"
                                            type="text"
                                            placeholder="Search books by title or ISBN..."
                                            class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            @input="fetchBooks"
                                        />
                                    </div>
                                </div>
                                <div class="w-full sm:w-48">
                                    <select
                                        v-model="isAvailable"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        @change="fetchBooks"
                                    >
                                        <option value="">All Status</option>
                                        <option value="1">Available</option>
                                        <option value="0">Not Available</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Books Table -->
                        <div v-if="hasPermission('view_books')" class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                            <div v-if="loading" class="flex justify-center items-center py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                            </div>
                            <table v-else class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0 z-10">
                                    <tr>
                                        <th
                                            v-for="column in columns"
                                            :key="column.field"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            @click="sort(column.field)"
                                        >
                                            <div class="flex items-center space-x-1">
                                                <span>{{ column.label }}</span>
                                                <span v-if="sortField === column.field" class="text-indigo-600">
                                                    {{ sortDirection === 'asc' ? '↑' : '↓' }}
                                                </span>
                                            </div>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="books.length === 0">
                                        <td colspan="7" class="text-center py-8 text-gray-400">
                                            {{ showTrash ? 'No deleted books found.' : 'No books found.' }}
                                        </td>
                                    </tr>
                                    <tr v-for="book in books" :key="book.uuid" class="hover:bg-gray-50 even:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ book.title }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500 line-clamp-2">{{ book.description }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ book.isbn }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                <span
                                                    :key="book.author.uuid"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                                                >
                                                    {{ book.author.name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                <span
                                                    :key="book.category.uuid"
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                                >
                                                    {{ book.category.name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-3">
                                                <button
                                                    v-if="hasPermission('view_books')"
                                                    @click="viewBook(book)"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                    title="View Details"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    v-if="!showTrash && hasPermission('edit_books')"
                                                    @click="editBook(book)"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                    title="Edit Book"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    v-if="!showTrash && hasPermission('delete_books')"
                                                    @click="deleteBook(book)"
                                                    class="text-red-600 hover:text-red-900"
                                                    title="Delete Book"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                                <button
                                                    v-if="showTrash && hasPermission('edit_books')"
                                                    @click="restoreBook(book)"
                                                    class="text-green-600 hover:text-green-900"
                                                    title="Restore Book"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </button>
                                                <button
                                                    v-if="showTrash && hasPermission('delete_books')"
                                                    @click="forceDeleteBook(book)"
                                                    class="text-red-600 hover:text-red-900"
                                                    title="Delete Permanently"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
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
        <div v-if="showCreateModal && (hasPermission('create_books') || hasPermission('edit_books'))" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ editingBook ? 'Edit Book' : 'Create New Book' }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ editingBook ? 'Update the book details below.' : 'Fill in the details to create a new book.' }}
                        </p>
                    </div>
                    <button
                        @click="showCreateModal = false"
                        class="text-gray-400 hover:text-gray-500 focus:outline-none"
                        :disabled="formLoading"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Basic Information Section -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input
                                        v-model="form.title"
                                        type="text"
                                        required
                                        :disabled="formLoading"
                                        class="block w-full rounded-md border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        :class="{ 'border-red-300': errors.title }"
                                        placeholder="Enter book title"
                                    />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title[0] }}</p>
                            </div>

                            <!-- ISBN Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">ISBN <span class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input
                                        v-model="form.isbn"
                                        type="text"
                                        required
                                        :disabled="formLoading"
                                        class="block w-full rounded-md border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        :class="{ 'border-red-300': errors.isbn }"
                                        placeholder="Enter ISBN number"
                                    />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="errors.isbn" class="mt-1 text-sm text-red-600">{{ errors.isbn[0] }}</p>
                            </div>

                            <!-- Publication Year Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Publication Year <span class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input
                                        v-model="form.publication_year"
                                        type="number"
                                        required
                                        min="1800"
                                        :max="new Date().getFullYear() + 1"
                                        :disabled="formLoading"
                                        class="block w-full rounded-md border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        :class="{ 'border-red-300': errors.publication_year }"
                                        placeholder="Enter publication year"
                                    />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <p v-if="errors.publication_year" class="mt-1 text-sm text-red-600">{{ errors.publication_year[0] }}</p>
                            </div>

                            <!-- Availability Select -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Availability <span class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <select
                                        v-model="form.is_available"
                                        :disabled="formLoading"
                                        class="block w-full rounded-md border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    >
                                        <option :value="true">Available</option>
                                        <option :value="false">Not Available</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description Section -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                        <div>
                            <textarea
                                v-model="form.description"
                                rows="3"
                                :disabled="formLoading"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                :class="{ 'border-red-300': errors.description }"
                                placeholder="Enter book description"
                            ></textarea>
                            <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description[0] }}</p>
                        </div>
                    </div>

                    <!-- Relationships Section -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Relationships</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Author <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <DynamicSelect
                                        v-model="form.author_uuid"
                                        :options="authors"
                                        placeholder="Search authors..."
                                        :ajax="true"
                                        :disabled="formLoading"
                                        ajax-url="/api/authors/search"
                                        :class="{ 'border-red-300': errors.author_uuid }"
                                    />
                                </div>
                                <p v-if="errors.author_uuid" class="mt-1 text-sm text-red-600">{{ errors.author_uuid[0] }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <DynamicSelect
                                        v-model="form.category_uuid"
                                        :options="categories"
                                        placeholder="Search categories..."
                                        :ajax="true"
                                        :disabled="formLoading"
                                        ajax-url="/api/categories/search"
                                        :class="{ 'border-red-300': errors.category_uuid }"
                                    />
                                </div>
                                <p v-if="errors.category_uuid" class="mt-1 text-sm text-red-600">{{ errors.category_uuid[0] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload Section -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Book File</h3>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-500 transition-colors duration-200">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload a file</span>
                                        <input
                                            type="file"
                                            @change="handleFileUpload"
                                            class="sr-only"
                                            accept=".pdf,.epub,.mobi"
                                            :disabled="formLoading"
                                        />
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PDF, EPUB, MOBI up to 500KB
                                </p>
                                <p v-if="form.file" class="text-sm text-indigo-600">
                                    Selected: {{ form.file.name }}
                                </p>
                            </div>
                        </div>
                        <p v-if="errors.file" class="mt-1 text-sm text-red-600">{{ errors.file[0] }}</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            :disabled="formLoading"
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="formLoading"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        >
                            <svg v-if="formLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ editingBook ? 'Update Book' : 'Create Book' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useRouter, useRoute } from 'vue-router';
import { usePermissions } from '../composables/usePermissions';
import DynamicSelect from '../components/DynamicSelect.vue';
import ExcelOperations from '../components/ExcelOperations.vue';
import MainLayout from '../layouts/MainLayout.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const books = ref([]);
const authors = ref([]);
const categories = ref([]);
const showCreateModal = ref(false);
const editingBook = ref(null);
const search = ref('');
const authorId = ref('');
const categoryId = ref('');
const currentPage = ref(1);
const sortField = ref('created_at');
const sortDirection = ref('desc');
const router = useRouter();
const showTrash = ref(false);
const isAvailable = ref('');
const { hasPermission } = usePermissions();
const route = useRoute();
const loading = ref(false);
const formLoading = ref(false);
const errors = ref({});

const columns = [
    { field: 'title', label: 'Title' },
    { field: 'description', label: 'Description' },
    { field: 'isbn', label: 'ISBN' },
    { field: 'author.name', label: 'Author' },
    { field: 'category.name', label: 'Category' },
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
    title: '',
    description: '',
    isbn: '',
    publication_year: '',
    author_uuid: '',
    category_uuid: '',
    file: null,
    is_available: true,
});

const fetchBooks = async () => {
    try {
        loading.value = true;
        const params = {
            page: currentPage.value,
            search: search.value,
            is_available: isAvailable.value,
            sort_field: sortField.value.includes('.') ? sortField.value.split('.')[0] : sortField.value,
            sort_direction: sortDirection.value,
            trashed: showTrash.value,
        };

        const response = await axios.get('/api/books', { params });
        books.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            from: response.data.from,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            to: response.data.to,
            total: response.data.total,
        };
    } catch (error) {
        console.error('Error fetching books:', error);
        toast.error('Failed to fetch books. Please try again.');
    } finally {
        loading.value = false;
    }
};

const fetchAuthors = async () => {
    try {
        const response = await axios.get('/api/authors');
        authors.value = response.data.data;
    } catch (error) {
        console.error('Error fetching authors:', error);
    }
};

const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        categories.value = response.data.data;
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
};

const editBook = (book) => {
    editingBook.value = book;
    form.value = {
        title: book.title,
        description: book.description,
        isbn: book.isbn,
        publication_year: book.publication_year,
        author_uuid: book.authors[0]?.uuid || '',
        category_uuid: book.categories[0]?.uuid || '',
        file: null,
        is_available: Boolean(book.is_available),
    };
    showCreateModal.value = true;
};

const deleteBook = async (book) => {
    if (!confirm('Are you sure you want to delete this book?')) return;
    try {
        loading.value = true;
        await axios.delete(`/api/books/${book.uuid}`);
        await fetchBooks();
        toast.success('Book deleted successfully!');
    } catch (error) {
        console.error('Error deleting book:', error);
        toast.error('Failed to delete book. Please try again.');
    } finally {
        loading.value = false;
    }
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Check file size (500KB limit)
        if (file.size > 500 * 1024) {
            alert('File size must be less than 500KB');
            event.target.value = null;
            return;
        }

        // Check file type
        const allowedTypes = ['application/pdf', 'application/epub+zip', 'application/x-mobipocket-ebook'];
        if (!allowedTypes.includes(file.type)) {
            alert('Only PDF, EPUB, and MOBI files are allowed');
            event.target.value = null;
            return;
        }

        form.value.file = file;
    }
};

const downloadBook = async (book) => {
    try {
        const response = await axios.get(`/api/books/${book.uuid}/download`, {
            responseType: 'blob',
        });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `${book.title}.${book.file_path.split('.').pop()}`);
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (error) {
        console.error('Error downloading book:', error);
    }
};

const handleSubmit = async () => {
    try {
        formLoading.value = true;
        errors.value = {};
        
        // Create the request data
        const requestData = {
            title: form.value.title,
            description: form.value.description,
            isbn: form.value.isbn,
            publication_year: form.value.publication_year,
            author_uuid: form.value.author_uuid,
            category_uuid: form.value.category_uuid,
            is_available: Boolean(form.value.is_available)
        };

        if (form.value.file) {
            // If there's a file, use FormData
            const formData = new FormData();
            formData.append('file', form.value.file);
            
            // Add all other fields to FormData
            Object.keys(requestData).forEach(key => {
                formData.append(key, requestData[key]);
            });

            // Set the correct content type for file upload
            const config = {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            };

            if (editingBook.value) {
                await axios.put(`/api/books/${editingBook.value.uuid}`, formData, config);
            } else {
                await axios.post('/api/books', formData, config);
            }
        } else {
            if (editingBook.value) {
                await axios.put(`/api/books/${editingBook.value.uuid}`, requestData);
            } else {
                await axios.post('/api/books', requestData);
            }
        }

        await fetchBooks();
        showCreateModal.value = false;
        toast.success(editingBook.value ? 'Book updated successfully!' : 'Book created successfully!');
        resetForm();
    } catch (error) {
        console.error('Error submitting form:', error);
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        } else {
            toast.error('Failed to save book. Please check your input and try again.');
        }
    } finally {
        formLoading.value = false;
    }
};

const resetForm = () => {
    form.value = {
        title: '',
        description: '',
        isbn: '',
        publication_year: '',
        author_uuid: '',
        category_uuid: '',
        file: null,
        is_available: true,
    };
    errors.value = {};
    editingBook.value = null;
};

const restoreBook = async (book) => {
    if (!confirm('Are you sure you want to restore this book?')) return;
    try {
        loading.value = true;
        await axios.put(`/api/books/${book.uuid}/restore`);
        await fetchBooks();
        toast.success('Book restored successfully!');
    } catch (error) {
        console.error('Error restoring book:', error);
        toast.error('Failed to restore book. Please try again.');
    } finally {
        loading.value = false;
    }
};

const forceDeleteBook = async (book) => {
    if (!confirm('Are you sure you want to delete this book permanently? This action cannot be undone.')) return;
    try {
        loading.value = true;
        await axios.delete(`/api/books/${book.uuid}/force`);
        await fetchBooks();
        toast.success('Book permanently deleted!');
    } catch (error) {
        console.error('Error deleting book permanently:', error);
        toast.error('Failed to permanently delete book. Please try again.');
    } finally {
        loading.value = false;
    }
};

const viewBook = (book) => {
    router.push({ name: 'book-detail', params: { uuid: book.uuid } });
};

const sort = (field) => {
    if (field === sortField.value) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    fetchBooks();
};

watch(() => route.query.edit, (uuid) => {
    if (uuid) {
        const book = books.value.find(b => b.uuid === uuid);
        if (book) {
            editBook(book);
        }
    }
});

onMounted(() => {
    fetchBooks();
    fetchAuthors();
    fetchCategories();
});
</script>