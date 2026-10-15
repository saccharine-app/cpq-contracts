<?php

use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia;
use Saccharine\CPQ\CpqServiceProvider;
use Saccharine\CPQ\Models\CatalogOffering;
use Saccharine\CPQ\Models\DemoContext;
use Saccharine\CPQ\Models\Quote;
use Workbench\Database\Seeders\DatabaseSeeder;

/*
|--------------------------------------------------------------------------
| Workbench Integration Test
|--------------------------------------------------------------------------
|
| Boots the CPQ package inside Orchestra Workbench with no host application:
| providers, migrations, the workbench seeder, the Inertia root view, and all
| three package routes (manifest / selector / quote store) are exercised.
|
*/

it('boots the package provider via testbench.yaml', function () {
    expect($this->app->getProviders(CpqServiceProvider::class))->not->toBeEmpty();
});

it('enforces the context morph map from the service provider', function () {
    expect(\Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel('context'))
        ->toBe(DemoContext::class);
});

it('runs the package migrations and workbench seeder', function () {
    $this->seed(DatabaseSeeder::class);

    $context = DemoContext::where('name', 'Workbench Demo Funeral Home')->first();

    expect($context)->not->toBeNull()
        ->and(CatalogOffering::where('owner_id', $context->id)->exists())->toBeTrue();
});

it('serves the JSON manifest endpoint', function () {
    $this->seed(DatabaseSeeder::class);
    $context = DemoContext::firstOrFail();

    $response = $this->getJson(route('cpq.manifest', ['context', $context->id]));

    $response->assertOk()
        ->assertJsonStructure(['offerings', 'presentation'])
        ->assertJsonPath('offerings.' . CatalogOffering::where('owner_id', $context->id)->first()->id . '.price_cents', 1500);
});

it('renders the selector Inertia page with the compiled manifest', function () {
    $this->seed(DatabaseSeeder::class);
    $context = DemoContext::firstOrFail();

    $this->get(route('cpq.selector', ['context', $context->id]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('CPQ/Selector')
            ->where('ownerType', 'context')
            ->where('ownerId', $context->id)
            ->has('manifest.offerings')
        );
});

it('returns the manifest as JSON when the client wants JSON', function () {
    $this->seed(DatabaseSeeder::class);
    $context = DemoContext::firstOrFail();

    $this->getJson(route('cpq.selector', ['context', $context->id]))
        ->assertOk()
        ->assertJsonStructure(['offerings', 'presentation']);
});

it('stores a quote draft through the save endpoint', function () {
    $context = DemoContext::create(['name' => 'Quote Save Context']);

    $response = $this->postJson(route('cpq.quotes.store'), [
        'owner_type' => 'context',
        'owner_id' => $context->id,
        'configurator_state' => ['lines' => [['offering' => 'x', 'qty' => 2]]],
    ]);

    $response->assertOk()->assertJsonPath('success', true);

    $quote = Quote::where('owner_id', $context->id)->first();
    expect($quote)->not->toBeNull()
        ->and($quote->status)->toBe('draft')
        ->and($quote->configurator_state)->toBe(['lines' => [['offering' => 'x', 'qty' => 2]]]);
});
