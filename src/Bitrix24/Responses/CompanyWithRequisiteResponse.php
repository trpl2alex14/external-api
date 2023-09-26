<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Company;


class CompanyWithRequisiteResponse extends BatchWithRequisiteResponse
{
    protected string $entityClass = Company::class;
}