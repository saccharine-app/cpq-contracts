<?php

namespace Saccharine\CPQ\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Quote extends Model
{
    use HasUuids;

    protected $table = 'quotes';

    protected $fillable = [
        'owner_type',
        'owner_id',
        'configurator_state',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'configurator_state' => 'array',
        ];
    }

    /**
     * The host application model that owns this quote (e.g., Location, CaseFile).
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}