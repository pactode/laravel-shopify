<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

uses(Tests\TestCase::class);

it('registers shopify_webhooks macro on route', function () {
    expect(Route::hasMacro('shopifyWebhooks'))->toBeTrue();
});

it('registers shopify macros on request', function () {
    expect(Request::hasMacro('shopifyShopDomain'))->toBeTrue();
    expect(Request::hasMacro('shopifyHmacSignature'))->toBeTrue();
    expect(Request::hasMacro('shopifyTopic'))->toBeTrue();
});

it('registers endpoint when using shopify_webhooks macro', function () {
    Route::shopifyWebhooks();

    Route::getRoutes()->refreshNameLookups();

    expect(Route::has('shopify.webhooks'))->toBeTrue();
});
