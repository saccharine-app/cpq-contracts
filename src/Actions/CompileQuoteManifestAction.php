<?php

namespace Saccharine\CPQ\Actions;

use Saccharine\CPQ\Models\CatalogOffering;

class CompileQuoteManifestAction
{
    public function execute(string $ownerType, string $ownerId): array
    {
        // Convert URL-safe owner type back to a class name (e.g., App-Models-Location -> App\Models\Location)
        $parsedOwnerType = str_replace('-', '\\', $ownerType);

        // Eager load the item and the currently active price
        $offerings = CatalogOffering::with(['item', 'prices'])
            ->where('owner_type', urldecode($parsedOwnerType))
            ->where('owner_id', $ownerId)
            ->where('is_active', true)
            ->get();

        $offeringsDictionary = [];
        $itemIdsForTab = [];

        foreach ($offerings as $offering) {
            $currentPrice = $offering->currentPrice();
            
            // Build the flat dictionary (O(1) lookups for the frontend)
            $offeringsDictionary[$offering->id] = [
                'name' => $offering->display_name,
                'price_cents' => $currentPrice ? $currentPrice->price_cents : 0,
                'is_taxable' => $currentPrice->is_taxable_override ?? ($offering->item->default_tax_class !== 'EXEMPT'),
            ];
            
            $itemIdsForTab[] = $offering->id;
        }

        // Build the presentation layer (Hardcoded for the MVP)
        $presentation = [
            'staff_selector' => [
                [
                    'tab_name' => 'General Services',
                    'item_ids' => $itemIdsForTab,
                ]
            ]
        ];

        return [
            'offerings' => $offeringsDictionary,
            'presentation' => $presentation,
        ];
    }
}