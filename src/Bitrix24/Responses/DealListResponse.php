<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\DealList;


class DealListResponse extends Response
{
    protected string $entityClass = DealList::class;
}