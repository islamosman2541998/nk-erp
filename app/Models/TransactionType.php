<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TransactionType extends Model
{
    use LogsActivity;
    protected $fillable = [
        'parent_id',
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(TransactionType::class, 'parent_id');
    }

    public function documentRequirements(): HasMany
    {
        return $this->hasMany(TransactionTypeDocumentRequirement::class);
    }
 public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->useLogName('transaction_types')
        ->logFillable()
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
            'created' => 'تم إنشاء نوع معاملة',
            'updated' => 'تم تعديل نوع معاملة',
            'deleted' => 'تم حذف نوع معاملة',
            default => $eventName,
        });
}

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}