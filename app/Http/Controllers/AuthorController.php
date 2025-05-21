<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $query = Author::with('books')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->is_active !== null, function ($query) use ($request) {
                $query->where('is_active', $request->is_active);
            });

        if ($request->sort_field) {
            $query->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
        } else {
            $query->latest();
        }

        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:authors,name',
            'biography' => 'nullable|string',
            'metadata' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $author = Author::create($validated);

        return response()->json($author, 201);
    }

    public function show(Author $author)
    {
        return response()->json($author->load('books'));
    }

    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('authors')->ignore($author->uuid, 'uuid')],
            'biography' => 'nullable|string',
            'metadata' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $author->update($validated);

        return response()->json($author);
    }

    public function destroy(Author $author)
    {
        $author->delete();
        return response()->json(['message' => 'Author deleted successfully']);
    }

    public function restore($uuid)
    {
        $author = Author::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $author->restore();
        return response()->json(['message' => 'Author restored successfully']);
    }

    public function forceDelete($uuid)
    {
        $author = Author::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $author->forceDelete();
        return response()->json(['message' => 'Author permanently deleted']);
    }

    public function search(Request $request)
    {
        $query = Author::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $authors = $query->paginate(10);

        return response()->json($authors);
    }
} 