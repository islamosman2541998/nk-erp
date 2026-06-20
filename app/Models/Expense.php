<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Expense extends Model
{
    use SoftDeletes , LogsActivity;

    protected $fillable = [
        'transaction_id',
        'expense_number',
        'category',
        'title',
        'amount',
        'currency',
        'expense_date',
        'paid_to',
        'payment_method',
        'status',
        'receipt_file_path',
        'drive_link',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
   public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->useLogName('expenses')
        ->logFillable()
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
            'created' => 'تم إنشاء المصروف',
            'updated' => 'تم تعديل المصروف',
            'deleted' => 'تم حذف المصروف',
            default => $eventName,
        });
}
}