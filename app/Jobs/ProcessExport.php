<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Models\ExportImportJob;
use Illuminate\Support\Str;

class ProcessExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $exportClass;
    protected $fields;
    protected $filename;
    protected $userId;
    protected $model;
    protected $jobUuid;

    public function __construct(string $exportClass, array $fields, string $filename, string $userId, $model = null)
    {
        $this->exportClass = $exportClass;
        $this->fields = $fields;
        $this->filename = $filename;
        $this->userId = $userId;
        $this->model = $model;
        $this->jobUuid = (string) Str::uuid();

        // Create the job record as pending
        ExportImportJob::create([
            'uuid' => $this->jobUuid,
            'user_uuid' => $userId,
            'type' => 'export',
            'model' => $model ?? class_basename($exportClass),
            'fields' => $fields,
            'filename' => $filename,
            'status' => 'pending',
        ]);
    }

    public function handle()
    {
        $job = ExportImportJob::find($this->jobUuid);
        if ($job) {
            $job->status = 'processing';
            $job->save();
        }
        try {
            $export = new $this->exportClass($this->fields);
            Excel::store($export, "exports/{$this->filename}", 'public');
            if ($job) {
                $job->status = 'completed';
                $job->filepath = "exports/{$this->filename}";
                $job->save();
            }
            // Notify user that export is complete (optional)
        } catch (\Exception $e) {
            if ($job) {
                $job->status = 'failed';
                $job->message = $e->getMessage();
                $job->save();
            }
            throw $e;
        }
    }
} 