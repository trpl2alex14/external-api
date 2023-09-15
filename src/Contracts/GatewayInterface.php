<?php

namespace ExternalApi\Contracts;


interface GatewayInterface
{
    public function getName(): string;

    /**
     * @param string $entity
     * @return RequestBuilderInterface
     */
    public function createRequestBuilder(string $entity): RequestBuilderInterface;

    public function call(ApiRequestInterface $request): ResponseInterface;

    public function createEntity(string $entity, ...$args): EntityInterface;

    public function getWebhookEndpoint(): string;
}
