<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Common\Response as ResponseBase;


class Response extends ResponseBase
{
    public function getResult(): mixed
    {
        return $this->getBody();
    }


    public function getBody()
    {
        return parent::getBody()['result'] ?? null;
    }


    public function hasErrors(): bool
    {
        return !!$this->getErrorType();
    }


    public function getErrorType(): ?string
    {
        return $this->getBody()['error'] ?? null;
    }


    public function getErrorMessage(): ?string
    {
        return match ($this->getErrorType()) {
            'expired_token' => 'expired token, cant get new auth? Check access oauth server.',
            'invalid_token' => 'invalid token, need reinstall application',
            'invalid_grant' => 'invalid grant, check out define CLIENT_SECRET or CLIENT_ID',
            'invalid_client' => 'invalid client, check out define CLIENT_SECRET or CLIENT_ID',
            'QUERY_LIMIT_EXCEEDED' => 'Too many requests, maximum 2 query by second',
            'ERROR_METHOD_NOT_FOUND' => 'Method not found! You can see the permissions of the application: scope',
            'NO_AUTH_FOUND' => 'Some setup error b24',
            'INTERNAL_SERVER_ERROR' => 'Server down, try later',
            null => null,
            default => $this->getErrorType()
        };
    }
}