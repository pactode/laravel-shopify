<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Pactode\Shopify\Factory;
use Pactode\Shopify\REST\Resources\BalanceResource;
use Pactode\Shopify\REST\Resources\DisputeResource;
use Pactode\Shopify\REST\Resources\PayoutResource;
use Pactode\Shopify\REST\Resources\TransactionResource;

beforeEach(function () {
    $this->shopify = Factory::fromConfig();
});

test('it gets the balance', function () {
    Http::fake([
        '*' => Http::response($this->fixture('balance.show')),
    ]);

    $path = '/shopify_payments/balance.json';

    $resource = $this->shopify->getShopifyPaymentsBalance();

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().$path
        && $request->method() === 'GET'
    );

    expect($resource)->toBeInstanceOf(BalanceResource::class);
});

test('it gets a list of disputes', function () {
    Http::fake([
        '*' => Http::response($this->fixture('disputes.all')),
    ]);

    $path = '/shopify_payments/disputes.json';

    $resources = $this->shopify->getShopifyPaymentsDisputes();

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().$path
        && $request->method() === 'GET'
    );

    expect($resources)->toBeInstanceOf(Collection::class);
    expect($resources->first())->toBeInstanceOf(DisputeResource::class);
    expect($resources)->toHaveCount(7);
});

test('it finds a dispute', function () {
    Http::fake([
        '*' => Http::response($this->fixture('disputes.show')),
    ]);

    $disputeId = '1234';

    $resource = $this->shopify->getShopifyPaymentsDispute($disputeId);

    $path = '/shopify_payments/disputes/'.$disputeId.'.json';

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().$path
        && $request->method() === 'GET'
    );

    expect($resource)->toBeInstanceOf(DisputeResource::class);
});

test('it gets a list of payouts', function () {
    Http::fake([
        '*' => Http::response($this->fixture('payouts.all')),
    ]);

    $resources = $this->shopify->getShopifyPaymentsPayouts();

    $path = '/shopify_payments/payouts.json';

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().$path
        && $request->method() === 'GET'
    );

    expect($resources)->toBeInstanceOf(Collection::class);
    expect($resources->first())->toBeInstanceOf(PayoutResource::class);
    expect($resources)->toHaveCount(8);
});

test('it finds a payout', function () {
    Http::fake([
        '*' => Http::response($this->fixture('payouts.show')),
    ]);

    $payoutId = '1234';
    $path = '/shopify_payments/payouts/'.$payoutId.'.json';

    $resource = $this->shopify->getShopifyPaymentsPayout($payoutId);

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().$path
        && $request->method() === 'GET'
    );

    expect($resource)->toBeInstanceOf(PayoutResource::class);
});

test('it gets a list of transactions', function () {
    Http::fake([
        '*' => Http::response($this->fixture('transactions.all')),
    ]);

    $path = '/shopify_payments/balance/transactions.json';
    $resources = $this->shopify->getShopifyPaymentsBalanceTransactions();

    Http::assertSent(fn (Request $request) => $request->url() === $this->shopify->getBaseUrl().$path
        && $request->method() === 'GET'
    );

    expect($resources)->toBeInstanceOf(Collection::class);
    expect($resources->first())->toBeInstanceOf(TransactionResource::class);
    expect($resources)->toHaveCount(3);
});
