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
        $query = Book::with(['author', 'category'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->when($request->is_available !== null, function ($query) use ($request) {
                $query->where('is_available', $request->is_available);
            })
            ->when($request->sort_field == 'author', function ($query) use ($request) {
                $query->leftJoin('authors', 'books.author_uuid', '=', 'authors.uuid')
                      ->orderBy('authors.name', $request->sort_direction ?? 'asc')
                      ->select('books.*');
            })
            ->when($request->sort_field == 'category', function ($query) use ($request) {
                $query->leftJoin('categories', 'books.category_uuid', '=', 'categories.uuid')
                      ->orderBy('categories.name', $request->sort_direction ?? 'asc')
                      ->select('books.*');
            })
            ->when($request->sort_field && !in_array($request->sort_field, ['author', 'category']), function ($query) use ($request) {
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
            'is_available' => 'required|boolean',
            'author_uuid' => 'required|exists:authors,uuid',
            'category_uuid' => 'required|exists:categories,uuid',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('books', 'public');
            $validated['file_path'] = $path;
        }

        $book = Book::create($validated);

        return response()->json($book->load(['author', 'category']), 201);
    }

    public function show(Book $book)
    {
        return response()->json($book->load(['author', 'category']));
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
            'author_uuid' => 'required|exists:authors,uuid',
            'category_uuid' => 'required|exists:categories,uuid',
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

        return response()->json($book->load(['author', 'category']));
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
            return response()->json(['message' => 'No file available for download'], 404);
        }

        $path = Storage::disk('public')->path($book->file_path);
        return response()->download($path);
    }
} 