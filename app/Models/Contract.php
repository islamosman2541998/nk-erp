<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Contract extends Model
{
    use SoftDeletes  , LogsActivity;

    protected $fillable = [
        'transaction_id',
        'contract_number',
        'contract_date',
        'contract_value',
        'currency',
        'status',
        'file_path',
        'drive_link',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'contract_value' => 'decimal:2',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function payments(): HasMany
{
    return $this->hasMany(Payment::class);
}

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
   public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->useLogName('contracts')
        ->logFillable()
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
            'created' => 'تم إنشاء العقد',
            'updated' => 'تم تعديل العقد',
            'deleted' => 'تم حذف العقد',
            default => $eventName,
        });
}
}