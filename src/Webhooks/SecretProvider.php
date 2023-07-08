<?php

namespace Pactode\Shopify\Webhooks;

interface SecretProvider
{
    public function getSecret(string $domain): string;
}
