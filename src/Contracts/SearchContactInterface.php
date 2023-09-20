<?php

namespace ExternalApi\Contracts;


interface SearchContactInterface extends EntityInterface
{
    public function getPhone(): null|string|array;

    public function getEmail(): null|string|array;

    public function getFirstName(): ?string;

    public function getLastName(): ?string;
}