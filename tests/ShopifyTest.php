<?php

use Pactode\Shopify\Shopify;

uses(Tests\TestCase::class);

it('returns the shopify instance from the container', function () {
    $shopify = $this->app->make('shopify');

    $this->assertInstanceOf(Shopify::class, $shopify);
});

it('returns the same shopify instance from the container', function () {
    $shopifyA = $this->app->make('shopify');
    $shopifyB = $this->app->make('shopify');

    $this->assertSame($shopifyA, $shopifyB);
});

it('memoizes the http client', function () {
    $shopify = $this->app->make('shopify');

    $clientA = $shopify->getHttpClient();
    $clientB = $shopify->getHttpClient();

    $this->assertSame($clientA, $clientB);
});

it('updates credentials and resets client', function () {
    $shopify = $this->app->make('shopify');

    $clientA = $shopify->getHttpClient();

    $shopify = $shopify->withCredentials('1234', '1234', '2021-01');

    $clientB = $shopify->getHttpClient();

    $this->assertNotSame($clientA, $clientB);
});
