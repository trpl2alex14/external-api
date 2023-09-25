<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Deal;


class DealBatchResponse extends BatchResponse
{
    protected string $entityClass = Deal::class;

    public function getBody(): ?array
    {
        $response = parent::getBody()['deal.add'];

        return $response ? ['id' => $response] : null;
    }
}