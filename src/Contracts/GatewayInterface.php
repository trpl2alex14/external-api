<?php

namespace ExternalApi\Contracts;


interface GatewayInterface
{
    public function getName(): string;

    public function createRequestBuilder(string $entity): RequestBuilderInterface;

    public function call(ApiRequestInterface|RequestBuilderInterface $request): ResponseInterface;

    public function createEntity(string $entity, ?array $args): EntityInterface;

    public function getWebhookEndpoint(): string;
}
