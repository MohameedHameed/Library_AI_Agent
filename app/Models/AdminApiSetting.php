<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminApiSetting extends Model
{
    protected $fillable = [
        'api_name',
        'display_name',
        'api_key',
        'api_url',
        'status',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Get the admin who approved this setting.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if this API is currently active/approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if this API is disabled.
     */
    public function isDisabled(): bool
    {
        return $this->status === 'disabled';
    }

    /**
     * Check if this API is pending approval.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Status badge color helper for views.
     */
    public function statusColor(): string
    {
        return match($this->status) {
            'approved' => 'green',
            'disabled' => 'red',
            default    => 'yellow',
        };
    }
}
