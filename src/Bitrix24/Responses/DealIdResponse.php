<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Deal;


class DealIdResponse extends IdResponse
{
    protected string $entityClass = Deal::class;
}