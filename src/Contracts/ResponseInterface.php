<?php

namespace ExternalApi\Contracts;


interface ResponseInterface
{
    public function getStatusCode(): int;

    public function getHeaders();

    public function getBody();
}