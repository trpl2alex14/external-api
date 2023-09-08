<?php

namespace ExternalApi\Contracts;


interface ResponseInterface
{
    public function getStatusCode(): int;
}