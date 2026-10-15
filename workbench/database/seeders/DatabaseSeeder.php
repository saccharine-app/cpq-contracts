<?php

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Saccharine\CPQ\Models\CatalogItem;
use Saccharine\CPQ\Models\CatalogOffering;
use Saccharine\CPQ\Models\DemoContext;
use Saccharine\CPQ\Models\OfferingPrice;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed a demo context (the morph-map "context" owner) with a catalog item,
     * a locally-scoped offering, and an active price. This gives the manifest
     * and selector routes real data to compile inside Workbench and in tests.
     */
    public function run(): void
    {
        $context = DemoContext::firstOrCreate(
            ['name' => 'Workbench Demo Funeral Home'],
        );

        $item = CatalogItem::firstOrCreate(
            ['sku' => 'WB-CHAIR-001'],
            [
                'canonical_name' => 'Family Chair (Row Seating)',
                'accounting_code' => '4100',
                'default_tax_class' => 'STANDARD',
                'attributes' => ['category' => 'Food, Beverage & Rentals'],
            ],
        );

        // Creating through the morph relation stores the enforced alias
        // ('context') in owner_type, matching the URL convention used by the
        // manifest/selector routes.
        $offering = CatalogOffering::where('catalog_item_id', $item->id)
            ->where('owner_id', $context->id)
            ->first();

        if (! $offering) {
            $offering = new CatalogOffering([
                'display_name' => 'Family Chair Rental',
                'display_description' => 'One padded chair for the service hall.',
                'local_code' => 'CHAIR-RENTAL',
                'is_active' => true,
            ]);
            $offering->catalog_item_id = $item->id;
            $offering->owner()->associate($context);
            $offering->save();
        }

        OfferingPrice::firstOrCreate(
            ['catalog_offering_id' => $offering->id],
            [
                'price_cents' => 1500,
                'currency' => 'CAD',
                'is_taxable_override' => null,
                'effective_at' => now()->subDay(),
                'ends_at' => null,
                'rules_manifest' => [],
            ],
        );
    }
}
