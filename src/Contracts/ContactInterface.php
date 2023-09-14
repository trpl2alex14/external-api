<?php

namespace ExternalApi\Contracts;


interface ContactInterface
{
    public function getPhone(): string|array;

    public function getEmail(): string|array;

    public function getFirstName(): string;

    public function getLastName(): string;
}