<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Lead;


class LeadResponse extends Response
{
    protected string $entityClass = Lead::class;

}