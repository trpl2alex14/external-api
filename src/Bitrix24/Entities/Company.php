<?php

namespace ExternalApi\Bitrix24\Entities;

use ExternalApi\Common\Entity;


class Company extends Entity
{
    protected array $fieldCodes = [
        'id' => 'ID',
        'title' => 'TITLE',
        'comments' => 'COMMENTS',
    ];
}