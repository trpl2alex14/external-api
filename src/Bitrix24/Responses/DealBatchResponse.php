<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Deal;


class DealBatchResponse extends BatchResponse
{
    protected string $entityClass = Deal::class;

    public function getResult(): ?array
    {
        $response = parent::getResult()['deal.add'];

        return $response ? ['id' => $response] : null;
    }
}