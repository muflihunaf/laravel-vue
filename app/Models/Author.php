<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use HasUuids, Auditable, SoftDeletes;

    protected $fillable = [
        'name',
        'biography',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'authors';

    public function books()
    {
        return $this->belongsToMany(Book::class, 'author_book', 'author_uuid', 'book_uuid');
    }

    protected function getAuditTags(): array
    {
        return [
            'type' => 'author',
            'name' => $this->name,
        ];
    }
} 