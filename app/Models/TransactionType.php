<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionType extends Model
{
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

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}