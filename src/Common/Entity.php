<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\EntityFieldsInterface;
use ExternalApi\Contracts\EntityInterface;


class Entity implements EntityFieldsInterface, EntityInterface
{
    protected array $fieldCodes = [
        'id' => 'ID'
    ];

    protected array $fields = [];


    public function __construct(array $fields = [])
    {
        foreach ($fields as $key => $value) {
            $this->setField($key, $value, true);
        }
    }


    public function getRaw(): array
    {
        return $this->fields;
    }


    public function getCode(string $name): string
    {
        return $this->fieldCodes[$name] ?? $name;
    }


    public function getField(string $name, bool $callUserFunc = false): mixed
    {
        $method = $callUserFunc ? Helper::getMethodName($name) : '';
        if ($method && method_exists($this, $method)) {
            return call_user_func_array([$this, $method], []);
        }

        return $this->fields[$this->getCode($name)] ?? null;
    }


    public function setField(string $name, $value, bool $callUserFunc = false): self
    {
        $method = $callUserFunc ? Helper::getMethodName($name, 'set') : '';
        if ($method && method_exists($this, $method)) {
            return call_user_func_array([$this, $method], [$value]);
        }

        $this->fields[$this->getCode($name)] = $value;

        return $this;
    }


    public function __get(string $name)
    {
        return $this->getField($name, true);
    }


    public function __set(string $name, $value)
    {
        $this->setField($name, $value, true);
    }
}