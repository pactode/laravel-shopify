<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Pactode\Shopify\Factory;
use Pactode\Shopify\REST\Resources\MetafieldResource;

$id = 1234;

beforeEach(function () {
    $this->shopify = Factory::fromConfig();
});

test('it creates a metafield', function (string $method, string $expectedUri, array $params = []) {
    Http::fakeSequence()->push($this->fixture('metafields.show'), 200);

    $payload = [
        'namespace' => 'pim',
        'key' => 'some_key',
        'value' => 'some value',
        'value_type' => 'string',
    ];

    array_push($params, $payload);

    $metafield = $this->shopify->$method(...$params);

    Http::assertSent(function (Request $request) use ($payload, $expectedUri) {
        expect($request->url())->toBe($this->shopify->getBaseUrl().$expectedUri);
        expect($request->method())->toBe('POST');
        expect($request->data())->toBe(['metafield' => $payload]);

        return true;
    });
    expect($metafield)->toBeInstanceOf(MetafieldResource::class);
})->with([
    ['createMetafield', '/metafields.json'],
    ['createCustomerMetafield', "/customers/{$id}/metafields.json", [$id]],
    ['createProductMetafield', "/products/{$id}/metafields.json", [$id]],
    ['createVariantMetafield', "/variants/{$id}/metafields.json", [$id]],
    ['createDraftOrderMetafield', "/draft_orders/{$id}/metafields.json", [$id]],
    ['createOrderMetafield', "/orders/{$id}/metafields.json", [$id]],
]);

test('it gets metafields count', function (string $method, string $expectedUri, array $params = []) {
    Http::fakeSequence()->push(['count' => 5], 200);

    $count = $this->shopify->$method(...$params);

    Http::assertSent(function (Request $request) use ($expectedUri) {
        expect($request->url())->toBe($this->shopify->getBaseUrl().$expectedUri);
        expect($request->method())->toBe('GET');

        return true;
    });
    expect($count)->toBe(5);
})->with([
    ['getMetafieldsCount', '/metafields/count.json'],
    ['getCustomerMetafieldsCount', "/customers/{$id}/metafields/count.json", [$id]],
    ['getProductMetafieldsCount', "/products/{$id}/metafields/count.json", [$id]],
    ['getVariantMetafieldsCount', "/variants/{$id}/metafields/count.json", [$id]],
    ['getDraftOrderMetafieldsCount', "/draft_orders/{$id}/metafields/count.json", [$id]],
    ['getOrderMetafieldsCount', "/orders/{$id}/metafields/count.json", [$id]],
]);

test('it gets metafields', function (string $method, string $expectedUri, array $params = []) {
    Http::fakeSequence()->push($this->fixture('metafields.all'), 200);

    $metafields = $this->shopify->$method(...$params);

    Http::assertSent(function (Request $request) use ($expectedUri) {
        expect($request->url())->toBe($this->shopify->getBaseUrl().$expectedUri);
        expect($request->method())->toBe('GET');

        return true;
    });
    expect($metafields->count())->toBe(1);
    expect($metafields)->toBeInstanceOf(Collection::class);
    expect($metafields->first())->toBeInstanceOf(MetafieldResource::class);
})->with([
    ['getMetafields', '/metafields.json'],
    ['getCustomerMetafields', "/customers/{$id}/metafields.json", [$id]],
    ['getProductMetafields', "/products/{$id}/metafields.json", [$id]],
    ['getVariantMetafields', "/variants/{$id}/metafields.json", [$id]],
    ['getDraftOrderMetafields', "/draft_orders/{$id}/metafields.json", [$id]],
    ['getOrderMetafields', "/orders/{$id}/metafields.json", [$id]],
]);

test('it updates a metafield', function () {
    Http::fakeSequence()->push($this->fixture('metafields.show'), 200);

    $metafield = $this->shopify->updateMetafield(1234, [
        'value' => 'new value',
    ]);

    Http::assertSent(function (Request $request) {
        expect($request->url())->toBe($this->shopify->getBaseUrl().'/metafields/1234.json');
        expect($request->method())->toBe('PUT');

        return true;
    });
    expect($metafield)->toBeInstanceOf(MetafieldResource::class);
});

test('it deletes a metafield', function () {
    Http::fakeSequence()->pushStatus(200);

    $this->shopify->deleteMetafield(1234);

    Http::assertSent(function (Request $request) {
        expect($request->url())->toBe($this->shopify->getBaseUrl().'/metafields/1234.json');
        expect($request->method())->toBe('DELETE');

        return true;
    });
});
