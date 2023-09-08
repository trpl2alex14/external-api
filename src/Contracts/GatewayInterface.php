<?php

namespace ExternalApi\Contracts;

use Psr\Http\Message\ResponseInterface;


interface GatewayInterface
{
    public function getName(): string;

    /**
     * @param string $entity
     * @return RequestBuilderInterface
     */
    public function requestBuilder(string $entity): RequestBuilderInterface;

    public function call(ApiRequestInterface $request): ResponseInterface;

    public function getWebhookEndpoint(): string;
}
