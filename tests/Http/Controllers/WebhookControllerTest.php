<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Pactode\Shopify\Exceptions\WebhookFailed;
use Pactode\Shopify\Webhooks\SecretProvider;
use Pactode\Shopify\Webhooks\Webhook;
use Pactode\Shopify\Webhooks\WebhookValidator;

beforeEach(function () {
    Route::shopifyWebhooks();
    Event::fake();
});

it('is missing a signature', function () {
    $response = $this->postJson(getUrl());

    $response->assertStatus(400);
    $response->assertSee('The request did not contain a header named `X-Shopify-Hmac-Sha256`.');
});

it('dispatches an event relative to topic name', function () {
    $signature = app(WebhookValidator::class)->calculateSignature('[]', config('shopify.webhooks.secret'));

    $response = $this->postJson(getUrl(), [], getValidHeaders([
        Webhook::HEADER_HMAC_SIGNATURE => $signature,
    ]));

    $response->assertOk();
    Event::assertDispatched('shopify-webhooks.orders-create');
});

it('throws an exception when hmac header is missing', function () {
    $this->withoutExceptionHandling();

    $this->expectExceptionObject(WebhookFailed::missingSignature());

    $this->postJson(getUrl());
});

it('throws an exception with an empty webhook secret', function () {
    $this->withoutExceptionHandling();

    $this->mock(SecretProvider::class)
        ->shouldReceive('getSecret')
        ->andReturn('')
        ->once();

    $this->expectExceptionObject(WebhookFailed::missingSigningSecret());

    $this->postJson(getUrl(), [], getValidHeaders());
});

it('throws an exception with invalid signature', function () {
    $this->withoutExceptionHandling();

    $this->expectExceptionObject(WebhookFailed::invalidSignature($signature = 'hmac'));

    $this->postJson(getUrl(), [], getValidHeaders([
        Webhook::HEADER_HMAC_SIGNATURE => $signature,
    ]));
});

function getUrl(): string
{
    return route('shopify.webhooks');
}

function getValidHeaders(array $overwrites = []): array
{
    return array_merge([
        Webhook::HEADER_SHOP_DOMAIN => 'test.myshopify.com',
        Webhook::HEADER_HMAC_SIGNATURE => 'hmac',
        Webhook::HEADER_TOPIC => 'orders/create',
    ], $overwrites);
}
