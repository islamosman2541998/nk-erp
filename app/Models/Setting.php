<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Setting extends Model
{
    use LogsActivity;
    protected $fillable = [
        'key',
        'value',
        'label',
        'group_name',
        'type',
        'sort_order',
    ];
   public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->useLogName('settings')
        ->logFillable()
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
            'created' => 'تم إنشاء إعداد',
            'updated' => 'تم تعديل إعداد',
            'deleted' => 'تم حذف إعداد',
            default => $eventName,
        });
}
}