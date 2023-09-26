<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\EntityFieldsInterface;
use ExternalApi\Contracts\EntityInterface;


class Entity implements EntityFieldsInterface, EntityInterface
{
    protected array $fieldCodes = [
        'id' => 'ID'
    ];

    protected array $multiFields = [];

    protected array $fields = [];


    public function __construct(array $fields = [], ?array $settingFields = null)
    {
        if($settingFields){
            $this->setSettingFields($settingFields);
        }

        foreach ($fields as $key => $value) {
            $this->setField($key, $value, true);
        }
    }


    public function setSettingFields(array $settings): self
    {
        $this->fieldCodes = array_merge($this->fieldCodes, $settings);

        return $this;
    }


    public function getRaw(): array
    {
        return $this->fields;
    }


    public function getFields(): array
    {
        return $this->fields;
    }


    public function getCodeFields(): array
    {
        return $this->fieldCodes;
    }


    public function setFields(array $fields): self
    {
        foreach ($fields as $key => $value) {
            $this->setField($key, $value, true);
        }

        return $this;
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

        $value = $this->fields[$this->getCode($name)] ?? null;

        return in_array($name, $this->multiFields)
            ? $this->fromMultiField($value, $name)
            : $value;
    }


    protected function fromMultiField(mixed $value, ?string $name = null)
    {
        return $value;
    }


    public function setField(string $name, $value, bool $callUserFunc = false): self
    {
        $method = $callUserFunc ? Helper::getMethodName($name, 'set') : '';
        if ($method && method_exists($this, $method)) {
            return call_user_func_array([$this, $method], [$value]);
        }

        $this->fields[$this->getCode($name)] = in_array($name, $this->multiFields)
            ? $this->toMultiField($value, $name)
            : $value;

        return $this;
    }


    protected function toMultiField(mixed $value, ?string $name = null)
    {
        return $value;
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