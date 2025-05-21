<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessExport;
use App\Jobs\ProcessImport;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ExcelController extends Controller
{
    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:books,authors,categories',
            'fields' => 'required|array',
            'fields.*' => 'required|string'
        ]);

        $exportClasses = [
            'books' => \App\Exports\BooksExport::class,
            'authors' => \App\Exports\AuthorsExport::class,
            'categories' => \App\Exports\CategoriesExport::class,
        ];

        $filename = sprintf(
            '%s_%s.xlsx',
            $request->type,
            now()->format('Y-m-d_His')
        );

        ProcessExport::dispatch(
            $exportClasses[$request->type],
            $request->fields,
            $filename,
            auth()->id()
        );

        return response()->json([
            'message' => 'Export started. You will be notified when it\'s ready.',
            'filename' => $filename
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'type' => 'required|in:books,authors,categories',
            'fields' => 'required|array',
            'fields.*' => 'required|string',
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        $importClasses = [
            'books' => \App\Imports\BooksImport::class,
            'authors' => \App\Imports\AuthorsImport::class,
            'categories' => \App\Imports\CategoriesImport::class,
        ];

        $file = $request->file('file');
        $filepath = $file->store('imports');

        ProcessImport::dispatch(
            $importClasses[$request->type],
            $request->fields,
            $filepath,
            auth()->id()
        );

        return response()->json([
            'message' => 'Import started. You will be notified when it\'s complete.'
        ]);
    }

    public function getAvailableFields(Request $request)
    {
        $request->validate([
            'type' => 'required|in:books,authors,categories'
        ]);

        $fields = [
            'books' => [
                'title',
                'isbn',
                'description',
                'published_at',
                'author',
                'categories'
            ],
            'authors' => [
                'name',
                'biography',
                'birth_date',
                'death_date'
            ],
            'categories' => [
                'name',
                'description'
            ]
        ];

        return response()->json([
            'fields' => $fields[$request->type]
        ]);
    }
} 