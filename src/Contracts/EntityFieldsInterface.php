<?php

namespace ExternalApi\Contracts;


interface EntityFieldsInterface
{
    public function getCode(string $name): string;
}