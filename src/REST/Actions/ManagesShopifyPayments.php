<?php

namespace Pactode\Shopify\REST\Actions;

use Illuminate\Support\Collection;
use Pactode\Shopify\REST\Resources\BalanceResource;
use Pactode\Shopify\REST\Resources\DisputeResource;
use Pactode\Shopify\REST\Resources\PayoutResource;
use Pactode\Shopify\Shopify;

/** @mixin Shopify */
trait ManagesShopifyPayments
{
    public function getShopifyPaymentsBalance(): BalanceResource
    {
        $response = $this->get('shopify_payments/balance');

        return new BalanceResource($response['balance'], $this);
    }

    public function getShopifyPaymentsDisputes(array $params = []): Collection
    {
        return $this->getResources('disputes', $params, ['shopify_payments']);
    }

    public function getShopifyPaymentsDispute($disputeId): DisputeResource
    {
        return $this->getResource('disputes', $disputeId, ['shopify_payments']);
    }

    public function getShopifyPaymentsPayouts(array $params = []): Collection
    {
        return $this->getResources('payouts', $params, ['shopify_payments']);
    }

    public function getShopifyPaymentsPayout($payoutId): PayoutResource
    {
        return $this->getResource('payouts', $payoutId, ['shopify_payments']);
    }

    public function getShopifyPaymentsBalanceTransactions(array $params = []): Collection
    {
        return $this->getResources('transactions', $params, ['shopify_payments', 'balance']);
    }
}
