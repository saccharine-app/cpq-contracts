<?php

namespace Package\CPQ\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Package\CPQ\Models\CatalogOffering;

class QuoteBuilderController extends Controller
{
    public function create($ownerType, $ownerId)
    {
        // Eager load the item and the currently active price
        $offerings = CatalogOffering::with(['item', 'prices'])
            ->where('owner_type', urldecode($ownerType))
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

        return Inertia::render('CPQ/Selector', [
            'manifest' => [
                'offerings' => $offeringsDictionary,
                'presentation' => $presentation,
            ]
        ]);
    }
}