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
    protected $userId;
    protected $model;
    protected $jobUuid;

    public function __construct(string $importClass, array $fields, string $filepath, string $userId, $model = null)
    {
        $this->importClass = $importClass;
        $this->fields = $fields;
        $this->filepath = $filepath;
        $this->userId = $userId;
        $this->model = $model;
        $this->jobUuid = (string) Str::uuid();

        // Create the job record as pending
        ExportImportJob::create([
            'uuid' => $this->jobUuid,
            'user_uuid' => $userId,
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
        if ($job) {
            $job->status = 'processing';
            $job->save();
        }
        try {
            $import = new $this->importClass($this->fields);
            Excel::import($import, $this->filepath);
            Storage::delete($this->filepath);
            if ($job) {
                $job->status = 'completed';
                $job->save();
            }
            // Notify user that import is complete (optional)
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