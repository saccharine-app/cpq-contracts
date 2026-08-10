<?php

namespace Saccharine\CPQ\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferingPrice extends Model
{
    use HasUuids;

    protected $table = 'offering_prices';

    protected $fillable = [
        'catalog_offering_id',
        'price_cents',
        'currency',
        'is_taxable_override',
        'effective_at',
        'ends_at',
        'rules_manifest',
    ];

    protected function casts(): array
    {
        return [
            'price_cents' => 'integer',
            'is_taxable_override' => 'boolean',
            'effective_at' => 'datetime',
            'ends_at' => 'datetime',
            'rules_manifest' => 'array',
        ];
    }

    /**
     * The offering this price belongs to.
     */
    public function offering(): BelongsTo
    {
        return $this->belongsTo(CatalogOffering::class, 'catalog_offering_id');
    }

    /**
     * Scope a query to only include prices active at a given date.
     */
    public function scopeActiveAt(Builder $query, \DateTimeInterface $date): Builder
    {
        return $query->where('effective_at', '<=', $date)
                     ->where(function (Builder $q) use ($date) {
                         $q->whereNull('ends_at')
                           ->orWhere('ends_at', '>', $date);
                     })
                     ->orderByDesc('effective_at');
    }
}