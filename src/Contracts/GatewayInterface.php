<?php

namespace ExternalApi\Contracts;

use ExternalApi\Contracts\ResponseInterface;


interface GatewayInterface
{
    public function getName(): string;

    /**
     * @param string $entity
     * @return RequestBuilderInterface
     */
    public function createRequestBuilder(string $entity): RequestBuilderInterface;

    public function call(ApiRequestInterface $request): ResponseInterface;

    public function getWebhookEndpoint(): string;
}
