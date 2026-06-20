<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TransactionTypeDocumentRequirement extends Model
{
    use LogsActivity;
    protected $fillable = [
        'transaction_type_id',
        'name',
        'description',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function transactionType(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class);
    }
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->useLogName('transaction_type_document_requirements')
        ->logFillable()
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
            'created' => 'تم إنشاء متطلب مستند',
            'updated' => 'تم تعديل متطلب مستند',
            'deleted' => 'تم حذف متطلب مستند',
            default => $eventName,
        });
}
}