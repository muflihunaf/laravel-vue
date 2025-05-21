<?php

namespace App\Http\Controllers;

use App\Models\ExportImportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExportImportJobController extends Controller
{
    // List jobs for the current user
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = ExportImportJob::where('user_uuid', $user->uuid)
            ->orderByDesc('created_at');

        if ($request->has('type') && !blank($request->type)) {
            $query->where('type', $request->type);
        }
        if ($request->has('model') && !blank($request->model)) {
            $query->where('model', $request->model);
        }
        if ($request->has('status') && !blank($request->status)) {
            $query->where('status', $request->status);
        }

        $jobs = $query->paginate(20);
        return response()->json($jobs);
    }

    // Show details for a specific job
    public function show($uuid)
    {
        $job = ExportImportJob::where('uuid', $uuid)
            ->where('user_uuid', Auth::user()->uuid)
            ->firstOrFail();
        return response()->json($job);
    }

    // Download exported file (if completed)
    public function download($uuid)
    {
        $job = ExportImportJob::where('uuid', $uuid)
            ->where('user_uuid', Auth::user()->uuid)
            ->where('type', 'export')
            ->where('status', 'completed')
            ->firstOrFail();
        if (!$job->filepath || !Storage::disk('public')->exists($job->filepath)) {
            abort(404, 'File not found');
        }
        $fullPath = storage_path('app/public/' . $job->filepath);
        $filename = $job->filename ?? basename($job->filepath);
        return response()->download($fullPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
} 