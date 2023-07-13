<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Pactode\Shopify\Factory;
use Pactode\Shopify\REST\Resources\ApiResource;

beforeEach(function () {
    $this->shopify = Factory::fromConfig();
});

it('gets discount codes', function () {
    Http::fake([
        '*' => Http::response($this->fixture('discountCodes.all')),
    ]);

    $resources = $this->shopify->getDiscountCodes(1234);

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/price_rules/1234/discount_codes.json', $request->url());
        $this->assertSame('GET', $request->method());

        return true;
    });
    $this->assertInstanceOf(Collection::class, $resources);
    $this->assertInstanceOf(ApiResource::class, $resources->first());
    $this->assertCount(1, $resources);
});

it('creates a discount code', function () {
    Http::fake([
        '*' => Http::response($this->fixture('discountCodes.show')),
    ]);

    $resource = $this->shopify->createDiscountCode(1234, 'DISCOUNT20');

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/price_rules/1234/discount_codes.json', $request->url());
        $this->assertEquals(['discount_code' => ['code' => 'DISCOUNT20']], $request->data());
        $this->assertSame('POST', $request->method());

        return true;
    });
    $this->assertInstanceOf(ApiResource::class, $resource);
});

it('finds a discount code', function () {
    Http::fake([
        '*' => Http::response($this->fixture('discountCodes.show')),
    ]);

    $resource = $this->shopify->getDiscountCode(1234, 5678);

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/price_rules/1234/discount_codes/5678.json', $request->url());
        $this->assertSame('GET', $request->method());

        return true;
    });
    $this->assertInstanceOf(ApiResource::class, $resource);
});

it('updates a discount code', function () {
    Http::fake([
        '*' => Http::response($this->fixture('discountCodes.show')),
    ]);

    $resource = $this->shopify->updateDiscountCode(1234, 5678, '20OFF');

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/price_rules/1234/discount_codes/5678.json', $request->url());
        $this->assertEquals(['discount_code' => ['code' => '20OFF']], $request->data());
        $this->assertSame('PUT', $request->method());

        return true;
    });
    $this->assertInstanceOf(ApiResource::class, $resource);
});

it('deletes a discount code', function () {
    Http::fake([
        '*' => Http::response(),
    ]);

    $this->shopify->deleteDiscountCode(1234, 5678);

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/price_rules/1234/discount_codes/5678.json', $request->url());
        $this->assertSame('DELETE', $request->method());

        return true;
    });
});

it('counts discount codes', function () {
    Http::fake([
        '*' => Http::response(['count' => 125]),
    ]);

    $count = $this->shopify->getDiscountCodesCount();

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/discount_codes/count.json', $request->url());
        $this->assertSame('GET', $request->method());

        return true;
    });
    $this->assertEquals(125, $count);
});

it('gets price rules', function () {
    Http::fake([
        '*' => Http::response($this->fixture('priceRules.all')),
    ]);

    $resources = $this->shopify->getPriceRules();

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/price_rules.json', $request->url());
        $this->assertSame('GET', $request->method());

        return true;
    });
    $this->assertInstanceOf(Collection::class, $resources);
    $this->assertInstanceOf(ApiResource::class, $resources->first());
    $this->assertCount(2, $resources);
});

it('creates a price rule', function () {
    Http::fake([
        '*' => Http::response($this->fixture('priceRules.show')),
    ]);

    $resource = $this->shopify->createPriceRule($payload = [
        'title' => 'SUMMERSALE10OFF',
        'target_type' => 'line_item',
        'target_selection' => 'all',
        'allocation_method' => 'across',
        'value_type' => 'fixed_amount',
        'value' => '-10.0',
        'customer_selection' => 'all',
        'starts_at' => '2017-01-19T17:59:10Z',
    ]);

    Http::assertSent(function (Request $request) use ($payload) {
        $this->assertSame($this->shopify->getBaseUrl().'/price_rules.json', $request->url());
        $this->assertEquals(['price_rule' => $payload], $request->data());
        $this->assertSame('POST', $request->method());

        return true;
    });
    $this->assertInstanceOf(ApiResource::class, $resource);
});

it('finds a price rule', function () {
    Http::fake([
        '*' => Http::response($this->fixture('priceRules.show')),
    ]);

    $resource = $this->shopify->getPriceRule(1234);

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/price_rules/1234.json', $request->url());
        $this->assertSame('GET', $request->method());

        return true;
    });
    $this->assertInstanceOf(ApiResource::class, $resource);
});

it('updates a price rule', function () {
    Http::fake([
        '*' => Http::response($this->fixture('priceRules.show')),
    ]);

    $resource = $this->shopify->updatePriceRule(1234, [
        'title' => 'SUMMERSALE10POFF',
    ]);

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/price_rules/1234.json', $request->url());
        $this->assertEquals(['price_rule' => ['title' => 'SUMMERSALE10POFF']], $request->data());
        $this->assertSame('PUT', $request->method());

        return true;
    });
    $this->assertInstanceOf(ApiResource::class, $resource);
});

it('deletes a price rule', function () {
    Http::fake([
        '*' => Http::response(),
    ]);

    $this->shopify->deletePriceRule(1234);

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/price_rules/1234.json', $request->url());
        $this->assertSame('DELETE', $request->method());

        return true;
    });
});

it('counts price rules', function () {
    Http::fake([
        '*' => Http::response(['count' => 125]),
    ]);

    $count = $this->shopify->getPriceRulesCount();

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/price_rules/count.json', $request->url());
        $this->assertSame('GET', $request->method());

        return true;
    });
    $this->assertEquals(125, $count);
});
