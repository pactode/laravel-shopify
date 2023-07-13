<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Pactode\Shopify\Factory;
use Pactode\Shopify\REST\Resources\ApiResource;

beforeEach(fn () => $this->shopify = Factory::fromConfig());

it('creates a webhook', function () {
    Http::fake([
        '*' => Http::response($this->fixture('webhooks.create')),
    ]);

    $resource = $this->shopify->createWebhook($payload = [
        'topic' => 'orders/create',
        'address' => 'https://whatever.hostname.com/',
        'format' => 'json',
    ]);

    Http::assertSent(function (Request $request) {
        expect($request->url())->toBe($this->shopify->getBaseUrl().'/webhooks.json');
        expect($request->method())->toBe('POST');

        return true;
    });
    expect($resource)->toBeInstanceOf(ApiResource::class);
});
