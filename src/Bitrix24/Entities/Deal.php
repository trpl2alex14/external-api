<?php

namespace ExternalApi\Bitrix24\Entities;

use ExternalApi\Common\Entity;


class Deal extends Entity
{
    protected array $fieldCodes = [
        'id' => 'ID',
        'title' => 'TITLE',
        'company_id' => 'COMPANY_ID',
        'comments' => 'COMMENTS',
        'currency' => 'CURRENCY_ID',
        'source_id' => 'SOURCE_ID',
        'stage_id' => 'STAGE_ID',
        'category_id' => 'CATEGORY_ID',
    ];

    protected array $multiFields = [];
}