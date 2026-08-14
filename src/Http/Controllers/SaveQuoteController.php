<?php

namespace Saccharine\CPQ\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Saccharine\CPQ\Actions\SaveQuoteDraftAction;

class SaveQuoteController extends Controller
{
    public function store(Request $request, SaveQuoteDraftAction $saveQuoteDraft)
    {
        // Validate the HTTP Request
        $validated = $request->validate([
            'quote_id' => 'nullable|uuid',
            'owner_type' => 'required|string',
            'owner_id' => 'required|uuid',
            'configurator_state' => 'required|array',
        ]);

        // Execute the Action
        $quote = $saveQuoteDraft->execute($validated);

        // Handle the HTTP Response
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'quote' => $quote
            ]);
        }

        return back()->with([
            'success' => 'Quote saved successfully.',
            'quote_id' => $quote->id,
        ]);
    }
}