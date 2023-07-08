<?php

namespace Pactode\Shopify\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Pactode\Shopify\Http\Middleware\ValidateWebhook;
use Pactode\Shopify\Webhooks\Webhook;

class WebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware(ValidateWebhook::class);
    }

    public function handle(Request $request)
    {
        try {
            $webhook = Webhook::fromRequest($request);

            Event::dispatch($webhook->eventName(), $webhook);

            return new JsonResponse();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return new Response('Error handling webhook', 500);
        }
    }
}
