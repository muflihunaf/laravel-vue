<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            $model->audit('created');
        });

        static::updated(function ($model) {
            $model->audit('updated');
        });

        static::deleted(function ($model) {
            $model->audit('deleted');
        });
    }

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    protected function audit(string $event): void
    {
        $oldValues = $event === 'created' ? null : $this->getOriginal();
        $newValues = $event === 'deleted' ? null : $this->getAttributes();

        $this->audits()->create([
            'user_id' => Auth::user()->uuid ?? null,
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'tags' => $this->getAuditTags(),
        ]);
    }

    protected function getAuditTags(): array
    {
        return [
            'type' => class_basename($this),
            'name' => $this->name ?? $this->title ?? $this->uuid,
        ];
    }
} 