<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with(['authors', 'categories'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->when($request->is_available !== null, function ($query) use ($request) {
                $query->where('is_available', $request->is_available);
            })
            ->when($request->sort_field == 'authors', function ($query) use ($request) {
                $query->leftJoin('author_book', 'books.uuid', '=', 'author_book.book_uuid')
                      ->leftJoin('authors', 'authors.uuid', '=', 'author_book.author_uuid')
                      ->orderBy('authors.name', $request->sort_direction ?? 'asc')
                      ->select('books.*');
            })
            ->when($request->sort_field == 'categories', function ($query) use ($request) {
                $query->leftJoin('book_category', 'books.uuid', '=', 'book_category.book_uuid')
                      ->leftJoin('categories', 'categories.uuid', '=', 'book_category.category_uuid')
                      ->orderBy('categories.name', $request->sort_direction ?? 'asc')
                      ->select('books.*');
            })
            ->when($request->sort_field && !in_array($request->sort_field, ['authors', 'categories']), function ($query) use ($request) {
                $query->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
            })
            ->when(!$request->sort_field, function ($query) {
                $query->latest();
            });

        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isbn' => 'required|string|unique:books,isbn',
            'publication_year' => 'required|integer|min:1800|max:' . (date('Y') + 1),
            'file' => 'nullable|file|mimes:pdf,epub,mobi|max:500',
            'metadata' => 'nullable|array',
            'is_available' => 'required',
            'authors' => 'required|array|min:1',
            'authors.*' => 'exists:authors,uuid',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,uuid',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('books', 'public');
            $validated['file_path'] = $path;
        }

        // check if is_available is true or false
        if ($validated['is_available'] == 'true') {
            $validated['is_available'] = 1;
        } else {
            $validated['is_available'] = 0;
        }

        $book = Book::create($validated);
        $book->authors()->sync($validated['authors']);
        $book->categories()->sync($validated['categories']);

        return response()->json($book->load(['authors', 'categories']), 201);
    }

    public function show(Book $book)
    {
        return response()->json($book->load(['authors', 'categories']));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isbn' => ['required', 'string', Rule::unique('books')->ignore($book->uuid, 'uuid')],
            'publication_year' => 'required|integer|min:1800|max:' . (date('Y') + 1),
            'file' => 'nullable|file|mimes:pdf,epub,mobi|max:500',
            'metadata' => 'nullable|array',
            'is_available' => 'required|boolean',
            'authors' => 'required|array|min:1',
            'authors.*' => 'exists:authors,uuid',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,uuid',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($book->file_path) {
                Storage::disk('public')->delete($book->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('books', 'public');
            $validated['file_path'] = $path;
        }

        $book->update($validated);
        $book->authors()->sync($validated['authors']);
        $book->categories()->sync($validated['categories']);

        return response()->json($book->load(['authors', 'categories']));
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(['message' => 'Book deleted successfully']);
    }

    public function restore($uuid)
    {
        $book = Book::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $book->restore();
        return response()->json(['message' => 'Book restored successfully']);
    }

    public function forceDelete($uuid)
    {
        $book = Book::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $book->forceDelete();
        return response()->json(['message' => 'Book permanently deleted']);
    }

    public function download(Book $book)
    {
        if (!$book->file_path) {
            return response()->json(['message' => 'No file available'], 404);
        }

        return Storage::disk('public')->download($book->file_path);
    }
} 