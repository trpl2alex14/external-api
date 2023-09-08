<?php

namespace ExternalApi\Contracts;


interface ApiRequestInterface
{
    public function getMethod(): string;

    public function getQueryValues(): ?array;

    public function getHeaders(): ?array;

    public function getData(): ?array;
}
