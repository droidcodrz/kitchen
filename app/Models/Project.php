<?php

namespace App\Models;

use App\Models\Concerns\HasActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes, HasActivityLog;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_no',
        'name',
        'slug',
        'client_id',
        'project_manager_id',
        'status',
        'proposal_signed_date',
        'delivery_date',
        'production_deadline',
        'actual_delivery_date',
        'description',
        'notes',
        'labels',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'proposal_signed_date' => 'date',
            'delivery_date' => 'date',
            'production_deadline' => 'date',
            'actual_delivery_date' => 'date',
            'labels' => 'array',
        ];
    }

    /**
     * Get the client for this project.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the project manager for this project.
     */
    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    /**
     * Get the products included in this project.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'project_product')
            ->withPivot('quantity', 'unit_price_at_time', 'notes');
    }

    /**
     * Get the teams assigned to this project.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'project_team')
            ->withPivot('assigned_at');
    }

    /**
     * Get the users (members) assigned to this project.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('assigned_at');
    }

    /**
     * Get the attachments for this project.
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the calendar events for this project.
     */
    public function calendarEvents(): MorphMany
    {
        return $this->morphMany(CalendarEvent::class, 'eventable');
    }

    /**

     * Get the milestones for this project.

     */

    public function milestones(): HasMany

    {

        return $this->hasMany(ProjectMilestone::class)->orderBy('sort_order')->orderBy('due_date');

    }

}
