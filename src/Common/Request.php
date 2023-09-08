<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\ApiRequestInterface;



class Request implements ApiRequestInterface
{
    protected string $response = Response::class;

    private array $parameters = [];

    public function __construct(array $parameters = null)
    {
        $this->parameters['method'] = $parameters['method'] ?? null;
        $this->parameters['query'] = $parameters['query'] ?? null;
        $this->parameters['headers'] = $parameters['headers'] ?? null;
        $this->parameters['data'] = $parameters['data'] ?? null;
    }


    public function getMethod(): string
    {
        return $this->parameters['method'] ?: '';
    }


    public function getQueryValues(): ?array
    {
        return $this->parameters['query'];
    }


    public function getHeaders(): ?array
    {
        return $this->parameters['headers'];
    }


    public function getData(): ?array
    {
        return $this->parameters['data'];
    }


    public function getResponseClassName(): string
    {
        return $this->response;
    }
}