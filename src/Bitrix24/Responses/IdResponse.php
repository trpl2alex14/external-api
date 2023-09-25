<?php

namespace ExternalApi\Bitrix24\Responses;


class IdResponse extends Response
{
    public function getBody(): ?array
    {
        $response = parent::getBody();

        return $response ? ['id' => $response] : null;
    }
}