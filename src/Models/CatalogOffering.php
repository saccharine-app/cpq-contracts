<?php

namespace Package\CPQ\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CatalogOffering extends Model
{
    use HasUuids;

    protected $table = 'catalog_offerings';

    protected $fillable = [
        'catalog_item_id',
        'owner_type',
        'owner_id',
        'display_name',
        'display_description',
        'local_code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * The master catalog item this offering aliases.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class, 'catalog_item_id');
    }

    /**
     * The host application model that owns this offering (e.g., Location).
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The temporal pricing history for this offering.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(OfferingPrice::class);
    }

    /**
     * Helper to retrieve the active price for a specific date (defaults to now).
     */
    public function currentPrice(\DateTimeInterface $date = null): ?OfferingPrice
    {
        return $this->prices()->activeAt($date ?? now())->first();
    }
}