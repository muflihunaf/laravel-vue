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
        'birth_date',
        'death_date',
        'nationality',
        'website',
        'email',
        'photo_path',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
    ];

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'authors';

    public function books()
    {
        return $this->hasMany(Book::class, 'author_uuid');
    }

    protected function getAuditTags(): array
    {
        return [
            'type' => 'author',
            'name' => $this->name,
        ];
    }
} 