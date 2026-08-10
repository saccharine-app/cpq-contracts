<?php

namespace Saccharine\CPQ\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Package\CPQ\Models\Quote;

class SaveQuoteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'quote_id' => 'nullable|uuid',
            'owner_type' => 'required|string',
            'owner_id' => 'required|uuid',
            'configurator_state' => 'required|array',
        ]);

        // Parse URL-safe owner_type back to a namespace if necessary
        $parsedOwnerType = str_replace('-', '\\', $validated['owner_type']);

        $quote = Quote::updateOrCreate(
            ['id' => $validated['quote_id']],
            [
                'owner_type' => $parsedOwnerType,
                'owner_id' => $validated['owner_id'],
                'configurator_state' => $validated['configurator_state'],
                'status' => 'draft',
            ]
        );

        // Redirect back with the quote_id so the frontend knows it's now working on a saved draft
        return back()->with([
            'success' => 'Quote saved successfully.',
            'quote_id' => $quote->id,
        ]);
    }
}