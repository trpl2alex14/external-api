<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\EntityFieldsInterface;


class EntityFields implements EntityFieldsInterface
{
    protected array $fields = [
        'id' => 'ID'
    ];


    public function getCode(string $name): string
    {
        return $this->fields[$name] ?? $name;
    }
}