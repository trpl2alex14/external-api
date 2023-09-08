<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\ApiRequestInterface;
use ExternalApi\Contracts\GatewayInterface;
use ExternalApi\Contracts\RequestBuilderInterface;


abstract class Builder implements RequestBuilderInterface
{
    protected string $method;

    protected array $parameters = [];


    public function __construct(protected GatewayInterface $gateway)
    {
    }


    public function method($name): self
    {
        $this->method = $name;

        return $this;
    }


    public function setParameters(array $parameters): self
    {
        $this->parameters = array_merge($this->parameters, $parameters);

        return $this;
    }


    public function build(): ApiRequestInterface
    {
        $data = $this->getData();

        $this->parameters = [];

        return [
            'method' => $this->method,
            'params' => $data
        ];
    }


    protected function getData(): array
    {
        return $this->parameters;
    }
}