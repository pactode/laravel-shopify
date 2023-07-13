<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Pactode\Shopify\Factory;
use Pactode\Shopify\REST\Cursor;
use Pactode\Shopify\REST\Resources\ProductResource;

beforeEach(function () {
    $this->shopify = Factory::fromConfig();
});

it('gets products', function () {
    Http::fake([
        '*' => Http::response($this->fixture('products.all')),
    ]);

    $resources = $this->shopify->getProducts();

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/products.json', $request->url());
        $this->assertSame('GET', $request->method());

        return true;
    });

    expect($resources)
        ->toBeInstanceOf(Collection::class)
        ->first()->toBeInstanceOf(ProductResource::class)
        ->count()->toBe(2);
});

it('creates a product', function () {
    Http::fake([
        '*' => Http::response($this->fixture('products.show')),
    ]);

    $resource = $this->shopify->createProduct($payload = [
        'title' => 'Test product',
        'handle' => 'test-product',
    ]);

    Http::assertSent(function (Request $request) use ($payload) {
        $this->assertSame($this->shopify->getBaseUrl().'/products.json', $request->url());
        $this->assertEquals(['product' => $payload], $request->data());
        $this->assertSame('POST', $request->method());

        return true;
    });
    expect($resource)->toBeInstanceOf(ProductResource::class);
});

it('finds a product', function () {
    Http::fake([
        '*' => Http::response($this->fixture('products.show')),
    ]);

    $resource = $this->shopify->getProduct($id = 1234);

    Http::assertSent(function (Request $request) use ($id) {
        $this->assertSame($this->shopify->getBaseUrl().'/products/'.$id.'.json', $request->url());
        $this->assertSame('GET', $request->method());

        return true;
    });
    expect($resource)->toBeInstanceOf(ProductResource::class);
});

it('updates a product', function () {
    Http::fake([
        '*' => Http::response($this->fixture('products.show')),
    ]);

    $id = 1234;

    $resource = $this->shopify->updateProduct($id, $payload = [
        'title' => 'Test product',
        'handle' => 'test-product',
    ]);

    Http::assertSent(function (Request $request) use ($id, $payload) {
        $this->assertSame($this->shopify->getBaseUrl().'/products/'.$id.'.json', $request->url());
        $this->assertEquals(['product' => $payload], $request->data());
        $this->assertSame('PUT', $request->method());

        return true;
    });
    expect($resource)->toBeInstanceOf(ProductResource::class);
});

it('deletes a product', function () {
    Http::fake([
        '*' => Http::response(),
    ]);

    $id = 1234;

    $this->shopify->deleteProduct($id);

    Http::assertSent(function (Request $request) use ($id) {
        $this->assertSame($this->shopify->getBaseUrl().'/products/'.$id.'.json', $request->url());
        $this->assertSame('DELETE', $request->method());

        return true;
    });
});

it('counts products', function () {
    Http::fake([
        '*' => Http::response(['count' => 125]),
    ]);

    $count = $this->shopify->getProductsCount();

    Http::assertSent(function (Request $request) {
        $this->assertSame($this->shopify->getBaseUrl().'/products/count.json', $request->url());
        $this->assertSame('GET', $request->method());

        return true;
    });
    $this->assertEquals(125, $count);
});

it('paginates products', function () {
    Http::fakeSequence()
        ->push(['count' => 4], 200)
        ->push($this->fixture('products.all'), 200, ['Link' => '<'.$this->shopify->getBaseUrl().'/products.json?page_info=1234&limit=2>; rel=next'])
        ->push($this->fixture('products.all'), 200);

    $count = $this->shopify->getProductsCount();
    $pages = $this->shopify->paginateProducts(['limit' => 2]);
    $results = collect();

    foreach ($pages as $page) {
        $results = $results->merge($page);
    }

    $this->assertInstanceOf(Cursor::class, $pages);
    $this->assertEquals($count, $results->count());

    Http::assertSequencesAreEmpty();
});
