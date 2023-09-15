<?php

namespace ExternalApi\Contracts;


interface SearchContactInterface extends EntityInterface
{
    public function getPhone(): string|array;

    public function getEmail(): string|array;

    public function getFirstName(): string;

    public function getLastName(): string;
}