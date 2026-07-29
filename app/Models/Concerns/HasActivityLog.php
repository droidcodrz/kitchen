<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasActivityLog
{
    /**
     * Boot the trait and register model event listeners for activity logging.
     */
    public static function bootHasActivityLog(): void
    {
        static::created(fn ($model) => $model->logActivity('created'));

        static::updated(fn ($model) => $model->logActivity('updated', $model->getChanges(), $model->getOriginal()));

        static::deleted(fn ($model) => $model->logActivity('deleted'));
    }

    /**
     * Get the activity logs for this model.
     */
    public function activities(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    /**
     * Log an activity for this model.
     */
    protected function logActivity(string $action, array $new = [], array $old = []): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => get_class($this),
            'subject_id' => $this->id,
            'properties' => !empty($new) || !empty($old) ? ['old' => $old, 'new' => $new] : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
