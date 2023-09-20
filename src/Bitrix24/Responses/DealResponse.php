<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Deal;


class DealResponse extends Response
{
    protected string $entityClass = Deal::class;

}