<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\ContactInterface;


class Contact implements ContactInterface
{
    public function __construct(private ?array $contact = null)
    {
    }


    public function getPhone(): string|array
    {
        return $this->contact['phone'] ?? '';
    }


    public function getEmail(): string|array
    {
        return $this->contact['email'] ?? '';
    }


    public function getFirstName(): string
    {
        return $this->contact['first_name'] ?? '';
    }


    public function getLastName(): string
    {
        return $this->contact['last_name'] ?? '';
    }


    public function setPhone(array|string $phone): self
    {
        $this->contact['phone'] = $phone;
        return $this;
    }


    public function setEmail(array|string $email): self
    {
        $this->contact['email'] = $email;
        return $this;
    }


    public function setFirstName(string $name): self
    {
        $this->contact['first_name'] = $name;
        return $this;
    }


    public function setLastName(string $name): self
    {
        $this->contact['last_name'] = $name;
        return $this;
    }
}