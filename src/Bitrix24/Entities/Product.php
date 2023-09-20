<?php

namespace ExternalApi\Bitrix24\Entities;

use ExternalApi\Common\Entity;


class Product extends Entity
{
    protected array $fieldCodes = [
        'id' => 'ID',
        'name' => 'NAME',
        'price' => 'PRICE',
        'comments' => 'COMMENTS',
        'currency' => 'CURRENCY_ID',
        'measure_code' => 'MEASURE',
        'section_id' => 'SECTION_ID'
    ];

    public function getArticle(): null|string|array
    {
        $value = $this->getField('article', false);

        return is_array($value) ? $value['value'] : $value;
    }
}