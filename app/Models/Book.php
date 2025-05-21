<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasUuids, Auditable, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'isbn',
        'file_path',
        'publication_year',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'publication_year' => 'integer',
    ];

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    protected function getAuditTags(): array
    {
        return [
            'type' => 'book',
            'title' => $this->title,
        ];
    }
} 