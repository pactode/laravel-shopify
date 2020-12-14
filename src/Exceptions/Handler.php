<?php

namespace Signifly\Shopify\Exceptions;

use Illuminate\Http\Client\Response;

class Handler
{
    public function handle(Response $response)
    {
        if ($response->successful()) {
            return;
        }

        if ($response->status() === 429) {
            throw new TooManyRequestsException();
        }

        if ($response->status() === 422) {
            throw new ValidationException($response->json('errors', []));
        }

        if ($response->status() === 404) {
            throw new NotFoundException();
        }

        $response->throw();
    }
}
