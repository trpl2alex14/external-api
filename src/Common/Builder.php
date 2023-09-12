<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\ApiRequestInterface;
use ExternalApi\Contracts\GatewayInterface;
use ExternalApi\Contracts\RequestBuilderInterface;


class Builder implements RequestBuilderInterface
{
    protected ?GatewayInterface $gateway;

    protected string $method;

    protected ?array $parameters = [];

    protected ?array $query = null;

    protected ?array $headers = null;

    protected ?string $response = null;


    public function __construct()
    {
    }


    public function setGateway(GatewayInterface $gateway): self
    {
        $this->gateway = $gateway;

        return $this;
    }


    public function method($name): self
    {
        $this->method = $name;

        return $this;
    }


    public function getMethod(): string
    {
        return $this->method;
    }


    public function setResponse(string $response): self
    {
        if (is_subclass_of($response, Response::class, true)) {
            $this->response = $response;
        }

        return $this;
    }


    public function setParameters(?array $parameters): self
    {
        $this->parameters = is_null($parameters) ? [] : array_merge($this->parameters, $parameters);

        return $this;
    }


    public function setQuery(?array $query): self
    {
        $this->query = $query;

        return $this;
    }


    public function setHeaders(?array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }


    public function setFields(array $fields): self
    {
        $this->parameters['fields'] = $fields;

        return $this;
    }


    public function setId(int $id): self
    {
        $this->parameters['id'] = $id;

        return $this;
    }


    public function build(): ApiRequestInterface
    {
        $data = $this->getData();

        $this->parameters = [];

        return new Request(
            [
                'method' => $this->method,
                'data' => $data,
                'query' => $this->query,
                'headers' => $this->headers
            ],
            $this->response
        );
    }


    protected function getData(): array
    {
        return $this->parameters;
    }
}