<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\ApiRequestInterface;
use ExternalApi\Contracts\GatewayInterface;
use ExternalApi\Contracts\RequestBuilderInterface;
use ExternalApi\Contracts\ResponseInterface;
use ExternalApi\Exceptions\BuilderException;


class Builder implements RequestBuilderInterface
{
    protected ?GatewayInterface $gateway;

    protected string $method;

    protected array $methods = [];

    protected ?array $parameters = [];

    protected ?array $query = null;

    protected ?array $headers = null;

    protected ?string $response = null;

    protected string $entityClass = Entity::class;

    private Entity $entity;

    protected array $requiredParametersForMethod = [];


    public function __construct()
    {
        if (class_exists($this->entityClass)) {
            $this->entity = new $this->entityClass();
        }

        $this->initialization();
    }


    protected function initialization()
    {
        //
    }


    public function getEntity(): Entity
    {
        return $this->entity;
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
        return $this->parameters['id'] ?? null;
    }

    /**
     * @throws BuilderException
     */
    public function build(): ApiRequestInterface
    {
        $this->validate();

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


    /**
     * @throws BuilderException
     */
    protected function validate()
    {
        if (empty($this->requiredParametersForMethod)) {
            return;
        }

        $requiredParameters = $this->requiredParametersForMethod[$this->method] ?? null;

        if (empty($requiredParameters)) {
            return;
        }

        $requiredParameters = is_string($requiredParameters) ? [$requiredParameters] : $requiredParameters;

        foreach ($requiredParameters as $parameter) {
            $parameters = explode('|', $parameter);

            $emptyParameters = array_filter(
                array_map(
                    fn($parameter) => is_null($this->getParameter($parameter)) ? $parameter : null,
                    $parameters
                )
            );

            if (!empty($emptyParameters)) {
                throw BuilderException::requiredParameters(implode(',', $emptyParameters));
            }
        }
    }


    public function getData(): array
    {
        return $this->parameters;
    }


    public function call(): ResponseInterface
    {
        return $this->gateway->call($this);
    }


}