<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Contracts\EntityFieldsInterface;
use ExternalApi\Contracts\FilterInterface;


class Filter implements FilterInterface
{
    private array $filters = [];


    public function __construct(private EntityFieldsInterface $entityFields)
    {
    }


    public function contains(string $key, $value): FilterInterface
    {
        $code = '%'.$this->entityFields->getCode($key);
        $this->filters[$code] = $value;

        return $this;
    }


    public function equal(string $key, $value): FilterInterface
    {
        $code = $this->entityFields->getCode($key);
        $this->filters[$code] = $value;

        return $this;
    }


    public function getData(): array
    {
        return $this->filters;
    }
}