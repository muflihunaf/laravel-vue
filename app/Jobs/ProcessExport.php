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
    protected $userUuid;
    protected $model;
    protected $jobUuid;

    public function __construct(string $exportClass, array $fields, string $filename, string $userUuid, $model = null)
    {
        $this->exportClass = $exportClass;
        $this->fields = $fields;
        $this->filename = $filename;
        $this->userUuid = $userUuid;
        $this->model = $model;
        $this->jobUuid = (string) Str::uuid();

        // Create the job record as pending
        ExportImportJob::create([
            'uuid' => $this->jobUuid,
            'user_uuid' => $this->userUuid,
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
        if (!$job) {
            throw new \Exception('Job record not found');
        }

        try {
            // Update status to processing
            $job->update(['status' => 'processing']);

            // Perform the export
            $export = new $this->exportClass($this->fields);
            $filePath = 'exports/' . $this->filename;
            
            Excel::store($export, $filePath, 'public');

            // Update job status
            $job->update([
                'status' => 'completed',
                'filepath' => $filePath
            ]);

            // Notify user (you can implement your notification system here)
            // For example, using Laravel's notification system or broadcasting

        } catch (\Exception $e) {
            // Update job status with error
            $job->update([
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);

            // Re-throw the exception to trigger job failure
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        // Handle job failure
        // You can implement additional failure handling here
    }
} 