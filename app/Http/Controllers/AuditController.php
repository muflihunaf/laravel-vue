<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request, string $modelType, string $modelId)
    {
        $audits = Audit::where('auditable_type', $modelType)
            ->where('auditable_id', $modelId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($audits);
    }

    public function show(Audit $audit)
    {
        return response()->json($audit->load('user'));
    }
} 