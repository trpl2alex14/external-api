<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\ResponseInterface;
use GuzzleHttp\Psr7\Response as BaseResponse;


class Response implements ResponseInterface
{
    public function __construct(private BaseResponse $response)
    {
    }


    public function getStatusCode(): int
    {
        return $this->response?->getStatusCode() ?: 500;
    }
}