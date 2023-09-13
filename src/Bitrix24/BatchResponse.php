<?php

namespace ExternalApi\Bitrix24;


class BatchResponse extends Response
{

    public function getResult(): ?array
    {
        return parent::getResult()['result'] ?? null;
    }
}