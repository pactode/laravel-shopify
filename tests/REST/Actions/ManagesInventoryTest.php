<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Pactode\Shopify\Factory;
use Pactode\Shopify\REST\Resources\ApiResource;

beforeEach(function () {
    $this->shopify = Factory::fromConfig();
});

it('gets locations count', function () {
    Http::fakeSequence()->push($this->fixture('locations.count'));

    $count = $this->shopify->getLocationsCount();

    expect($count)->toBe(5);
    Http::assertSent(function (Request $request) {
        expect($request->url())->toBe($this->shopify->getBaseUrl().'/locations/count.json');
        expect($request->method())->toBe('GET');

        return true;
    });
});

it('gets locations', function () {
    Http::fakeSequence()->push($this->fixture('locations.all'));

    $locations = $this->shopify->getLocations(['limit' => 200]);

    Http::assertSent(function (Request $request) {
        expect($request->url())->toBe($this->shopify->getBaseUrl().'/locations.json?limit=200');
        expect($request->method())->toBe('GET');

        return true;
    });
    expect($locations)->toBeInstanceOf(Collection::class);
    expect($locations->first())->toBeInstanceOf(ApiResource::class);
    expect($locations->count())->toBe(5);
});

it('gets a location', function () {
    Http::fakeSequence()->push($this->fixture('locations.show'));

    $location = $this->shopify->getLocation(1234);

    Http::assertSent(function (Request $request) {
        expect($request->url())->toBe($this->shopify->getBaseUrl().'/locations/1234.json');
        expect($request->method())->toBe('GET');

        return true;
    });
    expect($location)->toBeInstanceOf(ApiResource::class);
});

it('gets inventory levels for a location', function () {
    Http::fakeSequence()->push($this->fixture('locations.inventoryLevels'));

    $inventoryLevels = $this->shopify->getLocationInventoryLevels(1234);

    Http::assertSent(function (Request $request) {
        expect($request->url())->toBe($this->shopify->getBaseUrl().'/locations/1234/inventory_levels.json');
        expect($request->method())->toBe('GET');

        return true;
    });
    expect($inventoryLevels)->toBeInstanceOf(Collection::class);
    expect($inventoryLevels->first())->toBeInstanceOf(ApiResource::class);
    expect($inventoryLevels->count())->toBe(4);
});
