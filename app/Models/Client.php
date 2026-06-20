<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Client extends Model
{
    use SoftDeletes  , LogsActivity;

    protected $fillable = [
        'name',
        'facility_name',
        'commercial_registration_number',
        'tax_number',
        'phone',
        'email',
        'contact_person_name',
        'contact_person_phone',
        'contact_person_email',
        'city',
        'region',
        'address',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
   public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->useLogName('clients')
        ->logFillable()
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
            'created' => 'تم إنشاء العميل',
            'updated' => 'تم تعديل العميل',
            'deleted' => 'تم حذف العميل',
            default => $eventName,
        });
}
}