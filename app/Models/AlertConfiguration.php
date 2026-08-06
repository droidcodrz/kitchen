<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertConfiguration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'alert_type',
        'threshold_value',
        'is_enabled',
        'notify_roles',
        'notify_via_email',
        'always_notify_emails',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'threshold_value' => 'decimal:2',
            'is_enabled' => 'boolean',
            'notify_roles' => 'array',
            'notify_via_email' => 'boolean',
            'always_notify_emails' => 'array',
        ];
    }
}
