<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Deal;


class DealIdResponse extends Response
{
    protected string $entityClass = Deal::class;


    public function getResult(): ?array
    {
        $response = parent::getResult();

        return $response ? ['id' => $response] : null;
    }
}