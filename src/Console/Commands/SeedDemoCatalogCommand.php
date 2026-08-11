<?php

namespace Saccharine\CPQ\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Saccharine\CPQ\Models\CatalogItem;
use Saccharine\CPQ\Models\CatalogOffering;
use Saccharine\CPQ\Models\OfferingPrice;

class SeedDemoCatalogCommand extends Command
{
    protected $signature = 'cpq:seed-demo {--fresh : Truncate the tables before seeding}';
    protected $description = 'Seed the CPQ package with a robust demo catalog for UI testing';

    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('Truncating existing catalog data...');
            OfferingPrice::query()->delete();
            CatalogOffering::query()->delete();
            CatalogItem::query()->delete();
        }

        // Generate a single mock owner ID for this location
        $ownerId = Str::uuid()->toString();
        $ownerType = 'App\Models\Location';
        $effectiveDate = now()->subMonth();

        $demoItems = [
            // Professional Services
            ['sku' => 'SVC-BASIC-01', 'name' => 'Basic Services of Funeral Director & Staff', 'tax' => 'EXEMPT', 'price' => 150000],
            ['sku' => 'SVC-EMB-01', 'name' => 'Embalming', 'tax' => 'STANDARD', 'price' => 69500],
            ['sku' => 'SVC-DRESS-01', 'name' => 'Dressing, Casketing & Cosmetology', 'tax' => 'STANDARD', 'price' => 22500],
            
            // Facilities & Vehicles
            ['sku' => 'FAC-VIEW-01', 'name' => 'Use of Facilities and Staff for Viewing', 'tax' => 'STANDARD', 'price' => 45000],
            ['sku' => 'FAC-CER-01', 'name' => 'Use of Facilities for Funeral Ceremony', 'tax' => 'STANDARD', 'price' => 50000],
            ['sku' => 'VEH-HRSE-01', 'name' => 'Hearse', 'tax' => 'STANDARD', 'price' => 35000],
            ['sku' => 'VEH-LIMO-01', 'name' => 'Limousine', 'tax' => 'STANDARD', 'price' => 30000],

            // Merchandise
            ['sku' => 'CASKET-OAK-01', 'name' => 'Solid Oak Casket - Natural Finish', 'tax' => 'STANDARD', 'price' => 249500],
            ['sku' => 'CASKET-STL-01', 'name' => '18-Gauge Steel Casket - Blue', 'tax' => 'STANDARD', 'price' => 189500],
            ['sku' => 'CASKET-RNT-01', 'name' => 'Hardwood Rental Casket (Includes Insert)', 'tax' => 'STANDARD', 'price' => 99500],
            ['sku' => 'URN-BRS-01', 'name' => 'Classic Brass Urn', 'tax' => 'STANDARD', 'price' => 29500],
            ['sku' => 'URN-WD-01', 'name' => 'Handcrafted Cherry Wood Urn', 'tax' => 'STANDARD', 'price' => 35000],
            
            // Disbursements (Pass-through items)
            ['sku' => 'DISB-CREM-01', 'name' => 'Crematory Fee', 'tax' => 'DISBURSEMENT', 'price' => 45000],
            ['sku' => 'DISB-COR-01', 'name' => 'Coroner Cremation Certificate', 'tax' => 'DISBURSEMENT', 'price' => 7500],
            ['sku' => 'DISB-OBIT-01', 'name' => 'Newspaper Obituary Notice (Estimate)', 'tax' => 'DISBURSEMENT', 'price' => 25000],
        ];
        
        $this->withProgressBar($demoItems, function ($data) use ($ownerId, $ownerType, $effectiveDate) {
            
            // Create the Master Item
            $item = CatalogItem::create([
                'sku' => $data['sku'],
                'canonical_name' => $data['name'],
                'default_tax_class' => $data['tax'],
            ]);

            // Create the Local Offering
            $offering = CatalogOffering::create([
                'catalog_item_id' => $item->id,
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'display_name' => $data['name'], // Using the same name for local display
                'is_active' => true,
            ]);

            // Set the Effective Price
            OfferingPrice::create([
                'catalog_offering_id' => $offering->id,
                'price_cents' => $data['price'],
                'effective_at' => $effectiveDate,
            ]);

        });

        $this->newLine(2);
        $this->info('Demo catalog seeded successfully!');
        
        $this->comment('You can test the UI by visiting this URL:');
        
        // Format the class name for the URL route parameter (App\Models\Location -> App-Models-Location)
        $urlSafeOwnerType = str_replace('\\', '-', $ownerType);
        
        $this->line("http://localhost:8080/cpq/selector/{$urlSafeOwnerType}/{$ownerId}");
    }
}
