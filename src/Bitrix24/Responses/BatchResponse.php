<?php

namespace ExternalApi\Bitrix24\Responses;


class BatchResponse extends Response
{
    public function getBody()
    {
        return parent::getBody()['result'] ?? null;
    }
}