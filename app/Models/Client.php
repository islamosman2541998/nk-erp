<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use SoftDeletes;

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
}