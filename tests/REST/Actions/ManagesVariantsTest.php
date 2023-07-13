<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Pactode\Shopify\Factory;
use Pactode\Shopify\REST\Resources\VariantResource;

beforeEach(function () {
    $this->shopify = Factory::fromConfig();
});

it('lists all variants for a product', function () {
    Http::fake([
        '*' => Http::response(['variants' => [
            ['id' => 1234, 'title' => 'Some title'],
            ['id' => 4321, 'title' => 'Some title 2'],
        ]]),
    ]);

    $resources = $this->shopify->getVariants(5432);

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/products/5432/variants.json', $request->url());
        $this->assertSame('GET', $request->method());

        return true;
    });
    $this->assertInstanceOf(Collection::class, $resources);
    $this->assertCount(2, $resources);
});

it('creates a variant', function () {
    Http::fake([
        '*' => Http::response(['variant' => ['id' => 1234, 'title' => 'Some title']]),
    ]);

    $resource = $this->shopify->createVariant(5432, $payload = [
        'sku' => '12345678',
    ]);

    Http::assertSent(function (Request $request) use ($payload) {
        $this->assertSame($this->shopify->getBaseUrl().'/products/5432/variants.json', $request->url());
        $this->assertEquals(['variant' => $payload], $request->data());
        $this->assertSame('POST', $request->method());

        return true;
    });
    $this->assertInstanceOf(VariantResource::class, $resource);
});

it('finds a variant', function () {
    Http::fake([
        '*' => Http::response(['variant' => ['sku' => 1234, 'title' => 'Some title']]),
    ]);

    $resource = $this->shopify->getVariant(1234);

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/variants/1234.json', $request->url());
        $this->assertSame('GET', $request->method());

        return true;
    });
    $this->assertInstanceOf(VariantResource::class, $resource);
});

it('updates a variant', function () {
    Http::fake([
        '*' => Http::response(['variant' => ['id' => 1234, 'sku' => '123456']]),
    ]);

    $resource = $this->shopify->updateVariant(1234, $payload = [
        'sku' => '123456',
    ]);

    Http::assertSent(function (Request $request) use ($payload) {
        $this->assertSame($this->shopify->getBaseUrl().'/variants/1234.json', $request->url());
        $this->assertEquals(['variant' => $payload], $request->data());
        $this->assertSame('PUT', $request->method());

        return true;
    });
    $this->assertInstanceOf(VariantResource::class, $resource);
});

it('deletes a variant', function () {
    Http::fake([
        '*' => Http::response(),
    ]);

    $this->shopify->deleteVariant(5432, 1234);

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/products/5432/variants/1234.json', $request->url());
        $this->assertSame('DELETE', $request->method());

        return true;
    });
});

it('counts variants', function () {
    Http::fake([
        '*' => Http::response(['count' => 125]),
    ]);

    $count = $this->shopify->getVariantsCount(5432);

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/products/5432/variants/count.json', $request->url());
        $this->assertSame('GET', $request->method());

        return true;
    });
    $this->assertEquals(125, $count);
});
