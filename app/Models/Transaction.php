<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference_number',
        'client_id',
        'transaction_type_id',
        'transaction_subtype_id',
        'title',
        'description',
        'status',
        'internal_status',
        'priority',
        'project_name',
        'project_location',
        'city',
        'region',
        'activity_type',
        'activity_code',
        'category',
        'center_request_number',
        'authority_name',
        'authority_reference_number',
        'permit_number',
        'permit_issued_at',
        'permit_expires_at',
        'permit_needs_renewal',
        'assigned_to',
        'technical_manager_id',
        'coordinator_id',
        'financial_user_id',
        'started_at',
        'expected_delivery_at',
        'completed_at',
        'closed_at',
        'cancelled_at',
        'main_drive_link',
        'meetings_drive_link',
        'notes',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'archived_at',
        'archived_by',
        'archive_notes',
    ];

    protected $casts = [
        'permit_issued_at' => 'date',
        'permit_expires_at' => 'date',
        'permit_needs_renewal' => 'boolean',
        'started_at' => 'date',
        'expected_delivery_at' => 'date',
        'completed_at' => 'datetime',
        'closed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'approved_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function transactionType(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function transactionSubtype(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class, 'transaction_subtype_id');
    }
    public function documents(): HasMany
    {
        return $this->hasMany(TransactionDocument::class);
    }
    public function archivedBy(): BelongsTo
{
    return $this->belongsTo(User::class, 'archived_by');
}

public function scopeActive($query)
{
    return $query->whereNull('archived_at');
}

public function scopeArchived($query)
{
    return $query->whereNotNull('archived_at');
}

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function technicalManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technical_manager_id');
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function financialUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'financial_user_id');
    }
}