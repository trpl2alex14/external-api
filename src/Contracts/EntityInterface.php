<?php

namespace ExternalApi\Contracts;


interface EntityInterface
{
    public function getField(string $name);
}