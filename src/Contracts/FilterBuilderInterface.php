<?php

namespace ExternalApi\Contracts;


interface FilterBuilderInterface extends RequestBuilderInterface
{
    public function select(...$fields): FilterBuilderInterface;

    public function where(callable $callable): FilterBuilderInterface;
}
