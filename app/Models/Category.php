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
        'parent_uuid',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    public function books()
    {
        return $this->hasMany(Book::class, 'category_uuid');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_uuid');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_uuid');
    }

    protected function getAuditTags(): array
    {
        return [
            'type' => 'category',
            'name' => $this->name,
        ];
    }
} 