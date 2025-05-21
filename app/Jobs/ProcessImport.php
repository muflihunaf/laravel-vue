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

class ProcessImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $importClass;
    protected $fields;
    protected $filepath;
    protected $userUuid;
    protected $model;
    protected $jobUuid;

    public function __construct(string $importClass, array $fields, string $filepath, string $userUuid, $model = null)
    {
        $this->importClass = $importClass;
        $this->fields = $fields;
        $this->filepath = $filepath;
        $this->userUuid = $userUuid;
        $this->model = $model;
        $this->jobUuid = (string) Str::uuid();

        // Create the job record as pending
        ExportImportJob::create([
            'uuid' => $this->jobUuid,
            'user_uuid' => $this->userUuid,
            'type' => 'import',
            'model' => $model ?? class_basename($importClass),
            'fields' => $fields,
            'filename' => basename($filepath),
            'filepath' => $filepath,
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

            // Perform the import
            $import = new $this->importClass($this->fields);
            Excel::import($import, Storage::path($this->filepath));

            // Update job status
            $job->update([
                'status' => 'completed'
            ]);

            // Clean up the temporary file
            Storage::delete($this->filepath);

            // Notify user (you can implement your notification system here)
            // For example, using Laravel's notification system or broadcasting

        } catch (\Exception $e) {
            // Update job status with error
            $job->update([
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);

            // Clean up the temporary file
            if (Storage::exists($this->filepath)) {
                Storage::delete($this->filepath);
            }

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