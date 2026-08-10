<?php

namespace Saccharine\CPQ\Console\Commands;

use Illuminate\Console\Command;
use Saccharine\CPQ\Models\CatalogItem;
use Saccharine\CPQ\Models\CatalogOffering;
use Saccharine\CPQ\Models\OfferingPrice;
use Illuminate\Support\Str;

class SeedDemoCatalogCommand extends Command
{
    protected $signature = 'cpq:seed-demo';
    protected $description = 'Seed the CPQ package with demo catalog items and offerings';

    public function handle()
    {
        // Create a Master Item
        $item = CatalogItem::create([
            'sku' => 'SVC-BASIC-01',
            'canonical_name' => 'Basic Services of Funeral Director',
            'default_tax_class' => 'EXEMPT',
        ]);

        // Create the Offering (Bound to a dummy host Location ID)
        $offering = CatalogOffering::create([
            'catalog_item_id' => $item->id,
            'owner_type' => 'App\Models\Location', // Assuming host app namespace
            'owner_id' => Str::uuid(), // In reality, this points to an existing location
            'display_name' => 'Basic Services of Funeral Director & Staff',
            'is_active' => true,
        ]);

        // Set the Price
        OfferingPrice::create([
            'catalog_offering_id' => $offering->id,
            'price_cents' => 150000, // $1,500.00
            'effective_at' => now()->subYear(),
        ]);

        $this->info('Demo catalog seeded successfully!');
    }
}