<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\LeadList;


class LeadListResponse extends Response
{
    protected string $entityClass = LeadList::class;

    public function getBody(): ?array
    {
        $response = parent::getBody()['result'] ?? [];

        $response = array_column(array_merge(...array_values($response)), null, 'ID');

        return array_values(array_filter($response, fn($item) => isset($item['ID'])));
    }
}