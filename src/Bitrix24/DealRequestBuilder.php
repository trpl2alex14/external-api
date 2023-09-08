<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Common\Builder;
use ExternalApi\Contracts\GatewayInterface;


class DealRequestBuilder extends Builder
{
    private array $methods = [
        'create' => 'crm.deal.add',
        'update' => 'crm.deal.update',
    ];


    public function __construct(protected GatewayInterface $gateway)
    {
        parent::__construct($this->gateway);

        $this->parameters = [
            'params' => [
                'REGISTER_SONET_EVENT' => 'Y'
            ]
        ];
    }


    public function method($name): self
    {
        $this->method = $this->methods[$name] ?? $name;

        return $this;
    }


    public function setItems(array $items): self
    {

        // TODO: Implement setFields() method.
        return $this;
    }


    public function setClients(array $clients): self
    {
        // TODO: Implement setFields() method.
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


    protected function getData(): array
    {
        return $this->parameters;
    }
}