<?php

namespace Pactode\Shopify;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Pactode\Shopify\REST\Actions\ManagesAccess;
use Pactode\Shopify\REST\Actions\ManagesAnalytics;
use Pactode\Shopify\REST\Actions\ManagesBilling;
use Pactode\Shopify\REST\Actions\ManagesCollections;
use Pactode\Shopify\REST\Actions\ManagesCustomers;
use Pactode\Shopify\REST\Actions\ManagesDiscounts;
use Pactode\Shopify\REST\Actions\ManagesEvents;
use Pactode\Shopify\REST\Actions\ManagesFulfillments;
use Pactode\Shopify\REST\Actions\ManagesInventory;
use Pactode\Shopify\REST\Actions\ManagesMarketingEvents;
use Pactode\Shopify\REST\Actions\ManagesMetafields;
use Pactode\Shopify\REST\Actions\ManagesOnlineStore;
use Pactode\Shopify\REST\Actions\ManagesOrders;
use Pactode\Shopify\REST\Actions\ManagesPlus;
use Pactode\Shopify\REST\Actions\ManagesProducts;
use Pactode\Shopify\REST\Actions\ManagesSalesChannel;
use Pactode\Shopify\REST\Actions\ManagesShopifyPayments;
use Pactode\Shopify\REST\Actions\ManagesStoreProperties;
use Pactode\Shopify\REST\Cursor;
use Pactode\Shopify\Support\MakesHttpRequests;
use Pactode\Shopify\Support\TransformsResources;

class Shopify
{
    use MakesHttpRequests;
    use ManagesAccess;
    use ManagesAnalytics;
    use ManagesBilling;
    use ManagesCollections;
    use ManagesCustomers;
    use ManagesDiscounts;
    use ManagesEvents;
    use ManagesFulfillments;
    use ManagesInventory;
    use ManagesMarketingEvents;
    use ManagesMetafields;
    use ManagesOnlineStore;
    use ManagesOrders;
    use ManagesPlus;
    use ManagesProducts;
    use ManagesSalesChannel;
    use ManagesShopifyPayments;
    use ManagesStoreProperties;
    use TransformsResources;

    protected string $accessToken;

    protected string $domain;

    protected string $apiVersion;

    protected ?PendingRequest $httpClient = null;

    public function __construct(string $accessToken, string $domain, string $apiVersion)
    {
        $this->withCredentials($accessToken, $domain, $apiVersion);
    }

    public function cursor(Collection $results): Cursor
    {
        return new Cursor($this, $results);
    }

    public function getHttpClient(): PendingRequest
    {
        return $this->httpClient ??= Http::baseUrl($this->getBaseUrl())
            ->withHeaders(['X-Shopify-Access-Token' => $this->accessToken]);
    }

    public function graphQl(): PendingRequest
    {
        return Http::baseUrl("https://{$this->domain}/admin/api/{$this->apiVersion}/graphql.json")
            ->withHeaders(['X-Shopify-Access-Token' => $this->accessToken]);
    }

    public function getBaseUrl(): string
    {
        return "https://{$this->domain}/admin/api/{$this->apiVersion}";
    }

    public function tap(callable $callback): self
    {
        $callback($this->getHttpClient());

        return $this;
    }

    public function withCredentials(string $accessToken, string $domain, string $apiVersion): self
    {
        $this->accessToken = $accessToken;
        $this->domain = $domain;
        $this->apiVersion = $apiVersion;

        $this->httpClient = null;

        return $this;
    }
}
