<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMilestone extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'title',
        'due_date',
        'completed_at',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the project this milestone belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Whether this milestone is complete.
     */
    public function getIsCompleteAttribute(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * Whether this milestone is overdue (has a due date in the past and isn't complete).
     */
    public function getIsOverdueAttribute(): bool
    {
        return !$this->is_complete && $this->due_date !== null && $this->due_date->isPast();
    }
}
