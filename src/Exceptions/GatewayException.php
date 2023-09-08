<?php

namespace ExternalApi\Exceptions;

use Exception;


class GatewayException extends  Exception
{
    public function __construct(string $message)
    {
        parent::__construct('Gateway exception:'.$message);
    }


    public static function notSetEndPoint(): GatewayException
    {
        return new self('Webhook end point url not set');
    }


    public static function unknownRequestMethod(string $method): GatewayException
    {
        return new self("unknown request method ('$method')");
    }
}