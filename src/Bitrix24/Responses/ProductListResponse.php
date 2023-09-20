<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\ProductList;


class ProductListResponse extends Response
{
    protected string $entityClass = ProductList::class;
}