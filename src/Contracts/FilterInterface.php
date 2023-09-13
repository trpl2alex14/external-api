<?php

namespace ExternalApi\Contracts;


interface FilterInterface
{
    public function contains(string $key, $value): self;

    public function equal(string $key, $value): self;

    public function getData(): array;
}