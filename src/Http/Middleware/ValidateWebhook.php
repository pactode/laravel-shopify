<?php

namespace Pactode\Shopify\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Pactode\Shopify\Webhooks\WebhookValidator;

class ValidateWebhook
{
    private WebhookValidator $webhookValidator;

    public function __construct(WebhookValidator $webhookValidator)
    {
        $this->webhookValidator = $webhookValidator;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->webhookValidator->validateFromRequest($request);

        return $next($request);
    }
}
