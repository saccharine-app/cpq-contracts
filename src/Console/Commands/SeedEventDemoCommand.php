<?php

namespace Saccharine\CPQ\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Saccharine\CPQ\Models\CatalogItem;
use Saccharine\CPQ\Models\CatalogOffering;
use Saccharine\CPQ\Models\OfferingPrice;
use Saccharine\CPQ\Models\DemoContext; // A simple dummy model in your package

class SeedEventDemoCommand extends Command
{
    protected $signature = 'cpq:seed-events {--fresh}';
    protected $description = 'Seed an agnostic Event Planning catalog';

    public function handle()
    {
        // ... (Truncate logic here) ...

        // Create a dummy context for the URL
        $context = DemoContext::create(['name' => 'Downtown Banquet Hall']);
        $ownerId = $context->id;
        $ownerType = 'context'; // Maps via Relation::enforceMorphMap

        $demoItems = [
            // Services (Tab 1)
            ['sku' => 'EVT-VEN-01', 'name' => 'Grand Ballroom Rental (4 Hours)', 'tax' => 'STANDARD', 'price' => 250000, 'cat' => 'Services & Staffing'],
            ['sku' => 'EVT-STF-01', 'name' => 'Event Coordinator (Per Hour)', 'tax' => 'STANDARD', 'price' => 7500, 'cat' => 'Services & Staffing'],
            
            // Merchandise (Tab 2)
            ['sku' => 'CAT-DIN-01', 'name' => 'Plated Dinner (Per Person)', 'tax' => 'STANDARD', 'price' => 8500, 'cat' => 'Food, Beverage & Rentals'],
            ['sku' => 'CAT-BAR-01', 'name' => 'Open Bar Package (Per Person)', 'tax' => 'STANDARD', 'price' => 4500, 'cat' => 'Food, Beverage & Rentals'],
            ['sku' => 'RNT-CHAIR-01', 'name' => 'Chiavari Chair Rental (Each)', 'tax' => 'STANDARD', 'price' => 850, 'cat' => 'Food, Beverage & Rentals'],
            
            // Disbursements / Third-Party (Tab 3)
            ['sku' => 'DISB-DJ-01', 'name' => 'Outsourced DJ Entertainment', 'tax' => 'DISBURSEMENT', 'price' => 80000, 'cat' => 'Permits & Outsourced (Cash Advances)'],
            ['sku' => 'DISB-PERM-01', 'name' => 'City Noise Exemption Permit', 'tax' => 'EXEMPT', 'price' => 12500, 'cat' => 'Permits & Outsourced (Cash Advances)'],
        ];

        foreach ($demoItems as $data) {
            $item = CatalogItem::create([
                'sku' => $data['sku'],
                'canonical_name' => $data['name'],
                'default_tax_class' => $data['tax'],
                'attributes' => ['category' => $data['cat']]
            ]);

            $offering = CatalogOffering::create([
                'catalog_item_id' => $item->id,
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'display_name' => $data['name'],
            ]);

            OfferingPrice::create([
                'catalog_offering_id' => $offering->id,
                'price_cents' => $data['price'],
                'effective_at' => now()->subMonth(),
            ]);
        }

        $this->info('Event catalog seeded! Test UI at: /cpq/selector/context/' . $ownerId);
    }
}