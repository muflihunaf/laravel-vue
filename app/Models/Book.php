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
        'author_uuid',
        'category_uuid',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'publication_year' => 'integer',
    ];

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    public function author()
    {
        return $this->belongsTo(Author::class, 'author_uuid');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_uuid');
    }

    protected function getAuditTags(): array
    {
        return [
            'type' => 'book',
            'title' => $this->title,
        ];
    }
} 