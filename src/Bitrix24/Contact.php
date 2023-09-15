<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Common\Entity;
use ExternalApi\Contracts\SearchContactInterface;


class Contact extends Entity implements SearchContactInterface
{
    protected array $fieldCodes = [
        'id' => 'ID',
        'first_name' => 'NAME',
        'last_name' => 'LAST_NAME',
        'phone' => "PHONE",
        'email' => "EMAIL",
        //todo
    ];

    public function getPhone(): string|array
    {
        $value = $this->getField('phone', false);

        if (is_array($value)) {
            $value = array_column($value, 'VALUE');
        }

        return $value;
    }


    public function setPhone(array|string $value): self
    {
        $value = is_string($value) ? [$value] : $value;
        $value = array_map(fn($item) => ['VALUE' => $item], $value);

        return $this->setField('phone', $value, false);
    }


    public function getEmail(): string|array
    {
        $value = $this->getField('email');

        if (is_array($value)) {
            $value = array_column($value, 'VALUE');
        }

        return $value;
    }


    public function setEmail(array|string $value): self
    {
        $value = is_string($value) ? [$value] : $value;
        $value = array_map(fn($item) => ['VALUE' => $item], $value);

        return $this->setField('email', $value);
    }


    public function getFirstName(): string
    {
        return $this->getField('first_name');
    }


    public function getLastName(): string
    {
        return $this->getField('last_name');
    }
}