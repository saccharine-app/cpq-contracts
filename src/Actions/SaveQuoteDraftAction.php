<?php

namespace Saccharine\CPQ\Actions;

use Saccharine\CPQ\Models\Quote;

class SaveQuoteDraftAction
{
    public function execute(array $data): Quote
    {
        // Parse URL-safe owner_type back to a namespace if necessary
        $parsedOwnerType = str_replace('-', '\\', $data['owner_type']);

        return Quote::updateOrCreate(
            ['id' => $data['quote_id'] ?? null],
            [
                'owner_type' => $parsedOwnerType,
                'owner_id' => $data['owner_id'],
                'configurator_state' => $data['configurator_state'],
                'status' => 'draft',
            ]
        );
    }
}