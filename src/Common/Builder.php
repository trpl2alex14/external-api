<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\ApiRequestInterface;
use ExternalApi\Contracts\EntityFieldsInterface;
use ExternalApi\Contracts\GatewayInterface;
use ExternalApi\Contracts\RequestBuilderInterface;


class Builder implements RequestBuilderInterface
{
    protected ?GatewayInterface $gateway;

    protected string $method;

    protected array $methods = [];

    protected ?array $parameters = [];

    protected ?array $query = null;

    protected ?array $headers = null;

    protected ?string $response = null;

    protected string $entityFieldsClass = EntityFields::class;

    private EntityFieldsInterface $entityFields;


    public function __construct()
    {
        if(class_exists($this->entityFieldsClass)) {
            $this->entityFields = new $this->entityFieldsClass();
        }

        $this->initialization();
    }


    protected function initialization()
    {
        //
    }


    public function getEntityFields(): EntityFieldsInterface
    {
        return $this->entityFields;
    }


    public function setGateway(GatewayInterface $gateway): self
    {
        $this->gateway = $gateway;

        return $this;
    }


    public function method($name): self
    {
        $this->method = $this->methods[$name] ?? $name;

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


    public function setParameter(string $key, mixed $value): self
    {
        $this->parameters[$key] = $value;

        return $this;
    }


    public function getParameter(string $key): mixed
    {
        return $this->parameters[$key] ?? null;
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


    public function getFields(): ?array
    {
        return $this->parameters['fields'];
    }


    public function setId(int $id): self
    {
        $this->parameters['id'] = $id;

        return $this;
    }


    public function getId(): ?int
    {
        return $this->parameters['id'];
    }


    public function build(): ApiRequestInterface
    {
        $data = $this->getData();

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


    public function getData(): array
    {
        return $this->parameters;
    }
}