<?php

namespace ExternalApi\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Throwable;


class CouldNotCallApi extends Exception
{
    private ?ResponseInterface $response;


    public function __construct(string $message, ResponseInterface $response = null, int $code = null)
    {
        $this->response = $response;
        $this->message = $message;
        $this->code = $code ?? $response?->getStatusCode() ?? null;

        parent::__construct($message, $code);
    }


    public static function serviceRespondedWithAnError(ResponseInterface $response): CouldNotCallApi
    {
        return new self(
            sprintf('Webhook responded with an error: `%s`', $response->getBody()->getContents()),
            $response
        );
    }


    public static function serviceRespondedException(Throwable $e = null): CouldNotCallApi
    {
        return new self(
            sprintf('Call webhook responded exception: `%s`', $e->getMessage()),
        );
    }


    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}