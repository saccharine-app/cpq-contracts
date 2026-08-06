<?php

namespace Package\CPQ\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogItem extends Model
{
    use HasUuids;

    protected $table = 'catalog_items';

    protected $fillable = [
        'sku',
        'canonical_name',
        'accounting_code',
        'default_tax_class',
        'attributes',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
        ];
    }

    /**
     * Get all local offerings/aliases for this master item.
     */
    public function offerings(): HasMany
    {
        return $this->hasMany(CatalogOffering::class);
    }
}