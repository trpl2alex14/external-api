<?php

namespace ExternalApi\Bitrix24\Entities;

use ExternalApi\Common\Entity;


class ProductRow extends Entity
{
    protected array $fieldCodes = [
        'id' => 'PRODUCT_ID',
        'name' => 'PRODUCT_NAME',
        'price' => 'PRICE',
        'quantity' => 'QUANTITY',
        'discount' => 'DISCOUNT_SUM',
        'tax' => 'TAX_RATE',
        'tax_included' => 'TAX_INCLUDED',
        'measure_code' => 'MEASURE_CODE',
    ];

    protected array $fields = [
        'MEASURE_CODE' => 796,
        'TAX_INCLUDED' => 'Y',
    ];
}