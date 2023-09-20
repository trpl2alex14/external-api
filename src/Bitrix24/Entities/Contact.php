<?php

namespace ExternalApi\Bitrix24\Entities;

use ExternalApi\Common\Entity;
use ExternalApi\Contracts\SearchContactInterface;


class Contact extends Entity implements SearchContactInterface
{
    protected array $fieldCodes = [
        'id' => 'ID',
        'first_name' => 'NAME',
        'last_name' => 'LAST_NAME',
        'second_name' => 'SECOND_NAME',
        'phone' => 'PHONE',
        'email' => 'EMAIL',
        'comments' => 'COMMENTS'
    ];

    protected array $multiFields = ['phone', 'email'];


    protected function fromMultiField(mixed $value, ?string $name = null)
    {
        if (is_array($value)) {
            $value = array_column($value, 'VALUE', 'ID');
        }

        return $value;
    }


    protected function toMultiField(mixed $value, ?string $name = null)
    {
        $value = is_string($value) ? [$value] : $value;

        return is_array($value) ? array_map(fn($item) => is_array($item) ? $item : ['VALUE' => $item], $value) : $value;
    }


    public function getPhone(): null|string|array
    {
        return $this->getField('phone', false);
    }


    public function setPhone(array|string $value): self
    {
        return $this->setField('phone', $value, false);
    }


    public function getEmail(): null|string|array
    {
        return $this->getField('email', false);
    }


    public function setEmail(array|string $value): self
    {
        return $this->setField('email', $value, false);
    }


    public function getFirstName(): ?string
    {
        return $this->getField('first_name');
    }


    public function getLastName(): ?string
    {
        return $this->getField('last_name');
    }
}