<?php

namespace ExternalApi\Contracts;


/**
 * @method self select(string ...$fields)
 * @method self where(callable $callback)
 */
interface RequestBuilderInterface
{
    public function method($name): self;

    public function setParameters(?array $parameters): self;

    public function build();

    public function call(): ResponseInterface;
}
