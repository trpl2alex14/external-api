<?php

namespace ExternalApi\Common;

use ExternalApi\Contracts\ResponseInterface;
use GuzzleHttp\Psr7\Response as BaseResponse;


class Response implements ResponseInterface
{
    protected ?array $body = null;


    public function __construct(protected BaseResponse $response)
    {
        $this->body = json_decode($this->response?->getBody() ?: '{}', true);
    }


    public function getRawResponse(): BaseResponse
    {
        return $this->response;
    }


    public function getStatusCode(): int
    {
        return $this->response?->getStatusCode() ?: 500;
    }


    public function getHeaders()
    {
        return $this->response?->getHeaders();
    }


    public function getBody()
    {
        return $this->body;
    }
}