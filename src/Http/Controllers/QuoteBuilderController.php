<?php

namespace Saccharine\CPQ\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Saccharine\CPQ\Models\CatalogOffering;
use Saccharine\CPQ\Models\Quote;

class QuoteBuilderController extends Controller
{
    public function create(Request $request, $ownerType, $ownerId, $quoteId = null)
    {
        $manifest = $this->compileManifest($ownerType, $ownerId);
        $parsedOwnerType = str_replace('-', '\\', $ownerType);

        if ($request->wantsJson()) {
            return response()->json($manifest);
        }

        // Fetch the existing quote if an ID was provided
        $existingState = null;
        $validQuoteId = null;

        if ($quoteId) {
            $quote = Quote::where('id', $quoteId)
                ->where('owner_type', $parsedOwnerType)
                ->where('owner_id', $ownerId)
                ->first();

            if ($quote) {
                $existingState = $quote->configurator_state;
                $validQuoteId = $quote->id;
            }
        }

        return Inertia::render('CPQ/Selector', [
            'ownerType' => $ownerType,
            'ownerId' => $ownerId,
            'existingQuoteId' => $validQuoteId,
            'existingState' => $existingState,
            'manifest' => $manifest
        ]);
    }

    protected function compileManifest($ownerType, $ownerId)
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
