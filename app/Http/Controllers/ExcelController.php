<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ProcessExport;
use App\Jobs\ProcessImport;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BooksExport;
use App\Exports\AuthorsExport;
use App\Exports\CategoriesExport;
use App\Exports\UsersExport;
use App\Imports\BooksImport;
use App\Imports\AuthorsImport;
use App\Imports\CategoriesImport;
use App\Imports\UsersImport;

class ExcelController extends Controller
{
    protected $exportClasses = [
        'books' => BooksExport::class,
        'authors' => AuthorsExport::class,
        'categories' => CategoriesExport::class,
        'users' => UsersExport::class,
    ];

    protected $importClasses = [
        'books' => BooksImport::class,
        'authors' => AuthorsImport::class,
        'categories' => CategoriesImport::class,
        'users' => UsersImport::class,
    ];

    protected $fields = [
        'books' => ['title', 'isbn', 'description', 'published_at', 'author', 'category'],
        'authors' => ['name', 'biography'],
        'categories' => ['name', 'description'],
        'users' => ['name', 'email', 'roles'],
    ];

    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:books,authors,categories,users',
            'fields' => 'array',
            'fields.*' => 'string',
            'template' => 'boolean'
        ]);

        $type = $request->input('type');
        $fields = $request->input('fields', []);
        $isTemplate = $request->input('template', false);

        if (!isset($this->exportClasses[$type])) {
            return response()->json(['error' => 'Invalid export type'], 400);
        }

        // If fields array is empty, use all available fields
        if (empty($fields)) {
            $fields = $this->fields[$type];
        }

        if ($isTemplate) {
            // For templates, we'll do a direct download
            $exportClass = $this->exportClasses[$type];
            $export = new $exportClass($fields, true);
            return Excel::download($export, "{$type}_template.xlsx");
        }

        // For actual exports, use a job
        $filename = sprintf(
            '%s_%s.xlsx',
            $type,
            now()->format('Y-m-d_His')
        );

        ProcessExport::dispatch(
            $this->exportClasses[$type],
            $fields,
            $filename,
            auth()->user()->uuid
        );

        return response()->json([
            'message' => 'Export started. You will be notified when it\'s ready.',
            'filename' => $filename
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'type' => 'required|in:books,authors,categories,users',
            'fields' => 'array',
            'fields.*' => 'string',
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        $type = $request->input('type');
        $fields = $request->input('fields', []);

        // If fields array is empty, use all available fields
        if (empty($fields)) {
            $fields = $this->fields[$type];
        }

        $file = $request->file('file');
        $filepath = $file->store('imports');

        ProcessImport::dispatch(
            $this->importClasses[$type],
            $fields,
            $filepath,
            auth()->user()->uuid
        );

        return response()->json([
            'message' => 'Import started. You will be notified when it\'s complete.'
        ]);
    }

    public function getAvailableFields(Request $request)
    {
        $request->validate([
            'type' => 'required|in:books,authors,categories,users'
        ]);

        return response()->json([
            'fields' => $this->fields[$request->type]
        ]);
    }
} 