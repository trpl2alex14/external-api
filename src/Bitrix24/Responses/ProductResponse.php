<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Product;


class ProductResponse extends Response
{
    protected string $entityClass = Product::class;

}