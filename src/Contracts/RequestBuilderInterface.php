<?php

namespace ExternalApi\Contracts;


interface RequestBuilderInterface
{
    public function method($name): self;

    public function setParameters(array $parameters): self;

    public function build();
}
