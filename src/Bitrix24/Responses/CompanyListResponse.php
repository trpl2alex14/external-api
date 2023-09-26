<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\CompanyList;


class CompanyListResponse extends Response
{
    protected string $entityClass = CompanyList::class;

}