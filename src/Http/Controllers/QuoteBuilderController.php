<?php

namespace Saccharine\CPQ\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Saccharine\CPQ\Actions\CompileQuoteManifestAction;
use Saccharine\CPQ\Models\Quote;

class QuoteBuilderController extends Controller
{
    public function create(Request $request, CompileQuoteManifestAction $compileManifest, $ownerType, $ownerId, $quoteId = null)
{
        // Execute the Action
        $manifest = $compileManifest->execute($ownerType, $ownerId);

        // Handle API vs Web Response
        if ($request->wantsJson()) {
            return response()->json($manifest);
        }

        // Hydrate Existing Quote State
        $parsedOwnerType = str_replace('-', '\\', $ownerType);
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
}