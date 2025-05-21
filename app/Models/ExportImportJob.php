<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ExportImportJob extends Model
{
    use HasUuids;

    protected $table = 'export_import_jobs';
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'user_uuid',
        'type',
        'model',
        'fields',
        'filename',
        'filepath',
        'status',
        'message',
    ];

    protected $casts = [
        'fields' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
} 