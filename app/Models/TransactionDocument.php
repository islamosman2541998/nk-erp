<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TransactionDocument extends Model
{
    use LogsActivity;
    protected $fillable = [
        'transaction_id',
        'document_requirement_id',
        'name',
        'status',
        'file_path',
        'drive_link',
        'uploaded_by',
        'uploaded_at',
        'reviewed_by',
        'reviewed_at',
        'notes',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(TransactionTypeDocumentRequirement::class, 'document_requirement_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->useLogName('transaction_documents')
        ->logFillable()
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
            'created' => 'تم إنشاء مستند معاملة',
            'updated' => 'تم تعديل مستند معاملة',
            'deleted' => 'تم حذف مستند معاملة',
            default => $eventName,
        });
}
}