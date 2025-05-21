<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasUuids, Auditable, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_category', 'category_uuid', 'book_uuid');
    }

    protected function getAuditTags(): array
    {
        return [
            'type' => 'category',
            'name' => $this->name,
        ];
    }
} 