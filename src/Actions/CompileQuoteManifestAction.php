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
        $tabs = [
            'Services & Staffing' => [],
            'Food, Beverage & Rentals' => [],
            'Permits & Outsourced (Cash Advances)' => []
        ];

        foreach ($offerings as $offering) {
            $currentPrice = $offering->currentPrice();
            $category = $offering->item->attributes['category'] ?? 'Services & Staffing';
            
            $offeringsDictionary[$offering->id] = [
                'name' => $offering->display_name,
                'price_cents' => $currentPrice ? $currentPrice->price_cents : 0,
                'is_taxable' => $currentPrice->is_taxable_override ?? ($offering->item->default_tax_class !== 'EXEMPT'),
            ];
            
            if (isset($tabs[$category])) {
                $tabs[$category][] = $offering->id;
            }
        }

        $presentation = ['staff_selector' => []];
        foreach ($tabs as $tabName => $itemIds) {
            if (!empty($itemIds)) {
                $presentation['staff_selector'][] = [
                    'tab_name' => $tabName,
                    'item_ids' => $itemIds,
                ];
            }
        }

        return [
            'offerings' => $offeringsDictionary,
            'presentation' => $presentation,
        ];
    }
}