<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Commission extends Model
{
    use SoftDeletes  , LogsActivity;

    protected $fillable = [
        'transaction_id',
        'commission_number',
        'commission_category',
        'recipient_user_id',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'calculation_type',
        'base_type',
        'percentage',
        'fixed_amount',
        'calculated_amount',
        'currency',
        'due_date',
        'payment_date',
        'status',
        'proof_file_path',
        'drive_link',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'calculated_amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
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
        ->useLogName('commissions')
        ->logFillable()
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
            'created' => 'تم إنشاء العمولة',
            'updated' => 'تم تعديل العمولة',
            'deleted' => 'تم حذف العمولة',
            default => $eventName,
        });
}
}