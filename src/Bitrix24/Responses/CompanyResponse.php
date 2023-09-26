<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Company;


class CompanyResponse extends Response
{
    protected string $entityClass = Company::class;

}