<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Product;


class ProductIdResponse extends IdResponse
{
    protected string $entityClass = Product::class;
}