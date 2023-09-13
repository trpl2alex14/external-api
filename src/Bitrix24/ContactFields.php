<?php

namespace ExternalApi\Bitrix24;

use ExternalApi\Common\EntityFields as EntityFieldsBase;


class ContactFields extends EntityFieldsBase
{
    protected array $fields = [
        'id' => 'ID',
        'first_name' => 'NAME',
        'last_name' => 'LAST_NAME',
        //todo
    ];
}