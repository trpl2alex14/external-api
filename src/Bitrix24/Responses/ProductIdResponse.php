<?php

namespace ExternalApi\Bitrix24\Responses;

use ExternalApi\Bitrix24\Entities\Product;


class ProductIdResponse extends Response
{
    protected string $entityClass = Product::class;


    public function getResult(): ?array
    {
        $response = parent::getResult();

        return $response ? ['id' => $response] : null;
    }
}