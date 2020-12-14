<?php

namespace Signifly\Shopify;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Signifly\Shopify\Http\Controllers\WebhookController;
use Signifly\Shopify\Webhooks\SecretProvider;
use Signifly\Shopify\Webhooks\Webhook;

class ShopifyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig($this->app);

        /*
         * @todo Perhaps allow for options allowing a user to modify aspects of the route...?
         */
        Route::macro('shopifyWebhooks', function () {
            return $this->post('shopify/webhooks', [WebhookController::class, 'handle']);
        });

        Request::macro('shopifyShopDomain', function () {
            return $this->header(Webhook::HEADER_SHOP_DOMAIN);
        });

        Request::macro('shopifyHmacSignature', function () {
            return $this->header(Webhook::HEADER_HMAC_SIGNATURE);
        });

        Request::macro('shopifyTopic', function () {
            return $this->header(Webhook::HEADER_TOPIC);
        });
    }

    /**
     * Setup the config.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    protected function setupConfig(Application $app)
    {
        $this->publishes([
            __DIR__.'/../config/shopify.php' => config_path('shopify.php'),
        ], 'laravel-shopify');

        $this->mergeConfigFrom(__DIR__.'/../config/shopify.php', 'shopify');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Shopify::class, function () {
            return Factory::fromConfig();
        });

        $this->app->alias(Shopify::class, 'shopify');

        $this->app->singleton(SecretProvider::class, function (Application $app) {
            $secretProvider = config('shopify.webhooks.secret_provider');

            return $app->make($secretProvider);
        });
    }
}
